<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class AiResumeFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a default template for testing
        CvTemplate::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);
    }

    protected function createCvForUser(User $user): Cv
    {
        return Cv::forceCreate([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'template_id' => CvTemplate::first()->id,
            'title' => 'My Test Resume',
            'status' => 'draft',
        ]);
    }

    // ────────────────────────────────────────────────
    // Refine Bullet: Premium Gating via QuotaMiddleware
    // ────────────────────────────────────────────────

    public function test_basic_user_with_no_credits_cannot_refine_bullet(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 5]);
        $cv = $this->createCvForUser($user);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work at the company',
            'job_context' => 'Software Engineer'
        ]);

        // QuotaMiddleware returns 402 when credits exhausted
        $response->assertStatus(402)
                 ->assertJson(['error' => 'quota_exceeded']);
    }

    public function test_basic_user_with_credits_can_refine_bullet(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refineBullet')
                 ->once()
                 ->with('Did some work at the company', 'Software Engineer')
                 ->andReturn(['Option 1', 'Option 2', 'Option 3']);
            $mock->shouldReceive('logUsage')
                 ->once();
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work at the company',
            'job_context' => 'Software Engineer'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'options' => ['Option 1', 'Option 2', 'Option 3']
                 ]);

        $this->assertEquals(1, $user->fresh()->ai_quota_used);
    }

    public function test_premium_user_can_refine_bullet(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        // Mock the AiService
        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refineBullet')
                 ->once()
                 ->with('Did some work at the company', 'Software Engineer')
                 ->andReturn(['Option 1', 'Option 2', 'Option 3']);
            $mock->shouldReceive('logUsage')
                 ->once();
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work at the company',
            'job_context' => 'Software Engineer'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'options' => ['Option 1', 'Option 2', 'Option 3']
                 ]);
                 
        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    // ────────────────────────────────────────────────
    // Refine Bullet: Credit refund on AI failure
    // ────────────────────────────────────────────────

    public function test_refine_bullet_refunds_credit_on_failure(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refineBullet')
                 ->once()
                 ->andThrow(new \Exception('AI service failed'));
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work at the company',
        ]);

        $response->assertStatus(500);

        // Credit should be refunded
        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    public function test_refine_bullet_refunds_reserved_credit_with_ai_credit_service_when_provider_fails(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refineBullet')
                 ->once()
                 ->andThrow(new \Exception('AI service failed'));
            $mock->shouldNotReceive('logUsage');
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work at the company',
            'job_context' => 'Software Engineer'
        ]);

        $response->assertStatus(500);

        $this->assertSame(0, $user->fresh()->ai_quota_used);

        $reservation = DB::table('ai_credit_reservations')
            ->where('user_id', $user->id)
            ->where('context', 'bullet_optimize')
            ->first();

        $this->assertNotNull($reservation, 'Refine bullet should reserve credit through AiCreditService.');
        $this->assertSame('reserved', $reservation->status);
        $this->assertSame(1, (int) $reservation->credits);
        $this->assertSame(0, (int) $reservation->usage_before);
        $this->assertSame(1, (int) $reservation->usage_after);
        $this->assertNotNull($reservation->refunded_at, 'Failed provider calls should refund the reservation.');
    }

    // ────────────────────────────────────────────────
    // Generate Versions: Premium Gating
    // ────────────────────────────────────────────────

    public function test_basic_user_with_no_credits_cannot_generate_versions(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 5]);
        $cv = $this->createCvForUser($user);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a test job description that meets length. ', 5)
        ]);

        $response->assertStatus(402)
                 ->assertJson(['error' => 'quota_exceeded']);
    }

    public function test_basic_user_with_insufficient_credits_blocked_for_generate_versions(): void
    {
        // Generate versions needs 3 credits, user has only 2 remaining (5 - 3 = 2)
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 3]);
        $cv = $this->createCvForUser($user);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a test job description that meets length. ', 5)
        ]);

        $response->assertStatus(402)
                 ->assertJson(['error' => 'quota_exceeded']);
    }

    public function test_premium_user_can_generate_parallel_versions(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);
        
        // Add some sections to the CV
        $cv->sections()->create([
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 1,
            'content' => ['summary' => str_repeat('This is a sufficiently long summary for testing purposes that exceeds two hundred characters to satisfy validation. ', 3)]
        ]);

        // Mock the AiService
        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateCvVersions')
                 ->once()
                 ->andReturn([
                     'leadership' => [['type' => 'personal_info', 'content' => ['summary' => 'Leadership summary']]],
                     'technical' => [['type' => 'personal_info', 'content' => ['summary' => 'Technical summary']]],
                     'ownership' => [['type' => 'personal_info', 'content' => ['summary' => 'Ownership summary']]],
                 ]);
            $mock->shouldReceive('logUsage')
                 ->once();
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a detailed job description intended to exceed fifty characters. ', 2)
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        // Check if ChameleonAdaptations were created
        $this->assertDatabaseCount('chameleon_adaptations', 3);
        $this->assertDatabaseHas('chameleon_adaptations', [
            'cv_id' => $cv->id,
            'tone_style' => 'leadership',
        ]);
        $this->assertDatabaseHas('chameleon_adaptations', [
            'cv_id' => $cv->id,
            'tone_style' => 'technical',
        ]);
        $this->assertDatabaseHas('chameleon_adaptations', [
            'cv_id' => $cv->id,
            'tone_style' => 'ownership',
        ]);

        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    // ────────────────────────────────────────────────
    // Generate Versions: Credit refund on AI failure
    // ────────────────────────────────────────────────

    public function test_generate_versions_refunds_credits_on_failure(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $cv->sections()->create([
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 1,
            'content' => ['summary' => str_repeat('Long enough content for testing. ', 10)]
        ]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateCvVersions')
                 ->once()
                 ->andThrow(new \Exception('AI service failed'));
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a test job description that meets length. ', 5)
        ]);

        $response->assertStatus(500);

        // All 3 credits should be refunded
        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    public function test_generate_versions_refunds_reserved_credits_with_ai_credit_service_when_provider_fails(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $cv->sections()->create([
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 1,
            'content' => ['summary' => str_repeat('Long enough content for testing. ', 10)]
        ]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateCvVersions')
                 ->once()
                 ->andThrow(new \Exception('AI service failed'));
            $mock->shouldNotReceive('logUsage');
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a test job description that meets length. ', 5)
        ]);

        $response->assertStatus(500);

        $this->assertSame(0, $user->fresh()->ai_quota_used);

        $reservation = DB::table('ai_credit_reservations')
            ->where('user_id', $user->id)
            ->where('context', 'generate_versions')
            ->first();

        $this->assertNotNull($reservation, 'Generate versions should reserve credits through AiCreditService.');
        $this->assertSame('reserved', $reservation->status);
        $this->assertSame(3, (int) $reservation->credits);
        $this->assertSame(0, (int) $reservation->usage_before);
        $this->assertSame(3, (int) $reservation->usage_after);
        $this->assertNotNull($reservation->refunded_at, 'Failed provider calls should refund the reservation.');
    }

    // ────────────────────────────────────────────────
    // Authorization: User cannot access other's CV
    // ────────────────────────────────────────────────

    public function test_user_cannot_refine_bullet_on_other_users_cv(): void
    {
        $owner = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $attacker = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($owner);

        $response = $this->actingAs($attacker)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Trying to access someone else CV',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_generate_versions_on_other_users_cv(): void
    {
        $owner = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $attacker = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($owner);

        $response = $this->actingAs($attacker)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a test job description that meets length. ', 5)
        ]);

        $response->assertStatus(403);
    }

    // ────────────────────────────────────────────────
    // ATS Analyze: Premium Gating
    // ────────────────────────────────────────────────

    public function test_basic_user_with_no_credits_cannot_ats_analyze(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 5]);

        $response = $this->actingAs($user)->postJson('/ats/analyze', [
            'resume'          => str_repeat('Experienced developer with skills. ', 10),
            'job_description' => str_repeat('We need a senior developer with experience. ', 10),
        ]);

        $response->assertStatus(402)
                 ->assertJson(['error' => 'quota_exceeded']);
    }

    public function test_premium_user_can_ats_analyze(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('analyzeAts')
                 ->once()
                 ->andReturn([
                     'score' => 75,
                     'rating' => ['label' => 'Very Good', 'sublabel' => 'Needs Minor Polish', 'color' => 'success'],
                     'matched' => ['PHP', 'Laravel'],
                     'missing' => [],
                     'section_breakdown' => [],
                     'action_verbs' => ['developed'],
                     'missing_verbs' => [],
                     'has_numbers' => true,
                     'length_tip' => 'Good length.',
                     'insights' => [],
                 ]);
            $mock->shouldReceive('logUsage')
                 ->once();
        });

        $response = $this->actingAs($user)->postJson('/ats/analyze', [
            'resume'          => str_repeat('Experienced developer with strong skills in PHP and Laravel. ', 5),
            'job_description' => str_repeat('We need a senior developer with experience in PHP and Laravel. ', 5),
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['score', 'rating', 'word_count']);

        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }

    public function test_ats_analyze_refunds_credit_on_failure(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('analyzeAts')
                 ->once()
                 ->andThrow(new \Exception('AI service failed'));
        });

        $response = $this->actingAs($user)->postJson('/ats/analyze', [
            'resume'          => str_repeat('Experienced developer with skills. ', 10),
            'job_description' => str_repeat('We need a senior developer with experience. ', 10),
        ]);

        $response->assertStatus(500);
        $this->assertEquals(0, $user->fresh()->ai_quota_used);
    }
}
