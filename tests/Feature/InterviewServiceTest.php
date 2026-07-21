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

class InterviewServiceTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'test-api-key']);
    }

    private function geminiResponse(string $text): array
    {
        return ['candidates' => [['content' => ['parts' => [['text' => $text]]]]]];
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

        $cv = Cv::forceCreate([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Test CV',
            'job_target'  => 'Backend Engineer',
        ]);

        // Work experience with a specific company name for prompt-injection assertions
        CvSection::forceCreate([
            'id'     => Str::ulid(),
            'cv_id'  => $cv->id,
            'type'   => 'work_experience',
            'title'  => 'Work Experience',
            'order'  => 1,
            'content' => [
                ['company' => 'PT Maju Jaya', 'position' => 'Backend Developer', 'start_date' => '2022-01', 'end_date' => '2024-01', 'description' => 'Built REST APIs'],
            ],
        ]);

        CvSection::forceCreate([
            'id'     => Str::ulid(),
            'cv_id'  => $cv->id,
            'type'   => 'personal_info',
            'title'  => 'Personal Info',
            'order'  => 0,
            'content' => ['name' => 'Budi Santoso', 'email' => 'budi@example.com'],
        ]);

        return [$user, $cv];
    }

    // ── 1. Happy path: start session ─────────────────────────────────────────

    public function test_start_creates_session_and_returns_opening_question(): void
    {
        [$user, $cv] = $this->makeUserWithCv();

        Http::fake([self::GEMINI_PATTERN => Http::response($this->geminiResponse('Selamat pagi! Bisa ceritakan pengalaman Anda di PT Maju Jaya?'))]);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'session_id', 'message'])
                 ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('interview_sessions', [
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => 'active',
        ]);

        $sessionId = $response->json('session_id');
        $this->assertDatabaseHas('interview_messages', [
            'session_id' => $sessionId,
            'role'       => 'assistant',
        ]);
    }

    // ── 1b. Real token usage & cost are captured from usageMetadata ───────────

    public function test_start_records_real_token_usage_and_cost(): void
    {
        [$user, $cv] = $this->makeUserWithCv();

        // Deterministic pricing regardless of environment overrides.
        config([
            'services.gemini.pricing.input_per_million'  => 0.30,
            'services.gemini.pricing.output_per_million' => 2.50,
        ]);

        // Gemini returns usageMetadata on every real response — this is what
        // was previously discarded, making every ai_usage_logs row store 0.
        $body = $this->geminiResponse('Selamat pagi! Ceritakan pengalaman Anda.');
        $body['usageMetadata'] = [
            'promptTokenCount'     => 1000,
            'candidatesTokenCount' => 200,
            'totalTokenCount'      => 1200,
        ];

        Http::fake([self::GEMINI_PATTERN => Http::response($body)]);

        $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ])->assertStatus(200);

        // tokens_used must reflect the real total, not the old hardcoded 0.
        $this->assertDatabaseHas('ai_usage_logs', [
            'user_id'     => $user->id,
            'action_type' => 'interview_question',
            'tokens_used' => 1200,
        ]);

        // cost = 1000/1e6 * 0.30 + 200/1e6 * 2.50 = 0.0008 USD
        $log = \App\Models\AiUsageLog::where('user_id', $user->id)->latest('id')->first();
        $this->assertEqualsWithDelta(0.0008, (float) $log->cost_usd, 0.0000005);
    }

    // ── 2. CV content is injected into the Gemini prompt ─────────────────────

    public function test_cv_sections_are_injected_into_gemini_prompt(): void
    {
        [$user, $cv] = $this->makeUserWithCv();

        Http::fake([self::GEMINI_PATTERN => Http::response($this->geminiResponse('Ceritakan proyek di PT Maju Jaya?'))]);

        $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->body();
            // CV company name must appear in the system instruction sent to Gemini
            return str_contains($body, 'PT Maju Jaya');
        });
    }

    // ── 3. Message sends full conversation history ────────────────────────────

    public function test_message_sends_full_conversation_history_to_gemini(): void
    {
        [$user, $cv] = $this->makeUserWithCv();

        Http::fake([self::GEMINI_PATTERN => Http::response($this->geminiResponse('Pertanyaan lanjutan.'))]);

        // Manually create a session with one prior exchange
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
            'content'    => 'Ceritakan pengalaman Anda.',
        ]);

        $this->actingAs($user)->postJson("/interview/sessions/{$session->id}/message", [
            'content' => 'Saya bekerja di PT Maju Jaya selama 2 tahun.',
        ]);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            $contents = $body['contents'] ?? [];
            // Should contain the seed trigger + prior assistant turn + user's new message = 3 entries
            return count($contents) >= 3;
        });
    }

    public function test_message_prompt_uses_bounded_recent_conversation_history(): void
    {
        [$user, $cv] = $this->makeUserWithCv();

        Http::fake([self::GEMINI_PATTERN => Http::response($this->geminiResponse('Pertanyaan lanjutan.'))]);

        $session = InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => 'active',
            'started_at' => now(),
        ]);

        for ($i = 0; $i < 30; $i++) {
            InterviewMessage::create([
                'id'         => Str::ulid(),
                'session_id' => $session->id,
                'role'       => $i % 2 === 0 ? 'assistant' : 'user',
                'content'    => "Historical turn {$i}",
                'created_at' => now()->subMinutes(30 - $i),
                'updated_at' => now()->subMinutes(30 - $i),
            ]);
        }

        $this->actingAs($user)->postJson("/interview/sessions/{$session->id}/message", [
            'content' => 'This is the newest candidate answer.',
        ])->assertOk();

        Http::assertSentCount(1);

        $request = Http::recorded()->first()[0];
        $body = json_decode($request->body(), true);
        $contents = $body['contents'] ?? [];
        $encodedContents = json_encode($contents);

        $this->assertLessThanOrEqual(21, count($contents));
        $this->assertStringNotContainsString('Historical turn 0', $encodedContents);
        $this->assertStringContainsString('This is the newest candidate answer.', $encodedContents);
    }

    // ── 4. Cannot message another user's session ──────────────────────────────

    public function test_user_cannot_message_another_users_session(): void
    {
        [$owner, $cv] = $this->makeUserWithCv();
        $attacker = User::factory()->create(['role' => 'premium']);

        $session = InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $owner->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => 'active',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($attacker)->postJson("/interview/sessions/{$session->id}/message", [
            'content' => 'Hacked!',
        ]);

        $response->assertStatus(403);
    }

    // ── 5. Cannot send message to a completed session ─────────────────────────

    public function test_cannot_send_message_to_completed_session(): void
    {
        [$user, $cv] = $this->makeUserWithCv();

        $session = InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => 'completed',
            'started_at' => now(),
            'ended_at'   => now(),
        ]);

        $response = $this->actingAs($user)->postJson("/interview/sessions/{$session->id}/message", [
            'content' => 'Masih bisa kirim?',
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['success' => false]);

        $this->assertSame(0, $user->fresh()->ai_quota_used);
        $this->assertDatabaseMissing('ai_credit_reservations', [
            'user_id' => $user->id,
            'context' => 'interview_message',
        ]);
    }

    // ── 6. Quota is refunded on Gemini failure ────────────────────────────────

    public function test_quota_is_refunded_on_gemini_failure(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');
        $user->forceFill(['ai_quota_used' => 0])->save();

        Http::fake([self::GEMINI_PATTERN => Http::response(null, 500)]);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(500);

        // Credit must be refunded — quota unchanged
        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    // ── 6b. No orphaned session is created when the AI fails on start ──────────

    public function test_no_session_is_created_when_gemini_fails_on_start(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');

        Http::fake([self::GEMINI_PATTERN => Http::response(null, 500)]);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(500);

        // The AI call failed before persistence, so no empty/orphaned session or
        // message may remain — otherwise a failing AI could be spammed into
        // creating unlimited hollow sessions.
        $this->assertDatabaseCount('interview_sessions', 0);
        $this->assertDatabaseCount('interview_messages', 0);
    }

    public function test_message_provider_failure_refunds_reserved_credit(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');

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
            'content'    => 'Ceritakan pengalaman Anda.',
        ]);

        Http::fake([self::GEMINI_PATTERN => Http::response(null, 500)]);

        $response = $this->actingAs($user)->postJson("/interview/sessions/{$session->id}/message", [
            'content' => 'Saya membangun REST API.',
        ]);

        $response->assertStatus(500);

        $this->assertSame(0, $user->fresh()->ai_quota_used);
        $this->assertDatabaseHas('ai_credit_reservations', [
            'user_id' => $user->id,
            'credits' => 1,
            'context' => 'interview_message',
            'status' => 'reserved',
        ]);

        $reservation = DB::table('ai_credit_reservations')
            ->where('user_id', $user->id)
            ->where('context', 'interview_message')
            ->first();

        $this->assertNotNull($reservation->refunded_at);
    }
}
