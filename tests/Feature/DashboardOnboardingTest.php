<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_no_resume_sees_onboarding_message(): void
    {
        $user = User::factory()->create(['role' => 'basic']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
                 ->assertSee('Start your first manuscript')
                 ->assertSee('How Resumify works')
                 ->assertSee('Compose your resume')
                 ->assertDontSee('Your Resumes');
    }

    public function test_user_with_resume_sees_grid_not_onboarding(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = CvTemplate::factory()->create();

        Cv::forceCreate([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Backend Resume',
            'job_target'  => 'Backend Engineer',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
                 ->assertSee('Your Resumes')
                 ->assertSee('Backend Resume')
                 ->assertDontSee('Start your first manuscript');
    }
}
