<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dates were <input type="month">, whose native picker only steps month by
 * month — reaching a 2005 graduation meant dozens of clicks. They are now a
 * Year select followed by a Month select, writing the same YYYY-MM string into
 * a hidden field so the 23 resume templates and existing rows are untouched.
 */
class ResumeDateSelectTest extends TestCase
{
    use RefreshDatabase;

    private function makeCv(User $user, array $experience = []): Cv
    {
        $template = CvTemplate::factory()->create();

        $cv = Cv::forceCreate([
            'id'          => (string) Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Date Picker Resume',
        ]);

        foreach (['personal_info', 'work_experience', 'education', 'skills', 'target_job', 'certifications'] as $i => $type) {
            $section = $cv->sections()->create([
                'type'    => $type,
                'title'   => Str::headline($type),
                'content' => $type === 'work_experience' && $experience ? $experience : null,
            ]);
            $section->forceFill(['order' => $i + 1])->save();
        }

        return $cv->fresh('sections');
    }

    private function editor(array $experience = []): \Illuminate\Testing\TestResponse
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user, $experience);

        return $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
    }

    public function test_editor_no_longer_renders_native_month_inputs(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $response->assertDontSee('type="month"', false);
    }

    public function test_date_fields_render_year_and_month_selects(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $response->assertSee('data-month-year', false);
        $response->assertSee('data-month-year-year', false);
        $response->assertSee('data-month-year-month', false);
    }

    public function test_year_select_comes_before_month_select(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $content = $response->getContent();

        // Picking the year first is the whole point — a distant year should
        // never require scrolling through months.
        $this->assertLessThan(
            strpos($content, 'data-month-year-month'),
            strpos($content, 'data-month-year-year'),
            'The year select must be rendered before the month select.'
        );
    }

    public function test_only_the_hidden_field_is_named(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $content = $response->getContent();

        $start = strpos($content, 'data-month-year>');
        $this->assertNotFalse($start);
        $block = substr($content, $start, 2600);

        // saveSection() sweeps every named input, textarea and select in a row
        // into the payload, so a named select would pollute the resume content.
        $this->assertStringContainsString('name="start_date"', $block);
        $this->assertStringNotContainsString('<select name=', $block);
    }

    public function test_hidden_field_keeps_the_auto_save_hook(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $response->assertSee('auto-save js-month-year-value', false);
    }

    public function test_saved_value_repopulates_both_selects(): void
    {
        $response = $this->editor([
            ['title' => 'Engineer', 'company' => 'Acme', 'start_date' => '2019-04', 'end_date' => '', 'description' => ''],
        ]);

        $response->assertOk();
        $response->assertSee('value="2019-04"', false);
        $response->assertSee('<option value="2019" selected>2019</option>', false);
        $response->assertSee('<option value="04" selected>' . __('messages.editor.date_picker.months.04') . '</option>', false);
    }

    public function test_a_year_outside_the_default_range_stays_selectable(): void
    {
        $response = $this->editor([
            ['title' => 'Apprentice', 'company' => 'Old Corp', 'start_date' => '1950-03', 'end_date' => '', 'description' => ''],
        ]);

        $response->assertOk();
        // Dropping it from the list would silently erase the user's date the
        // next time the section auto-saves.
        $response->assertSee('<option value="1950" selected>1950</option>', false);
    }

    public function test_default_range_spans_recent_and_long_past_years(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $currentYear = (int) date('Y');

        $response->assertSee('<option value="' . $currentYear . '"', false);
        $response->assertSee('<option value="' . ($currentYear + 5) . '"', false);
        $response->assertSee('<option value="' . ($currentYear - 60) . '"', false);
    }

    public function test_month_names_exist_in_both_locales(): void
    {
        $en = require lang_path('en/messages.php');
        $id = require lang_path('id/messages.php');

        $this->assertCount(12, $en['editor']['date_picker']['months']);
        $this->assertCount(12, $id['editor']['date_picker']['months']);
        $this->assertSame('January', $en['editor']['date_picker']['months']['01']);
        $this->assertSame('Januari', $id['editor']['date_picker']['months']['01']);
        $this->assertSame('Tahun', $id['editor']['date_picker']['year']);
    }

    public function test_partial_selection_never_produces_a_half_date(): void
    {
        $source = file_get_contents(resource_path('views/components/ui/month-year-input.blade.php'));

        $this->assertStringContainsString('(year && month)', $source);
        $this->assertStringContainsString("new Event('input', { bubbles: true })", $source);
    }
}
