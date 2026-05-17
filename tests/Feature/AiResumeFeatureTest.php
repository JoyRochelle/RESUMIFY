<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        return Cv::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'template_id' => CvTemplate::first()->id,
            'title' => 'My Test Resume',
            'status' => 'draft',
        ]);
    }

    public function test_basic_user_cannot_refine_bullet(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work',
            'job_context' => 'Software Engineer'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['success' => false, 'message' => 'Premium feature only.']);
    }

    public function test_premium_user_can_refine_bullet(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        // Mock the AiService
        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('refineBullet')
                 ->once()
                 ->with('Did some work', 'Software Engineer')
                 ->andReturn(['Option 1', 'Option 2', 'Option 3']);
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/refine-bullet", [
            'text' => 'Did some work',
            'job_context' => 'Software Engineer'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'options' => ['Option 1', 'Option 2', 'Option 3']
                 ]);
                 
        $this->assertEquals(1, $user->fresh()->ai_quota_used);
    }

    public function test_basic_user_cannot_generate_parallel_versions(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a test job description that meets length. ', 5)
        ]);

        $response->assertStatus(403)
                 ->assertJson(['success' => false, 'message' => 'Premium feature only.']);
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
            'content' => ['summary' => 'Old summary']
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

        $this->assertEquals(3, $user->fresh()->ai_quota_used);
    }
}
