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

class InterviewSseStreamingTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'test-api-key']);
    }

    private function makeUserWithCv(string $role = 'premium'): array
    {
        $template = CvTemplate::create([
            'id'         => Str::ulid(),
            'name'       => 'Test Template',
            'blade_path' => 'templates.default',
            'is_active'  => true,
            'is_premium' => false,
        ]);

        $user = User::factory()->create(['role' => $role, 'ai_quota_used' => 0]);

        $cv = Cv::create([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Test CV',
            'job_target'  => 'Backend Engineer',
        ]);

        CvSection::create([
            'id'      => Str::ulid(),
            'cv_id'   => $cv->id,
            'type'    => 'target_job',
            'title'   => 'Target Job',
            'order'   => 0,
            'content' => ['job_title' => 'Backend Engineer'],
        ]);

        return [$user, $cv];
    }

    private function makeSession(User $user, Cv $cv, string $status = 'active'): InterviewSession
    {
        return InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => $status,
            'started_at' => now(),
            'ended_at'   => $status !== 'active' ? now() : null,
        ]);
    }

    private function makeAssistantMessage(InterviewSession $session): void
    {
        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Tell me about yourself.',
        ]);
    }

    private function fakeGeminiSseBody(string ...$tokens): string
    {
        $out = '';
        foreach ($tokens as $token) {
            $out .= 'data: ' . json_encode([
                'candidates' => [['content' => ['parts' => [['text' => $token]]]]],
            ]) . "\n\n";
        }
        return $out;
    }

    // ── 1. Stream endpoint returns 200 for session owner ─────────────────────

    public function test_stream_endpoint_accessible_for_session_owner(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);
        $this->makeAssistantMessage($session);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeGeminiSseBody('Hello!'),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi there.']);

        $response->assertStatus(200);
    }

    // ── 2. Other users cannot access the stream endpoint ────────────────────

    public function test_stream_blocked_for_other_user(): void
    {
        [$owner, $cv]  = $this->makeUserWithCv();
        [$attacker]    = $this->makeUserWithCv();
        $session       = $this->makeSession($owner, $cv);

        $response = $this->actingAs($attacker)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(403);
    }

    // ── 3. User message is saved to DB before stream is emitted ─────────────

    public function test_stream_saves_user_message_before_stream(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);
        $this->makeAssistantMessage($session);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeGeminiSseBody('Great answer!'),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'My user answer.']);

        $this->assertDatabaseHas('interview_messages', [
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => 'My user answer.',
        ]);
    }

    // ── 4. Streamed tokens are assembled and saved as assistant message ──────

    public function test_stream_saves_assembled_ai_response_to_db(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);
        $this->makeAssistantMessage($session);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeGeminiSseBody('Hello, ', 'world', '!'),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(200);
        $content = $response->streamedContent(); // executes the closure, captures via ob_start callback
        $this->assertStringContainsString('"done":true', $content);

        $this->assertDatabaseHas('interview_messages', [
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Hello, world!',
        ]);
    }

    // ── 5. Completed session returns 422 SSE error event ────────────────────

    public function test_stream_returns_error_event_if_session_not_active(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'completed');

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(422);

        $this->assertSame(0, $user->fresh()->ai_quota_used);
        $this->assertDatabaseMissing('ai_credit_reservations', [
            'user_id' => $user->id,
            'context' => 'interview_stream',
        ]);
    }

    // ── 6. Quota is refunded when AI call fails ───────────────────────────────

    public function test_stream_refunds_quota_on_ai_failure(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeSession($user, $cv);
        $this->makeAssistantMessage($session);

        $initialQuota = $user->ai_quota_used;

        Http::fake([
            self::GEMINI_PATTERN => Http::response(null, 500),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(200);
        $content = $response->streamedContent(); // executes the closure, triggering the catch block
        $this->assertStringContainsString('"error":', $content);

        $user->refresh();
        $this->assertEquals($initialQuota, $user->ai_quota_used);
    }

    public function test_stream_provider_failure_refunds_reserved_credit(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeSession($user, $cv);
        $this->makeAssistantMessage($session);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(null, 500),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringContainsString('"error":', $content);

        $this->assertSame(0, $user->fresh()->ai_quota_used);
        $this->assertDatabaseHas('ai_credit_reservations', [
            'user_id' => $user->id,
            'credits' => 1,
            'context' => 'interview_stream',
            'status' => 'reserved',
        ]);

        $reservation = DB::table('ai_credit_reservations')
            ->where('user_id', $user->id)
            ->where('context', 'interview_stream')
            ->first();

        $this->assertNotNull($reservation->refunded_at);
    }

    public function test_stream_does_not_consume_credit_when_client_disconnects_before_stream_runs(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $session     = $this->makeSession($user, $cv);
        $this->makeAssistantMessage($session);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->fakeGeminiSseBody('This token is never consumed.'),
                200,
                ['Content-Type' => 'text/event-stream']
            ),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/stream", ['content' => 'Hi.']);

        $response->assertStatus(200);

        $this->assertSame(0, $user->fresh()->ai_quota_used);
        $this->assertDatabaseMissing('ai_usage_logs', [
            'user_id' => $user->id,
            'action_type' => 'interview_question',
            'resume_id' => $session->resume_id,
        ]);
    }
}
