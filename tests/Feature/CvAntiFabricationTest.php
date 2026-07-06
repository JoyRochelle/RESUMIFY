<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CvAntiFabricationTest extends TestCase
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

    public function test_generate_versions_flags_fabricated_content_with_warning(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        $cv->sections()->create([
            'type' => 'work_experience',
            'title' => 'Experience',
            'order' => 1,
            'content' => [
                [
                    'company' => 'Acme Corp',
                    'bullets' => [
                        str_repeat('Maintained internal tools using PHP and Laravel. ', 5),
                    ],
                ],
            ],
        ]);

        $this->mock(AiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateCvVersions')
                ->once()
                ->andReturn([
                    'leadership' => [
                        [
                            'type' => 'work_experience',
                            'content' => [
                                [
                                    'company' => 'Acme Corp',
                                    'bullets' => ['Partnered with engineers at Google, boosting revenue by 45%.'],
                                ],
                            ],
                        ],
                    ],
                    'technical' => [
                        [
                            'type' => 'work_experience',
                            'content' => [
                                ['company' => 'Acme Corp', 'bullets' => ['Maintained internal tools using PHP and Laravel.']],
                            ],
                        ],
                    ],
                    'ownership' => [
                        [
                            'type' => 'work_experience',
                            'content' => [
                                ['company' => 'Acme Corp', 'bullets' => ['Maintained internal tools using PHP and Laravel.']],
                            ],
                        ],
                    ],
                ]);
            $mock->shouldReceive('logUsage')->once();
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a detailed job description intended to exceed fifty characters. ', 2),
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $versions = collect($response->json('versions'));

        $leadership = $versions->firstWhere('angle', 'leadership');
        $this->assertNotNull($leadership['warning'], 'Fabricated version should carry a warning.');
        $this->assertContains('Google', $leadership['warning']['entities']);
        $this->assertContains('45', $leadership['warning']['numbers']);

        $technical = $versions->firstWhere('angle', 'technical');
        $this->assertNull($technical['warning'], 'Clean rewrite should not carry a warning.');
    }
}
