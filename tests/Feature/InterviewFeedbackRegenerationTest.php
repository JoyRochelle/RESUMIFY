<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewFeedback;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Sessions that ended while feedback generation was broken (Gemini 400,
 * see InterviewFeedbackSchemaCompatTest) are stuck: completed, no
 * InterviewFeedback row, and history falls back to the chat-only view
 * forever. This covers the recovery path — regenerating the report from
 * the conversation page — plus the report links between chat and report.
 */
class InterviewFeedbackRegenerationTest extends TestCase
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

    private function makeSession(User $user, Cv $cv, string $status = 'completed'): InterviewSession
    {
        $session = InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => $status,
            'started_at' => now()->subHour(),
            'ended_at'   => $status !== 'active' ? now() : null,
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

        return $session;
    }

    private function makeFeedback(InterviewSession $session): InterviewFeedback
    {
        return InterviewFeedback::create([
            'session_id'       => $session->id,
            'question_scores'  => [],
            'missing_keywords' => [],
            'overall_score'    => 70,
            'readiness_badge'  => 'almost_ready',
        ]);
    }

    private function fakeSuccessfulFeedback(): void
    {
        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => json_encode([
                        'overall_score'    => 82,
                        'readiness_badge'  => 'ready',
                        'missing_keywords' => [],
                        'question_scores'  => [],
                    ])]]]],
                ],
            ], 200),
        ]);
    }

    // ── Regeneration endpoint ────────────────────────────────────────────────

    public function test_owner_can_regenerate_report_for_completed_session_without_feedback(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        $this->fakeSuccessfulFeedback();

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/feedback/generate");

        $response->assertRedirect(route('interview.feedback', $session));

        $this->assertDatabaseHas('interview_feedback', [
            'session_id'    => $session->id,
            'overall_score' => 82,
        ]);
    }

    public function test_regeneration_is_rejected_when_feedback_already_exists(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);
        $this->makeFeedback($session);

        Http::fake();

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/feedback/generate");

        $response->assertRedirect(route('interview.feedback', $session));
        Http::assertNothingSent();
        $this->assertSame(0, $user->fresh()->ai_quota_used);
    }

    public function test_regeneration_is_rejected_for_active_session(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'active');

        Http::fake();

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/feedback/generate");

        $response->assertRedirect();
        Http::assertNothingSent();
        $this->assertDatabaseMissing('interview_feedback', ['session_id' => $session->id]);
    }

    public function test_regeneration_is_forbidden_for_other_users(): void
    {
        [$owner, $cv] = $this->makeUserWithCv();
        [$attacker]   = $this->makeUserWithCv();
        $session      = $this->makeSession($owner, $cv);

        $response = $this->actingAs($attacker)
            ->post("/interview/sessions/{$session->id}/feedback/generate");

        $response->assertStatus(403);
    }

    public function test_regeneration_refunds_credit_when_provider_fails(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(null, 500),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/feedback/generate");

        $response->assertRedirect();
        $this->assertSame(0, $user->fresh()->ai_quota_used);
        $this->assertDatabaseMissing('interview_feedback', ['session_id' => $session->id]);
    }

    // ── Links between the conversation page and the report ──────────────────

    public function test_completed_session_page_offers_generate_report_when_feedback_missing(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertSee('Generate Report');
        $response->assertSee("/interview/sessions/{$session->id}/feedback/generate", false);
    }

    public function test_completed_session_page_links_to_report_when_feedback_exists(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);
        $this->makeFeedback($session);

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertSee('View Report');
        $response->assertSee(route('interview.feedback', $session), false);
    }

    public function test_active_session_page_does_not_offer_report_actions(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'active');

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertDontSee('Generate Report');
        $response->assertDontSee('View Report');
    }
}
