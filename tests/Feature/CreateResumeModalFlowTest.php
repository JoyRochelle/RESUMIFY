<?php

namespace Tests\Feature;

use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The create-resume modal used to render one form per template with an
 * invisible full-card submit button, so clicking a card created the resume
 * outright: no confirmation step, and a double click could produce two
 * resumes for a Premium account (Basic is only saved by its 1-resume limit).
 */
class CreateResumeModalFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        CvTemplate::factory()->create(['is_active' => true, 'is_premium' => false, 'name' => 'Aperture']);
    }

    public function test_modal_renders_a_single_form_for_all_templates(): void
    {
        CvTemplate::factory()->count(3)->create(['is_active' => true, 'is_premium' => false]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertSame(
            1,
            substr_count($response->getContent(), route('resumes.store')),
            'The modal must post through one form, not one form per template card.'
        );
    }

    public function test_template_cards_are_selectable_inputs_not_submit_buttons(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('type="radio"', false);
        $response->assertSee('name="template_id"', false);
        $response->assertDontSee('aria-label="Use Aperture template"', false);
    }

    public function test_create_button_starts_disabled(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $content = $response->getContent();

        $position = strpos($content, 'id="create-resume-submit"');
        $this->assertNotFalse($position, 'The modal must render a single create button.');
        $this->assertStringContainsString('disabled', substr($content, $position, 200));
    }

    public function test_create_button_exposes_a_loading_label(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('data-loading-label="' . __('messages.resume.create.creating') . '"', false);
        $response->assertSee('id="create-resume-submit-spinner"', false);
        // Spinner must be hidden with an inline style so Material Symbols CSS
        // cannot win on specificity and leave it spinning on page load.
        $response->assertSee('style="display:none"', false);
    }

    public function test_submit_handler_blocks_a_second_submission(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee("submit.dataset.submitting === 'true'", false);
        $response->assertSee("submit.dataset.submitting = 'true'", false);
    }

    public function test_title_field_posts_directly_from_the_visible_input(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $content = $response->getContent();

        $position = strpos($content, 'id="create-resume-title"');
        $this->assertNotFalse($position);
        $this->assertStringContainsString('name="title"', substr($content, $position, 200));
    }

    public function test_locked_premium_template_offers_no_radio(): void
    {
        $basic = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        CvTemplate::factory()->create(['is_active' => true, 'is_premium' => true, 'name' => 'Boardroom']);

        $response = $this->actingAs($basic)->get(route('dashboard'));

        $response->assertOk();
        $content = $response->getContent();

        $cardStart = strpos($content, 'Locked Premium Template');
        $this->assertNotFalse($cardStart, 'A Basic account must still see the locked premium card.');
        $response->assertSee(route('user.upgrade-quota'), false);
    }

    public function test_store_still_rejects_a_missing_template(): void
    {
        $this->actingAs($this->user)
            ->post(route('resumes.store'), ['title' => 'No Template Resume'])
            ->assertSessionHasErrors('template_id');
    }

    public function test_store_still_rejects_a_missing_title(): void
    {
        $template = CvTemplate::factory()->create(['is_active' => true, 'is_premium' => false]);

        $this->actingAs($this->user)
            ->post(route('resumes.store'), ['template_id' => $template->id])
            ->assertSessionHasErrors('title');
    }

    public function test_store_creates_the_resume_when_both_fields_are_present(): void
    {
        $template = CvTemplate::factory()->create(['is_active' => true, 'is_premium' => false]);

        $response = $this->actingAs($this->user)->post(route('resumes.store'), [
            'title'       => 'Staff Engineer Resume',
            'template_id' => $template->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cvs', [
            'user_id' => $this->user->id,
            'title'   => 'Staff Engineer Resume',
        ]);
    }
}
