<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Regression test for the production 400 captured on 2026-07-06:
 * "The specified schema produces a constraint that has too many states for
 * serving" — Gemini rejects response_schemas that combine nested array
 * length limits with bounded integers / maxLength strings. All of those
 * bounds are already enforced app-side by InterviewFeedbackResponse, so the
 * provider schema must stay structural only (types, required, enum).
 */
class InterviewFeedbackSchemaCompatTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'test-api-key']);
    }

    private function makeCompletedSetup(): array
    {
        $template = CvTemplate::create([
            'id'         => Str::ulid(),
            'name'       => 'Test Template',
            'blade_path' => 'templates.default',
            'is_active'  => true,
            'is_premium' => false,
        ]);

        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);

        $cv = Cv::forceCreate([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Test CV',
            'job_target'  => 'Backend Engineer',
        ]);

        CvSection::forceCreate([
            'id'      => Str::ulid(),
            'cv_id'   => $cv->id,
            'type'    => 'target_job',
            'title'   => 'Target Job',
            'order'   => 0,
            'content' => ['job_title' => 'Backend Engineer'],
        ]);

        $session = InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => 'active',
            'started_at' => now(),
        ]);

        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Tell me about a project you led.',
        ]);
        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => 'I led a data migration for a retail client.',
        ]);

        return [$user, $session];
    }

    public function test_feedback_response_schema_avoids_constraint_state_explosion(): void
    {
        [$user, $session] = $this->makeCompletedSetup();

        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => json_encode([
                        'overall_score'    => 75,
                        'readiness_badge'  => 'ready',
                        'missing_keywords' => [],
                        'question_scores'  => [],
                    ])]]]],
                ],
            ], 200),
        ]);

        $this->actingAs($user)->post("/interview/sessions/{$session->id}/end");

        Http::assertSent(function ($request) {
            $schema = $request->data()['generationConfig']['response_schema'] ?? null;
            if ($schema === null) {
                return false;
            }

            $encoded = json_encode($schema);

            foreach (['minimum', 'maximum', 'maxLength', 'maxItems', 'minLength', 'minItems'] as $forbidden) {
                if (str_contains($encoded, '"' . $forbidden . '"')) {
                    return false;
                }
            }

            return true;
        });
    }
}
