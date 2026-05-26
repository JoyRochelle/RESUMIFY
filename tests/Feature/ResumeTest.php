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

        $response->assertRedirect('/manuscripts');
        
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
        
        $cv = Cv::create([
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
}
