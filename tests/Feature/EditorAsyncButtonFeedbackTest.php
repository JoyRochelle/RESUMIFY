<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Apply-version and restore-snapshot both fire a request and then reload the
 * page. Apply set `disabled` without changing a single pixel, and restore did
 * not lock anything at all — so a user on a slow connection could fire several
 * restores in a row, each writing another pre_restore snapshot.
 */
class EditorAsyncButtonFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private function makeCv(User $user): Cv
    {
        $template = CvTemplate::factory()->create();

        $cv = Cv::forceCreate([
            'id'          => (string) Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Async Feedback Resume',
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

    private function editor(): \Illuminate\Testing\TestResponse
    {
        $user = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->makeCv($user);

        return $this->actingAs($user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
    }

    public function test_apply_modal_cancel_button_is_addressable(): void
    {
        $response = $this->editor();

        $response->assertOk();
        // The apply handler disables Cancel too, so it needs an id to reach.
        $response->assertSee('id="apply-version-cancel-btn"', false);
    }

    public function test_restore_confirmation_modal_is_rendered(): void
    {
        $response = $this->editor();

        $response->assertOk();
        $response->assertSee('id="restore-version-modal"', false);
        $response->assertSee('id="restore-version-confirm-btn"', false);
        $response->assertSee(__('messages.editor.restore_version_title'));
    }

    public function test_loading_labels_reach_the_editor_javascript(): void
    {
        $response = $this->editor();

        $response->assertOk();
        // resume-editor.js cannot call __(), so every string it renders has to
        // travel through window.editorConfig.i18n.
        $response->assertSee('window.editorConfig', false);
        $response->assertSee('applying', false);
        $response->assertSee('restoring', false);
    }

    public function test_applying_and_restoring_labels_exist_in_both_locales(): void
    {
        $en = require lang_path('en/messages.php');
        $id = require lang_path('id/messages.php');

        foreach (['applying', 'restoring'] as $key) {
            $this->assertArrayHasKey($key, $en['editor']['js'], "Missing en editor.js.{$key}");
            $this->assertArrayHasKey($key, $id['editor']['js'], "Missing id editor.js.{$key}");
        }

        foreach (['restore_version_title', 'restore_version_desc', 'restore_and_overwrite'] as $key) {
            $this->assertArrayHasKey($key, $en['editor'], "Missing en editor.{$key}");
            $this->assertArrayHasKey($key, $id['editor'], "Missing id editor.{$key}");
        }
    }

    public function test_editor_javascript_no_longer_uses_a_native_confirm(): void
    {
        $source = file_get_contents(resource_path('js/features/resume-editor.js'));

        $this->assertStringNotContainsString(
            'confirm(t.confirm_restore)',
            $source,
            'Restore must use the in-app confirmation modal, not the browser confirm() dialog.'
        );
    }

    public function test_restore_locks_every_restore_button_while_in_flight(): void
    {
        $source = file_get_contents(resource_path('js/features/resume-editor.js'));

        $this->assertStringContainsString('setButtonLoading', $source);
        $this->assertStringContainsString('resetButtonLoading', $source);
        $this->assertStringContainsString('data-restore-btn', $source);
    }
}
