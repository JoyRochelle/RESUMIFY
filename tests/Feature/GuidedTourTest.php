<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuidedTourTest extends TestCase
{
    use RefreshDatabase;

    private function makeCv(User $user, string $title = 'Backend Resume'): Cv
    {
        $template = CvTemplate::factory()->create();

        $cv = Cv::forceCreate([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => $title,
            'job_target'  => 'Backend Engineer',
        ]);

        foreach (['personal_info', 'work_experience', 'education', 'skills', 'target_job'] as $i => $type) {
            $section = $cv->sections()->create([
                'type'    => $type,
                'title'   => Str::headline($type),
                'content' => null,
            ]);
            $section->forceFill(['order' => $i + 1])->save();
        }

        return $cv->fresh('sections');
    }

    public function test_dashboard_ships_a_tour_pointing_at_its_own_controls(): void
    {
        $user = User::factory()->create(['role' => 'basic']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
                 ->assertSee('data-tour-root', false)
                 ->assertSee('window.guidedTourConfig', false)
                 ->assertSee('id="tour-title-dashboard"', false)
                 ->assertSee('data-tour="dashboard-create"', false)
                 ->assertSee('Welcome to Resumify');
    }

    public function test_resume_editor_tour_walks_through_every_section(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $cv = $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertStatus(200)
                 ->assertSee('id="tour-title-editor"', false);

        // Each editor section the user must fill has an anchor the tour targets.
        foreach ([
            'section-target-job',
            'section-personal-info',
            'section-work-experience',
            'section-education',
            'section-skills',
            'editor-refine',
            'editor-tailor',
            'editor-export',
        ] as $anchor) {
            $response->assertSee('data-tour="' . $anchor . '"', false);
        }
    }

    public function test_ats_page_ships_its_own_tour(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.ai-assistant'));

        $response->assertStatus(200)
                 ->assertSee('id="tour-title-ats"', false)
                 ->assertSee('data-tour="ats-cv"', false)
                 ->assertSee('data-tour="ats-analyze"', false);
    }

    public function test_interview_page_ships_its_own_tour(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('interview.index'));

        $response->assertStatus(200)
                 ->assertSee('id="tour-title-interview"', false)
                 ->assertSee('data-tour="interview-cv"', false)
                 ->assertSee('data-tour="interview-position"', false);
    }

    public function test_manuscripts_index_ships_its_own_tour(): void
    {
        $user = User::factory()->create(['role' => 'basic']);

        $response = $this->actingAs($user)->get(route('user.manuscript'));

        $response->assertStatus(200)
                 ->assertSee('id="tour-title-manuscripts"', false)
                 ->assertSee('data-tour="manuscripts-create"', false);
    }

    public function test_every_page_with_a_tour_offers_a_button_to_replay_it(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $cv = $this->makeCv($user);

        foreach ([
            route('dashboard'),
            route('user.manuscript'),
            route('user.manuscript', ['cv_id' => $cv->id]),
            route('user.ai-assistant'),
            route('interview.index'),
        ] as $url) {
            $this->actingAs($user)->get($url)
                 ->assertStatus(200)
                 ->assertSee('data-tour-trigger', false);
        }
    }

    public function test_tour_copy_is_translated_for_indonesian_users(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'locale' => 'id']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200)
                 ->assertSee('Selamat datang di Resumify')
                 ->assertSee('Lewati tutorial')
                 ->assertDontSee('Welcome to Resumify');
    }

    public function test_editor_tour_steps_carry_titles_and_bodies_not_raw_keys(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $cv = $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertStatus(200)
                 ->assertDontSee('messages.tour.editor.steps', false);
    }
}
