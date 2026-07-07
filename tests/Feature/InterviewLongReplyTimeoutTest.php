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
 * Regression tests for "balasan chat terlalu panjang menyebabkan error":
 * Gemini 2.5 Flash spends real wall-clock time (and, separately, token
 * budget) on hidden "thinking" before emitting a visible answer. For the
 * elaborate, CV-referencing replies this persona is instructed to give,
 * that hidden reasoning phase can push the round trip close to the
 * previously hardcoded 30s/60s ceilings, aborting otherwise-successful
 * long replies. These tests pin down the fix: thinking is disabled for
 * the conversational calls, and the timeout ceilings are widened.
 */
class InterviewLongReplyTimeoutTest extends TestCase
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

    private function makeActiveSessionWithOpening(User $user, Cv $cv): InterviewSession
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
            'content'    => 'Tell me about yourself.',
        ]);

        return $session;
    }

    // ── 1. Non-streaming message call must disable Gemini's hidden thinking ──

    public function test_message_endpoint_disables_thinking_budget(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'A reasonably detailed follow-up question.']]]],
                ],
            ], 200),
        ]);

        $this->actingAs($user)
            ->postJson("/interview/sessions/{$session->id}/message", [
                'content' => 'Tell me in great detail about a time you led a cross-functional project.',
            ]);

        Http::assertSent(function ($request) {
            $config = $request->data()['generationConfig'] ?? [];

            return ($config['thinkingConfig']['thinkingBudget'] ?? null) === 0;
        });
    }

    // ── 2. Streaming call must disable Gemini's hidden thinking too ──────────

    public function test_stream_endpoint_disables_thinking_budget(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                'data: ' . json_encode([
                    'candidates' => [['content' => ['parts' => [['text' => 'Hello!']]]]],
                ]) . "\n\n",
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)->post(
            "/interview/sessions/{$session->id}/stream",
            ['content' => 'Tell me in great detail about a time you led a cross-functional project.']
        );
        $response->streamedContent();

        Http::assertSent(function ($request) {
            $config = $request->data()['generationConfig'] ?? [];

            return ($config['thinkingConfig']['thinkingBudget'] ?? null) === 0;
        });
    }

    // ── 3. Conversational Gemini timeout ceiling must be widened ─────────────
    // Pinned via a named class constant so a future edit can't silently
    // shrink it back toward the 30s/60s values that were too tight for
    // longer, elaborate replies.

    public function test_conversational_gemini_timeout_is_widened_past_previous_ceiling(): void
    {
        $reflection = new \ReflectionClass(\App\Services\InterviewService::class);
        $constant   = $reflection->getConstant('GEMINI_CONVERSATION_TIMEOUT_SECONDS');

        $this->assertNotFalse(
            $constant,
            'InterviewService must define GEMINI_CONVERSATION_TIMEOUT_SECONDS to pin the conversational Gemini call timeout.'
        );
        $this->assertGreaterThanOrEqual(
            90,
            $constant,
            'Conversational Gemini timeout regressed below the 90s floor needed for longer replies.'
        );
    }

    // ── 4. Frontend abort timer must not fire before the backend can finish ──

    public function test_frontend_stream_abort_timeout_is_widened_past_backend_timeout(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertDontSee('controller.abort(), 60000)', false);
        $response->assertSee('controller.abort(), 100000)', false);
    }
}
