<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Basic accounts get a limited number of free runs of the ATS Analyzer and the
 * mock interview before the upgrade wall goes up.
 */
class FreeTrialAllowanceTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'test-api-key']);
    }

    private function basicUser(): User
    {
        return User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
    }

    private function cvFor(User $user): Cv
    {
        $template = CvTemplate::create([
            'id' => (string) Str::ulid(),
            'name' => 'Trial Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);

        $cv = Cv::forceCreate([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Trial Resume',
            'job_target' => 'Backend Engineer',
        ]);

        CvSection::forceCreate([
            'id' => (string) Str::ulid(),
            'cv_id' => $cv->id,
            'type' => 'target_job',
            'title' => 'Target Job',
            'order' => 0,
            'content' => ['job_title' => 'Backend Engineer'],
        ]);

        return $cv;
    }

    private function spendAtsTrials(User $user, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            AiUsageLog::create([
                'user_id' => $user->id,
                'action_type' => 'ats_analyze',
            ]);
        }
    }

    private function spendInterviewTrials(User $user, Cv $cv, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            InterviewSession::create([
                'id' => (string) Str::ulid(),
                'user_id' => $user->id,
                'resume_id' => $cv->id,
                'job_target' => 'Backend Engineer',
                'status' => 'completed',
                'started_at' => now(),
                'ended_at' => now(),
            ]);
        }
    }

    // ── Configured allowance ────────────────────────────────────────────────

    public function test_both_features_default_to_three_free_runs(): void
    {
        $this->assertSame(3, config('plans.trials.ats_analyze'));
        $this->assertSame(3, config('plans.trials.interview'));
    }

    public function test_basic_user_starts_with_the_full_allowance(): void
    {
        $user = $this->basicUser();

        $this->assertSame(3, $user->getTrialRemaining('ats_analyze'));
        $this->assertSame(3, $user->getTrialRemaining('interview'));
        $this->assertTrue($user->canUseTrialFeature('ats_analyze'));
    }

    public function test_premium_and_admin_are_never_trial_limited(): void
    {
        foreach (['premium', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->assertNull($user->getTrialRemaining('ats_analyze'));
            $this->assertNull($user->getTrialRemaining('interview'));
            $this->assertTrue($user->hasTrialRemaining('interview'));
        }
    }

    // ── ATS Analyzer ────────────────────────────────────────────────────────

    public function test_basic_user_can_run_ats_analysis_inside_the_trial(): void
    {
        $user = $this->basicUser();

        // Faked at the HTTP layer so the real usage log is written — that log
        // is what the trial counter reads.
        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => json_encode([
                        'score' => 82,
                        'rating' => ['label' => 'Very Good', 'sublabel' => 'Needs Minor Polish', 'color' => 'success'],
                        'keyword_score' => 76,
                        'matched' => ['Laravel'],
                        'missing' => [],
                        'section_breakdown' => [],
                        'action_verbs' => ['built'],
                        'missing_verbs' => ['owned'],
                        'has_numbers' => true,
                        'length_tip' => 'Good length.',
                        'insights' => [
                            ['title' => 'Add metrics', 'body' => 'Quantify impact.'],
                            ['title' => 'Mirror keywords', 'body' => 'Use job description terms.'],
                            ['title' => 'Tighten summary', 'body' => 'Lead with backend scope.'],
                            ['title' => 'Show ownership', 'body' => 'Name your delivery role.'],
                        ],
                    ])]]],
                ]],
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('Experienced Laravel developer with production hiring platform results. ', 5),
            'job_description' => str_repeat('We need a Laravel developer with API and database experience. ', 5),
        ]);

        $response->assertOk();
        $this->assertSame(2, $user->fresh()->getTrialRemaining('ats_analyze'));
    }

    public function test_basic_user_is_blocked_once_the_ats_trial_is_spent(): void
    {
        $user = $this->basicUser();
        $this->spendAtsTrials($user, 3);

        $response = $this->actingAs($user)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('resume content with achievements ', 4),
            'job_description' => str_repeat('job description with product metrics ', 4),
        ]);

        $response->assertStatus(402)
            ->assertJson([
                'error' => 'premium_required',
                'upgrade_url' => route('user.upgrade-quota'),
            ]);
    }

    public function test_deleting_scan_history_does_not_refill_the_ats_trial(): void
    {
        $user = $this->basicUser();
        $this->spendAtsTrials($user, 3);

        // Usage is counted from the append-only AI log, not from ats_scans.
        $this->assertSame(0, $user->getTrialRemaining('ats_analyze'));
        $this->assertSame(0, $user->atsScans()->count());
        $this->assertFalse($user->canUseTrialFeature('ats_analyze'));
    }

    public function test_ats_page_shows_the_remaining_trial_count(): void
    {
        $user = $this->basicUser();
        $this->spendAtsTrials($user, 1);

        $response = $this->actingAs($user)->get(route('user.ai-assistant'));

        $response->assertOk()
            ->assertSee('Free trial: 2 analyses left of 3')
            ->assertSee('analyze-btn', false);
    }

    public function test_ats_page_shows_the_upgrade_lock_once_the_trial_is_spent(): void
    {
        $user = $this->basicUser();
        $this->spendAtsTrials($user, 3);

        $response = $this->actingAs($user)->get(route('user.ai-assistant'));

        $response->assertOk()
            ->assertSee('all 3 free ATS analyses')
            ->assertDontSee('id="analyze-btn"', false);
    }

    // ── Mock interview ──────────────────────────────────────────────────────

    public function test_basic_user_can_start_a_second_and_third_interview(): void
    {
        $user = $this->basicUser();
        $cv = $this->cvFor($user);
        $this->spendInterviewTrials($user, $cv, 2);

        Http::fake([
            self::GEMINI_PATTERN => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => 'Tell me about yourself.']]]]],
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id' => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
    }

    public function test_interview_page_shows_the_remaining_trial_count(): void
    {
        $user = $this->basicUser();
        $cv = $this->cvFor($user);
        $this->spendInterviewTrials($user, $cv, 1);

        $response = $this->actingAs($user)->get(route('interview.index'));

        $response->assertOk()->assertSee('2 free trial sessions left');
    }

    public function test_interview_trial_counter_is_singular_on_the_last_run(): void
    {
        $user = $this->basicUser();
        $cv = $this->cvFor($user);
        $this->spendInterviewTrials($user, $cv, 2);

        $response = $this->actingAs($user)->get(route('interview.index'));

        $response->assertOk()->assertSee('1 free trial session left');
    }

    // ── Configurability ─────────────────────────────────────────────────────

    public function test_allowance_is_driven_by_config(): void
    {
        config(['plans.trials.interview' => 1]);

        $user = $this->basicUser();
        $cv = $this->cvFor($user);
        $this->spendInterviewTrials($user, $cv, 1);

        $response = $this->actingAs($user)->postJson('/interview/start', [
            'cv_id' => $cv->id,
            'job_target' => 'Backend Engineer',
        ]);

        $response->assertStatus(402)->assertJson(['error' => 'trial_used']);
    }
}
