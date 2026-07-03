<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class AtsScanOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private CvTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->template = CvTemplate::create([
            'id' => (string) Str::ulid(),
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);
    }

    public function test_user_cannot_scan_with_another_users_cv_id(): void
    {
        $owner = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $attacker = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $ownersCv = $this->cvFor($owner, ['title' => 'Owners Resume']);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('analyzeAts');
            $mock->shouldNotReceive('logUsage');
        });

        $response = $this->actingAs($attacker)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('Experienced Laravel developer with production hiring platform results. ', 5),
            'job_description' => str_repeat('We need a Laravel developer with API and database experience. ', 5),
            'cv_id' => $ownersCv->id,
        ]);

        $this->assertContains($response->getStatusCode(), [403, 404]);
        $this->assertDatabaseMissing('ats_scans', [
            'user_id' => $attacker->id,
            'cv_id' => $ownersCv->id,
        ]);
        $this->assertEquals(0, $attacker->fresh()->ai_quota_used);
    }

    public function test_user_can_scan_with_their_own_cv_id(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->cvFor($user, ['title' => 'My Resume']);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('analyzeAts')
                ->once()
                ->andReturn([
                    'score' => 82,
                    'rating' => ['label' => 'Strong', 'sublabel' => 'Well aligned', 'color' => 'success'],
                    'matched' => ['Laravel', 'API'],
                    'missing' => [],
                    'section_breakdown' => [],
                    'action_verbs' => ['built'],
                    'missing_verbs' => [],
                    'has_numbers' => true,
                    'length_tip' => 'Good length.',
                    'insights' => [],
                ]);
            $mock->shouldReceive('logUsage')->once();
        });

        $response = $this->actingAs($user)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('Experienced Laravel developer with production hiring platform results. ', 5),
            'job_description' => str_repeat('We need a Laravel developer with API and database experience. ', 5),
            'cv_id' => $cv->id,
            'job_title' => 'Laravel Engineer',
            'job_company' => 'Acme',
        ]);

        $response->assertOk()
            ->assertJsonPath('score', 82);

        $this->assertDatabaseHas('ats_scans', [
            'user_id' => $user->id,
            'cv_id' => $cv->id,
            'job_title' => 'Laravel Engineer',
            'job_company' => 'Acme',
        ]);
        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    public function test_ats_analyze_refunds_reserved_credit_when_provider_fails(): void
    {
        Config::set('plans.premium_features', []);

        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
        $cv = $this->cvFor($user, ['title' => 'My Resume']);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('analyzeAts')
                ->once()
                ->andThrow(new \Exception('AI service failed'));
            $mock->shouldNotReceive('logUsage');
        });

        $response = $this->actingAs($user)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('Experienced Laravel developer with production hiring platform results. ', 5),
            'job_description' => str_repeat('We need a Laravel developer with API and database experience. ', 5),
            'cv_id' => $cv->id,
        ]);

        $response->assertStatus(500);

        $this->assertSame(0, $user->fresh()->ai_quota_used);

        $reservation = DB::table('ai_credit_reservations')
            ->where('user_id', $user->id)
            ->where('context', 'ats_analyze')
            ->first();

        $this->assertNotNull($reservation, 'ATS analyze should reserve credit through AiCreditService.');
        $this->assertSame('reserved', $reservation->status);
        $this->assertSame(1, (int) $reservation->credits);
        $this->assertSame(0, (int) $reservation->usage_before);
        $this->assertSame(1, (int) $reservation->usage_after);
        $this->assertNotNull($reservation->refunded_at, 'Failed provider calls should refund the reservation.');
    }

    private function cvFor(User $user, array $attributes = []): Cv
    {
        return Cv::create(array_merge([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $this->template->id,
            'title' => 'Resume',
            'status' => 'draft',
        ], $attributes));
    }
}
