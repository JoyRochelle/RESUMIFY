<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Once PDF export became free for every plan, "Premium PDF Export" on the
 * pricing and upgrade pages was a promise the product no longer keeps.
 */
class PdfExportCopyTest extends TestCase
{
    use RefreshDatabase;

    private function makeCv(User $user, bool $premiumTemplate): Cv
    {
        $template = CvTemplate::factory()->create(['is_premium' => $premiumTemplate, 'is_active' => true]);

        $cv = Cv::forceCreate([
            'id'          => (string) Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Copy Test Resume',
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

    public function test_basic_user_sees_an_active_download_button_on_a_free_template(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user, premiumTemplate: false);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();
        $response->assertSee('id="download-btn"', false);
    }

    public function test_basic_user_sees_the_lock_on_a_premium_template(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user, premiumTemplate: true);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();
        $response->assertDontSee('id="download-btn"', false);
        $response->assertSee(__('messages.editor.premium_pdf_title'));
    }

    public function test_premium_user_sees_an_active_download_button_on_a_premium_template(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user, premiumTemplate: true);

        $response = $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));

        $response->assertOk();
        $response->assertSee('id="download-btn"', false);
    }

    public function test_pricing_page_no_longer_sells_pdf_export(): void
    {
        $response = $this->get(route('pricing'));

        $response->assertOk();
        $response->assertDontSee('Premium PDF Export');
        $response->assertSee(__('messages.landing.pricing.plans.premium_template_library'));
    }

    public function test_upgrade_page_no_longer_sells_pdf_export(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertDontSee('Premium PDF Export');
        $response->assertSee(__('messages.upgrade_quota.plans.premium_template_library'));
    }

    public function test_the_retired_lang_key_is_gone_from_both_locales(): void
    {
        $en = require lang_path('en/messages.php');
        $id = require lang_path('id/messages.php');

        $this->assertArrayNotHasKey('premium_pdf_export', $en['landing']['pricing']['plans']);
        $this->assertArrayNotHasKey('premium_pdf_export', $id['landing']['pricing']['plans']);
        $this->assertArrayHasKey('premium_template_library', $en['upgrade_quota']['plans']);
        $this->assertArrayHasKey('premium_template_library', $id['upgrade_quota']['plans']);
    }
}
