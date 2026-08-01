<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class InterviewTrialGateTest extends TestCase
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
            'title'       => 'CV-' . substr($user->id, -6),
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

    private function geminiResponse(string $text): array
    {
        return ['candidates' => [['content' => ['parts' => [['text' => $text]]]]]];
    }

    // ── 1. Basic user with no sessions can start their trial ─────────────────

    public function test_basic_user_can_start_first_trial_session(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->geminiResponse('Welcome! Please tell me about yourself.'),
                200
            ),
        ]);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    // ── 2. Basic user with an existing session is blocked ────────────────────

    public function test_basic_user_is_blocked_after_using_trial(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');

        for ($i = 0; $i < config('plans.trials.interview'); $i++) {
            $this->makeSession($user, $cv);
        }

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(402)
                 ->assertJson(['error' => 'trial_used']);
    }

    // ── 3. Premium user bypasses the trial gate even with many sessions ───────

    public function test_premium_user_bypasses_trial_gate(): void
    {
        [$user, $cv] = $this->makeUserWithCv('premium');

        // Create 3 existing sessions
        $this->makeSession($user, $cv);
        $this->makeSession($user, $cv);
        $this->makeSession($user, $cv);

        Http::fake([
            self::GEMINI_PATTERN => Http::response(
                $this->geminiResponse('Welcome back! Let\'s continue your practice.'),
                200
            ),
        ]);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id'      => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    // ── 4. Basic user who used their trial sees the upgrade wall ─────────────

    public function test_basic_user_sees_upgrade_wall_after_trial(): void
    {
        [$user, $cv] = $this->makeUserWithCv('basic');

        for ($i = 0; $i < config('plans.trials.interview'); $i++) {
            $this->makeSession($user, $cv);
        }

        $response = $this->actingAs($user)->get('/interview');

        $response->assertStatus(200)
                 ->assertSee('Your Free Trials Have Been Used');
    }

    // ── 5. Basic user with no sessions sees the trial info banner ────────────

    public function test_basic_user_sees_trial_banner_before_using_trial(): void
    {
        [$user] = $this->makeUserWithCv('basic');

        $response = $this->actingAs($user)->get('/interview');

        $response->assertStatus(200)
                 ->assertSee('3 free trial sessions left');
    }
}
