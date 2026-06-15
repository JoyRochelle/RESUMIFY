<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewFeedback;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InterviewSessionHistoryTest extends TestCase
{
    use RefreshDatabase;

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

    private function makeSession(User $user, Cv $cv, string $status = 'completed', ?string $jobTarget = null): InterviewSession
    {
        return InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => $jobTarget ?? 'Backend Engineer',
            'status'     => $status,
            'started_at' => now(),
            'ended_at'   => $status !== 'active' ? now() : null,
        ]);
    }

    private function makeFeedback(InterviewSession $session, int $score = 75): InterviewFeedback
    {
        return InterviewFeedback::create([
            'session_id'       => $session->id,
            'question_scores'  => [],
            'missing_keywords' => [],
            'overall_score'    => $score,
            'readiness_badge'  => $score >= 75 ? 'ready' : ($score >= 50 ? 'almost_ready' : 'needs_practice'),
        ]);
    }

    // ── 1. History page returns 200 for authenticated user ────────────────────

    public function test_history_page_accessible_to_authenticated_user(): void
    {
        [$user] = $this->makeUserWithCv();

        $response = $this->actingAs($user)->get('/interview/history');

        $response->assertStatus(200);
    }

    // ── 2. Sessions appear in the list with their job target ─────────────────

    public function test_history_shows_sessions_with_job_target(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'completed', 'Senior PHP Developer');

        $response = $this->actingAs($user)->get('/interview/history');

        $response->assertStatus(200)
                 ->assertSee('Senior PHP Developer');
    }

    // ── 3. Score is shown when feedback exists ────────────────────────────────

    public function test_history_shows_score_when_feedback_exists(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);
        $this->makeFeedback($session, 82);

        $response = $this->actingAs($user)->get('/interview/history');

        $response->assertStatus(200)
                 ->assertSee('82');
    }

    // ── 4. Empty state shown when no sessions exist ───────────────────────────

    public function test_history_shows_empty_state_with_no_sessions(): void
    {
        [$user] = $this->makeUserWithCv();

        $response = $this->actingAs($user)->get('/interview/history');

        $response->assertStatus(200)
                 ->assertSee('No interview sessions yet');
    }

    // ── 5. CV filter narrows results correctly ────────────────────────────────

    public function test_history_filters_by_resume(): void
    {
        [$user, $cv1] = $this->makeUserWithCv();

        $template = CvTemplate::first();
        $cv2 = Cv::create([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Second CV',
            'job_target'  => 'Frontend Engineer',
        ]);

        $this->makeSession($user, $cv1, 'completed', 'Backend Role');
        $this->makeSession($user, $cv2, 'completed', 'Frontend Role');

        $response = $this->actingAs($user)->get("/interview/history?cv_id={$cv1->id}");

        $response->assertStatus(200)
                 ->assertSee('Backend Role')
                 ->assertDontSee('Frontend Role');
    }

    // ── 6. History is scoped to the authenticated user ────────────────────────

    public function test_history_only_shows_own_sessions(): void
    {
        [$user, $cv]        = $this->makeUserWithCv();
        [$otherUser, $cv2]  = $this->makeUserWithCv();

        $this->makeSession($user, $cv, 'completed', 'My Job Target');
        $this->makeSession($otherUser, $cv2, 'completed', 'Other User Job');

        $response = $this->actingAs($user)->get('/interview/history');

        $response->assertStatus(200)
                 ->assertSee('My Job Target')
                 ->assertDontSee('Other User Job');
    }
}
