<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Regression tests for the interview SSE stream against malformed Gemini
 * responses and missing output buffers (production crash 2026-07-06,
 * InterviewController@stream: "ob_flush(): Failed to flush buffer").
 */
class InterviewStreamMalformedResponseTest extends TestCase
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
     * SSE body whose events carry a changed/unexpected JSON structure:
     * valid SSE framing, but no candidates[].content.parts[].text inside.
     */
    private function fakeMalformedGeminiSseBody(): string
    {
        return 'data: ' . json_encode([
            'error' => ['code' => 200, 'message' => 'unexpected structure'],
        ]) . "\n\n"
            . 'data: ' . json_encode(['candidates' => [['finishReason' => 'STOP']]]) . "\n\n";
    }

    private function fakeValidGeminiSseBody(string ...$tokens): string
    {
        $out = '';
        foreach ($tokens as $token) {
            $out .= 'data: ' . json_encode([
                'candidates' => [['content' => ['parts' => [['text' => $token]]]]],
            ]) . "\n\n";
        }
        return $out;
    }

    // ── 1. Malformed provider payload must refund the reserved credit ────────

    public function test_stream_refunds_credit_when_provider_payload_is_malformed(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeMalformedGeminiSseBody(),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('"error":', $content);
        $this->assertStringNotContainsString('"done":true', $content);

        $this->assertSame(
            0,
            $user->fresh()->ai_quota_used,
            'Credit was not refunded after a malformed provider payload.'
        );

        $reservation = DB::table('ai_credit_reservations')
            ->where('user_id', $user->id)
            ->where('context', 'interview_stream')
            ->first();

        $this->assertNotNull($reservation);
        $this->assertNotNull(
            $reservation->refunded_at,
            'Reservation was never marked as refunded after a malformed provider payload.'
        );
    }

    // ── 2. Malformed payload must not persist an empty assistant message ─────

    public function test_stream_does_not_persist_empty_assistant_message_on_malformed_payload(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeMalformedGeminiSseBody(),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->streamedContent();

        $this->assertDatabaseMissing('interview_messages', [
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => '',
        ]);

        $this->assertDatabaseMissing('ai_usage_logs', [
            'user_id'     => $user->id,
            'action_type' => 'interview_question',
        ]);
    }

    // ── 3. Stream must survive running without any output buffer ─────────────
    // Reproduces the production crash: under `php artisan serve` there is no
    // active output buffer, so every unguarded ob_flush() raises ErrorException.

    public function test_stream_survives_running_without_output_buffer(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeActiveSessionWithOpening($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeValidGeminiSseBody('Hello!'),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(200);

        // Drain every active output buffer to mirror the zero-buffer SAPI
        // state of `php artisan serve`, then restore them afterwards so
        // PHPUnit's own buffer bookkeeping stays intact.
        $bufferedContents = [];
        while (ob_get_level() > 0) {
            $bufferedContents[] = ob_get_clean();
        }

        $caught = null;
        try {
            try {
                $response->baseResponse->sendContent();
            } catch (\Throwable $e) {
                $caught = $e;
            }
        } finally {
            foreach (array_reverse($bufferedContents) as $buffered) {
                ob_start();
                echo $buffered;
            }
        }

        $this->assertNull(
            $caught,
            'SSE stream crashed when no output buffer was active: '
                . ($caught ? $caught->getMessage() : '')
        );

        $this->assertDatabaseHas('interview_messages', [
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Hello!',
        ]);
    }
}
