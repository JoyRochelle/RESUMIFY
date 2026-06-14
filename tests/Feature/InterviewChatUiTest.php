<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InterviewChatUiTest extends TestCase
{
    use RefreshDatabase;

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

    // ── 1. Start page accessible ──────────────────────────────────────────────

    public function test_authenticated_user_can_view_interview_start_page(): void
    {
        [$user] = $this->makeUserWithCv();

        $response = $this->actingAs($user)->get('/interview');

        $response->assertStatus(200);
    }

    // ── 2. Start page only lists own CVs ─────────────────────────────────────

    public function test_start_page_only_lists_own_cvs(): void
    {
        [$user, $cv]       = $this->makeUserWithCv();
        [$other, $otherCv] = $this->makeUserWithCv();

        $response = $this->actingAs($user)->get('/interview');

        $response->assertStatus(200)
                 ->assertSee($cv->title)
                 ->assertDontSee($otherCv->title);
    }

    // ── 3. User can view their own session ───────────────────────────────────

    public function test_user_can_view_own_active_session(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Selamat datang! Ceritakan pengalaman Anda.',
        ]);

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(200)
                 ->assertSee('Selamat datang! Ceritakan pengalaman Anda.');
    }

    // ── 4. Cannot view another user's session ────────────────────────────────

    public function test_user_cannot_view_another_users_session(): void
    {
        [$owner, $cv] = $this->makeUserWithCv();
        [$attacker]   = $this->makeUserWithCv();
        $session      = $this->makeSession($owner, $cv);

        $response = $this->actingAs($attacker)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(403);
    }

    // ── 5. End session marks it completed ────────────────────────────────────

    public function test_end_session_marks_session_completed(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/end");

        $response->assertRedirect(route('interview.index'));

        $this->assertDatabaseHas('interview_sessions', [
            'id'     => $session->id,
            'status' => 'completed',
        ]);

        $this->assertNotNull($session->fresh()->ended_at);
    }

    // ── 6. Cannot end an already-completed session ───────────────────────────

    public function test_cannot_end_already_completed_session(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv, 'completed');

        $response = $this->actingAs($user)
            ->post("/interview/sessions/{$session->id}/end");

        $response->assertRedirect();

        $this->assertDatabaseHas('interview_sessions', [
            'id'     => $session->id,
            'status' => 'completed',
        ]);
    }
}
