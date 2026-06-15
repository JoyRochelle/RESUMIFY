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

class InterviewFeedbackTest extends TestCase
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
            'title'       => 'CV-' . substr($user->id, -6),
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

    private function makeFeedback(InterviewSession $session, array $overrides = []): InterviewFeedback
    {
        return InterviewFeedback::create(array_merge([
            'session_id'       => $session->id,
            'question_scores'  => [
                [
                    'question'       => 'Ceritakan pengalaman Anda di PT Contoh Sejahtera',
                    'answer_summary' => 'Kandidat menyebutkan pengalaman backend dengan Laravel.',
                    'star_scores'    => ['situation' => 80, 'task' => 70, 'action' => 85, 'result' => 75],
                    'feedback'       => 'Jawaban baik namun kurang menyebutkan hasil kuantitatif.',
                ],
            ],
            'missing_keywords' => ['agile', 'leadership'],
            'overall_score'    => 80,
            'readiness_badge'  => 'ready',
        ], $overrides));
    }

    private function geminiJsonBody(array $data): array
    {
        return [
            'candidates' => [[
                'content' => ['parts' => [['text' => json_encode($data)]]],
            ]],
        ];
    }

    // ── 1. End session triggers feedback generation and redirects to report ───

    public function test_end_session_generates_feedback_and_redirects_to_report(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Ceritakan pengalaman Anda.',
        ]);
        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => 'Saya pernah bekerja di PT Contoh selama 2 tahun.',
        ]);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->geminiJsonBody([
                'overall_score'    => 82,
                'readiness_badge'  => 'ready',
                'missing_keywords' => ['agile'],
                'question_scores'  => [[
                    'question'       => 'Ceritakan pengalaman Anda.',
                    'answer_summary' => 'Kandidat menyebutkan PT Contoh.',
                    'star_scores'    => ['situation' => 80, 'task' => 75, 'action' => 85, 'result' => 80],
                    'feedback'       => 'Jawaban bagus.',
                ]],
            ]), 200),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/end");

        $response->assertRedirect(route('interview.feedback', $session));

        $this->assertDatabaseHas('interview_feedback', [
            'session_id'    => $session->id,
            'overall_score' => 82,
        ]);
    }

    // ── 2. Feedback page is accessible to the session owner ──────────────────

    public function test_feedback_page_accessible_to_session_owner(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'completed');
        $this->makeFeedback($session);

        $response = $this->actingAs($user)
            ->get("/interview/sessions/{$session->id}/feedback");

        $response->assertStatus(200);
    }

    // ── 3. Another user cannot access the feedback page ──────────────────────

    public function test_feedback_page_blocked_for_other_users(): void
    {
        [$owner, $cv]  = $this->makeUserWithCv();
        [$attacker]    = $this->makeUserWithCv();
        $session       = $this->makeSession($owner, $cv, 'completed');
        $this->makeFeedback($session);

        $response = $this->actingAs($attacker)
            ->get("/interview/sessions/{$session->id}/feedback");

        $response->assertStatus(403);
    }

    // ── 4. Feedback page renders the readiness badge label ───────────────────

    public function test_feedback_shows_readiness_badge(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'completed');
        $this->makeFeedback($session, ['readiness_badge' => 'ready', 'overall_score' => 80]);

        $response = $this->actingAs($user)
            ->get("/interview/sessions/{$session->id}/feedback");

        $response->assertStatus(200)
                 ->assertSee('Ready to Work');
    }

    // ── 5. Feedback page renders question score cards ─────────────────────────

    public function test_feedback_shows_question_scores(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'completed');
        $this->makeFeedback($session);

        $response = $this->actingAs($user)
            ->get("/interview/sessions/{$session->id}/feedback");

        $response->assertStatus(200)
                 ->assertSee('Ceritakan pengalaman Anda di PT Contoh Sejahtera');
    }

    // ── 6. AI failure still marks session completed and redirects to index ───

    public function test_end_session_still_completes_if_ai_fails(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(null, 500),
        ]);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/end");

        $response->assertRedirect(route('interview.index'));

        $this->assertDatabaseHas('interview_sessions', [
            'id'     => $session->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseMissing('interview_feedback', [
            'session_id' => $session->id,
        ]);
    }
}
