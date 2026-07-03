<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cv;
use App\Models\CvTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeTest extends TestCase
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

    public function test_authenticated_user_can_view_resumes_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'basic']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_a_resume(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = CvTemplate::first();

        $response = $this->actingAs($user)->post('/resumes', [
            'title' => 'My First Resume',
            'template_id' => $template->id,
        ]);

        $cv = Cv::where('title', 'My First Resume')->first();
        $response->assertRedirect("/manuscripts?cv_id={$cv->id}");
        
        $this->assertDatabaseHas('cvs', [
            'user_id' => $user->id,
            'title' => 'My First Resume',
            'template_id' => $template->id,
        ]);

        // Check if default sections were created
        $cv = Cv::where('title', 'My First Resume')->first();
        $this->assertCount(5, $cv->sections);
    }

    public function test_user_can_update_resume_title(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = CvTemplate::first();
        
        $cv = Cv::forceCreate([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Old Title',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->put("/resumes/{$cv->id}", [
            'title' => 'New Title',
        ]);

        $response->assertRedirect("/manuscripts?cv_id={$cv->id}");
        $this->assertDatabaseHas('cvs', [
            'id' => $cv->id,
            'title' => 'New Title',
        ]);
    }

    public function test_basic_user_cannot_duplicate_resume_after_reaching_resume_limit(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = CvTemplate::first();

        $cv = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Quota Limited Resume',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->post(route('resumes.duplicate', $cv));

        $response->assertRedirect(route('user.upgrade-quota'));
        $response->assertSessionHas('error', 'Basic accounts can create 1 resume. Upgrade to Premium for unlimited resumes.');

        $this->assertSame(1, $user->cvs()->count());
        $this->assertDatabaseMissing('cvs', [
            'user_id' => $user->id,
            'title' => 'Quota Limited Resume (Copy)',
        ]);
    }

    public function test_premium_user_can_duplicate_resume_with_sections(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $template = CvTemplate::first();

        $cv = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Premium Resume',
            'job_target' => 'Senior Laravel Engineer',
            'company_target' => 'Resumify',
            'status' => 'draft',
        ]);

        $cv->sections()->createMany([
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

        $response = $this->actingAs($user)->post(route('resumes.duplicate', $cv));

        $copy = Cv::where('user_id', $user->id)
            ->where('title', 'Premium Resume (Copy)')
            ->first();

        $this->assertNotNull($copy);
        $response->assertRedirect(route('user.manuscript', ['cv_id' => $copy->id]));
        $response->assertSessionHas('success', 'Resume duplicated successfully!');

        $copy->load('sections');

        $this->assertSame($user->id, $copy->user_id);
        $this->assertCount(2, $copy->sections);
        $this->assertSame(
            ['Personal Info', 'Skills'],
            $copy->sections->pluck('title')->all()
        );
        $this->assertTrue(
            $copy->sections->every(fn ($section) => $section->cv_id === $copy->id)
        );
        $this->assertDatabaseCount('cvs', 2);
        $this->assertDatabaseCount('cv_sections', 4);
    }
}
