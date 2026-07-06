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
 * Regression tests for Gemini 2.5 Flash "thinking" consuming the entire
 * maxOutputTokens budget on longer/more elaborate replies, leaving
 * candidates[0].content.parts empty (finishReason: MAX_TOKENS). Reported
 * by the user as: "ketika balasan chat terlalu panjang ... terjadi error".
 */
class InterviewMaxTokensTruncationTest extends TestCase
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

    /**
     * Simulates Gemini 2.5 Flash spending the entire token budget on hidden
     * "thinking" before producing any visible answer: finishReason is
     * MAX_TOKENS and the parts array is absent, exactly as the real API
     * returns for this condition.
     */
    private function fakeMaxTokensJsonResponse(): array
    {
        return [
            'candidates' => [
                [
                    'content'      => ['role' => 'model'],
                    'finishReason' => 'MAX_TOKENS',
                ],
            ],
        ];
    }

    private function fakeMaxTokensSseBody(): string
    {
        return 'data: ' . json_encode($this->fakeMaxTokensJsonResponse()) . "\n\n";
    }

    // ── 1. Non-streaming message endpoint must not 500 on MAX_TOKENS ─────────

    public function test_message_endpoint_refunds_credit_when_reply_hits_max_tokens(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->fakeMaxTokensJsonResponse(), 200),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/interview/sessions/{$session->id}/message", [
                'content' => 'Tell me in great detail about a time you led a cross-functional project.',
            ]);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);

        $this->assertSame(
            0,
            $user->fresh()->ai_quota_used,
            'Credit was not refunded when Gemini truncated the reply at MAX_TOKENS.'
        );

        $this->assertDatabaseMissing('interview_messages', [
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => '',
        ]);
    }

    // ── 2. Streaming endpoint must surface a clean error, not silence ────────

    public function test_stream_endpoint_refunds_credit_when_reply_hits_max_tokens(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeMaxTokensSseBody(),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)->post(
            "/interview/sessions/{$session->id}/stream",
            ['content' => 'Tell me in great detail about a time you led a cross-functional project.']
        );

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('"error":', $content);
        $this->assertStringNotContainsString('"done":true', $content);

        $this->assertSame(0, $user->fresh()->ai_quota_used);
    }
}
