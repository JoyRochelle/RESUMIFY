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
 * Regression tests for "dulu sudah pernah ada tapi sekarang ketika klik
 * history chat yang sudah selesai hanya berisi chat saja": feedback
 * generation shares the same missing-thinkingConfig / too-tight-timeout
 * root cause already fixed for the conversational calls (see
 * InterviewLongReplyTimeoutTest). Analyzing a full transcript is the most
 * reasoning-heavy call in the system, yet it had the tightest timeout
 * (45s) and no thinkingConfig — so it was the most likely to fail,
 * leaving the session completed but without an InterviewFeedback row.
 */
class InterviewFeedbackGenerationTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'test-api-key']);
    }

    private function makeUserWithCv(string $role = 'basic'): array
    {
        $template = CvTemplate::create([
            'id'         => Str::ulid(),
            'name'       => 'Test Template',
            'blade_path' => 'templates.default',
            'is_active'  => true,
            'is_premium' => false,
        ]);

        $user = User::factory()->create(['role' => $role, 'ai_quota_used' => 0]);

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

        return [$user, $cv];
    }

    private function makeActiveSessionWithMessages(User $user, Cv $cv): InterviewSession
    {
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
            'content'    => 'Tell me about a time you led a project.',
        ]);
        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => 'I led a migration project at my previous company.',
        ]);

        return $session;
    }

    // ── 1. Feedback generation must disable Gemini's hidden thinking ─────────

    public function test_feedback_generation_disables_thinking_budget(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithMessages($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => json_encode([
                        'overall_score'    => 80,
                        'readiness_badge'  => 'ready',
                        'missing_keywords' => [],
                        'question_scores'  => [],
                    ])]]]],
                ],
            ], 200),
        ]);

        $this->actingAs($user)->post("/interview/sessions/{$session->id}/end");

        Http::assertSent(function ($request) {
            $config = $request->data()['generationConfig'] ?? [];

            return ($config['thinkingConfig']['thinkingBudget'] ?? null) === 0;
        });
    }

    // ── 2. Feedback generation timeout ceiling must be widened past 45s ──────

    public function test_feedback_generation_timeout_is_widened_past_previous_ceiling(): void
    {
        $reflection = new \ReflectionClass(\App\Services\InterviewService::class);
        $constant   = $reflection->getConstant('GEMINI_FEEDBACK_TIMEOUT_SECONDS');

        $this->assertNotFalse(
            $constant,
            'InterviewService must define GEMINI_FEEDBACK_TIMEOUT_SECONDS to pin the feedback-generation Gemini call timeout.'
        );
        $this->assertGreaterThanOrEqual(
            90,
            $constant,
            'Feedback generation timeout regressed below the 90s floor needed to analyze a full transcript.'
        );
    }

    // ── 3. A completed session with a successful feedback call must persist a score ──

    public function test_ending_a_session_persists_feedback_when_gemini_succeeds(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithMessages($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => json_encode([
                        'overall_score'    => 80,
                        'readiness_badge'  => 'ready',
                        'missing_keywords' => [],
                        'question_scores'  => [],
                    ])]]]],
                ],
            ], 200),
        ]);

        $this->actingAs($user)->post("/interview/sessions/{$session->id}/end");

        $this->assertDatabaseHas('interview_feedback', [
            'session_id'    => $session->id,
            'overall_score' => 80,
        ]);
    }
}
