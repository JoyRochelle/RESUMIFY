<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * On mobile the Tailor control used to render as an icon-only button whose
 * label lived solely in aria-label/title — neither of which a touch screen
 * ever shows. Users saw a bare sparkle icon with no way to learn what it did.
 */
class EditorMobileHeaderTest extends TestCase
{
    use RefreshDatabase;

    private function makeCv(User $user): Cv
    {
        $template = CvTemplate::factory()->create();

        $cv = Cv::forceCreate([
            'id'          => (string) Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Mobile Header Resume',
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

    public function test_tailor_button_carries_a_visible_label(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();
        $response->assertSee(__('messages.editor.tailor_cv'));
    }

    public function test_tailor_button_is_not_hidden_below_the_sm_breakpoint(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();
        $content = $response->getContent();

        // Locate the tailor control and confirm it is not carrying a class that
        // removes it from the mobile layout.
        $position = strpos($content, 'data-tour="editor-tailor"');
        $this->assertNotFalse($position, 'The editor header must render a tailor control.');

        $tag = substr($content, strrpos(substr($content, 0, $position), '<'), 400);
        $this->assertStringNotContainsString('max-sm:hidden', $tag);
    }

    public function test_editor_header_renders_exactly_one_tailor_control(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();

        // Two variants (icon-only for mobile, labelled for desktop) put two
        // entry points to the same modal in the DOM and made the guided tour
        // pick whichever came first rather than whichever was on screen.
        $this->assertSame(
            1,
            substr_count($response->getContent(), 'data-tour="editor-tailor"'),
            'The editor header must expose a single tailor control across all breakpoints.'
        );
    }

    public function test_tailor_control_opens_the_versions_modal(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();
        $response->assertSee('openCvVersionsModal()', false);
    }
}
