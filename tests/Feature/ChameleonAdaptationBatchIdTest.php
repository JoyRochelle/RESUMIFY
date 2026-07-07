<?php

namespace Tests\Feature;

use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ChameleonAdaptationBatchIdTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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

    public function test_generated_versions_share_the_same_batch_id(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $cv->sections()->create([
            'type' => 'work_experience',
            'title' => 'Experience',
            'order' => 1,
            'content' => ['bullets' => [str_repeat('Long enough content for testing. ', 10)]],
        ]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateCvVersions')
                ->once()
                ->andReturn([
                    'leadership' => [['type' => 'work_experience', 'content' => ['bullets' => ['Leadership bullet.']]]],
                    'technical' => [['type' => 'work_experience', 'content' => ['bullets' => ['Technical bullet.']]]],
                    'ownership' => [['type' => 'work_experience', 'content' => ['bullets' => ['Ownership bullet.']]]],
                ]);
            $mock->shouldReceive('logUsage')->once();
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a detailed job description intended to exceed fifty characters. ', 2),
        ]);

        $response->assertStatus(200);

        $batchIds = ChameleonAdaptation::where('cv_id', $cv->id)->pluck('batch_id')->unique();

        $this->assertCount(1, $batchIds, 'All 3 generated versions should share one batch_id.');
        $this->assertNotNull($batchIds->first());
    }

    public function test_two_separate_generation_calls_produce_different_batch_ids(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $cv->sections()->create([
            'type' => 'work_experience',
            'title' => 'Experience',
            'order' => 1,
            'content' => ['bullets' => [str_repeat('Long enough content for testing. ', 10)]],
        ]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateCvVersions')
                ->twice()
                ->andReturn([
                    'leadership' => [['type' => 'work_experience', 'content' => ['bullets' => ['Leadership bullet.']]]],
                    'technical' => [['type' => 'work_experience', 'content' => ['bullets' => ['Technical bullet.']]]],
                    'ownership' => [['type' => 'work_experience', 'content' => ['bullets' => ['Ownership bullet.']]]],
                ]);
            $mock->shouldReceive('logUsage')->twice();
        });

        $jobDescription = str_repeat('This is a detailed job description intended to exceed fifty characters. ', 2);

        $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", ['job_description' => $jobDescription])
            ->assertStatus(200);
        $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", ['job_description' => $jobDescription])
            ->assertStatus(200);

        $batchIds = ChameleonAdaptation::where('cv_id', $cv->id)->pluck('batch_id')->unique();

        $this->assertCount(2, $batchIds, 'Two separate generation calls should produce two distinct batch_ids.');
    }
}
