<?php

namespace Tests\Unit;

use App\Actions\Resumes\DuplicateResumeAction;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateResumeActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_duplicates_a_resume_with_its_sections_for_the_same_owner(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $template = CvTemplate::factory()->create();

        $resume = Cv::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Backend Resume',
            'job_target' => 'Senior Laravel Engineer',
            'status' => 'draft',
        ]);

        $resume->sections()->createMany([
            [
                'type' => 'personal_info',
                'title' => 'Personal Info',
                'content' => ['name' => 'Kenny'],
                'order' => 1,
            ],
            [
                'type' => 'skills',
                'title' => 'Skills',
                'content' => ['items' => ['Laravel', 'Testing']],
                'order' => 2,
            ],
        ]);

        $copy = app(DuplicateResumeAction::class)->execute($resume);

        $copy->load('sections');

        $this->assertNotSame($resume->id, $copy->id);
        $this->assertSame($user->id, $copy->user_id);
        $this->assertSame('Backend Resume (Copy)', $copy->title);
        $this->assertCount(2, $copy->sections);
        $this->assertSame(['Personal Info', 'Skills'], $copy->sections->pluck('title')->all());
        $this->assertTrue($copy->sections->every(fn ($section) => $section->cv_id === $copy->id));
    }
}
