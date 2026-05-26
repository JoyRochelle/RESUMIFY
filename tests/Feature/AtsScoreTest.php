<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\AtsScoreService;

class AtsScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_ats_score_is_calculated_correctly(): void
    {
        $user = User::factory()->create();
        
        $template = CvTemplate::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'name' => 'Default',
            'blade_path' => 'templates.default',
        ]);

        $cv = Cv::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'My Resume',
        ]);

        // Add Target Job
        $cv->sections()->create([
            'type' => 'target_job',
            'title' => 'Target Job',
            'order' => 1,
            'content' => [
                'job_title' => 'Software Engineer',
                'job_description' => 'Looking for a Software Engineer with PHP, Laravel, and MySQL experience.'
            ]
        ]);

        // Add Personal Info (Summary)
        $cv->sections()->create([
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 2,
            'content' => [
                'summary' => 'Experienced software engineer skilled in PHP and Laravel.'
            ]
        ]);

        // Add Work Experience
        $cv->sections()->create([
            'type' => 'work_experience',
            'title' => 'Work Experience',
            'order' => 3,
            'content' => [
                ['company' => 'Tech Corp', 'role' => 'Developer', 'description' => 'Worked with MySQL and PHP.']
            ]
        ]);

        $score = AtsScoreService::calculate($cv);

        // Calculate expected:
        // Job words: software, engineer, php, laravel, mysql, experience, looking (maybe stopwords removed)
        // Resume words: software, engineer, skilled, php, laravel, tech, corp, developer, worked, mysql
        // Match should be quite high
        $this->assertGreaterThan(50, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}
