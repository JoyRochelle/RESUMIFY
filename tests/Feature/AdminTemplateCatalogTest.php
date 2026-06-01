<?php

namespace Tests\Feature;

use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTemplateCatalogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;
    private CvTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->create(['role' => 'admin',  'email_verified_at' => now()]);
        $this->basicUser = User::factory()->create(['role' => 'basic',  'email_verified_at' => now()]);

        $this->template = CvTemplate::factory()->create([
            'name'      => 'Minimal Classic',
            'category'  => 'professional',
            'is_active' => true,
            'is_premium' => false,
            'sort_order' => 1,
        ]);
    }

    // =========================================================
    // Access Control
    // =========================================================

    public function test_admin_can_access_template_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.index'))
            ->assertOk()
            ->assertViewIs('admin.templates.index');
    }

    public function test_basic_user_cannot_access_template_index(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.templates.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_template_index(): void
    {
        $this->get(route('admin.templates.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_access_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.create'))
            ->assertOk()
            ->assertViewIs('admin.templates.create');
    }

    public function test_admin_can_access_edit_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.edit', $this->template))
            ->assertOk()
            ->assertViewIs('admin.templates.edit');
    }

    public function test_basic_user_cannot_access_create_form(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.templates.create'))
            ->assertForbidden();
    }

    public function test_basic_user_cannot_access_edit_form(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.templates.edit', $this->template))
            ->assertForbidden();
    }

    // =========================================================
    // Index — view variables
    // =========================================================

    public function test_index_passes_required_variables(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.index'))
            ->assertViewHasAll(['templates', 'activeCount', 'inactiveCount', 'premiumCount', 'search', 'category', 'status']);
    }

    public function test_active_count_reflects_active_templates(): void
    {
        CvTemplate::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin)->get(route('admin.templates.index'));

        // setUp creates 1 active; this adds 1 inactive
        $this->assertEquals(1, $response->viewData('activeCount'));
        $this->assertEquals(1, $response->viewData('inactiveCount'));
    }

    public function test_premium_count_reflects_premium_templates(): void
    {
        CvTemplate::factory()->premium()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.templates.index'));

        $this->assertEquals(1, $response->viewData('premiumCount'));
    }

    public function test_index_paginates_at_10(): void
    {
        CvTemplate::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.templates.index'));

        $this->assertLessThanOrEqual(10, $response->viewData('templates')->count());
    }

    // =========================================================
    // Index — filters
    // =========================================================

    public function test_search_filter_by_name(): void
    {
        CvTemplate::factory()->create(['name' => 'Unique XYZ Template']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.templates.index', ['search' => 'XYZ']));

        $names = $response->viewData('templates')->pluck('name');
        $this->assertTrue($names->contains('Unique XYZ Template'));
        $this->assertFalse($names->contains('Minimal Classic'));
    }

    public function test_category_filter_returns_matching_templates(): void
    {
        CvTemplate::factory()->create(['category' => 'creative', 'name' => 'Creative One']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.templates.index', ['category' => 'creative']));

        $response->viewData('templates')->each(
            fn($t) => $this->assertEquals('creative', $t->category)
        );
    }

    public function test_status_filter_active_returns_only_active(): void
    {
        CvTemplate::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.templates.index', ['status' => 'active']));

        $response->viewData('templates')->each(
            fn($t) => $this->assertTrue($t->is_active)
        );
    }

    public function test_status_filter_inactive_returns_only_inactive(): void
    {
        CvTemplate::factory()->inactive()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.templates.index', ['status' => 'inactive']));

        $response->viewData('templates')->each(
            fn($t) => $this->assertFalse($t->is_active)
        );
    }

    // =========================================================
    // Create / Store
    // =========================================================

    public function test_admin_can_create_template(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'Modern Pro',
                'blade_path' => 'templates.modern-pro',
                'category'   => 'professional',
                'sort_order' => 5,
            ])
            ->assertRedirect(route('admin.templates.index'));

        $this->assertDatabaseHas('cv_templates', [
            'name'       => 'Modern Pro',
            'blade_path' => 'templates.modern-pro',
            'category'   => 'professional',
        ]);
    }

    public function test_store_redirects_with_success_flash(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'Flash Test',
                'blade_path' => 'templates.flash',
                'category'   => 'creative',
            ])
            ->assertSessionHas('success');
    }

    public function test_store_sets_is_active_default_false_when_unchecked(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'Inactive Template',
                'blade_path' => 'templates.inactive',
                'category'   => 'technology',
                'is_active'  => '0',
            ]);

        $this->assertDatabaseHas('cv_templates', [
            'name'      => 'Inactive Template',
            'is_active' => false,
        ]);
    }

    public function test_store_sets_is_premium_when_checked(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'Premium Template',
                'blade_path' => 'templates.premium',
                'category'   => 'managerial',
                'is_premium' => '1',
            ]);

        $this->assertDatabaseHas('cv_templates', [
            'name'       => 'Premium Template',
            'is_premium' => true,
        ]);
    }

    public function test_store_requires_name(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => '',
                'blade_path' => 'templates.test',
                'category'   => 'professional',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_store_requires_blade_path(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'Test Template',
                'blade_path' => '',
                'category'   => 'professional',
            ])
            ->assertSessionHasErrors('blade_path');
    }

    public function test_store_requires_valid_category(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'Test Template',
                'blade_path' => 'templates.test',
                'category'   => 'invalid_category',
            ])
            ->assertSessionHasErrors('category');
    }

    public function test_store_rejects_invalid_badge_color(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'        => 'Test Template',
                'blade_path'  => 'templates.test',
                'category'    => 'professional',
                'badge_color' => 'rainbow',
            ])
            ->assertSessionHasErrors('badge_color');
    }

    public function test_store_rejects_invalid_style_config_json(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'         => 'Test Template',
                'blade_path'   => 'templates.test',
                'category'     => 'professional',
                'style_config' => 'not-valid-json{',
            ])
            ->assertSessionHasErrors('style_config');
    }

    public function test_store_accepts_valid_style_config_json(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'         => 'JSON Template',
                'blade_path'   => 'templates.json',
                'category'     => 'professional',
                'style_config' => '{"primary_color":"#4f3b2f"}',
            ])
            ->assertRedirect(route('admin.templates.index'));

        $this->assertDatabaseHas('cv_templates', ['name' => 'JSON Template']);
    }

    public function test_store_with_thumbnail_upload(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.templates.store'), [
                'name'       => 'With Thumbnail',
                'blade_path' => 'templates.thumb',
                'category'   => 'professional',
                'thumbnail'  => UploadedFile::fake()->image('thumb.jpg', 300, 400),
            ])
            ->assertRedirect(route('admin.templates.index'));

        $template = CvTemplate::where('name', 'With Thumbnail')->first();
        $this->assertNotNull($template->thumbnail_url);
        Storage::disk('public')->assertExists($template->thumbnail_url);
    }

    public function test_basic_user_cannot_store_template(): void
    {
        $this->actingAs($this->basicUser)
            ->post(route('admin.templates.store'), [
                'name'       => 'Sneaky',
                'blade_path' => 'templates.sneaky',
                'category'   => 'professional',
            ])
            ->assertForbidden();
    }

    // =========================================================
    // Update
    // =========================================================

    public function test_admin_can_update_template(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.templates.update', $this->template), [
                'name'       => 'Updated Name',
                'blade_path' => $this->template->blade_path,
                'category'   => 'creative',
            ])
            ->assertRedirect(route('admin.templates.index'));

        $this->assertEquals('Updated Name', $this->template->fresh()->name);
        $this->assertEquals('creative', $this->template->fresh()->category);
    }

    public function test_update_redirects_with_success_flash(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.templates.update', $this->template), [
                'name'       => 'Updated',
                'blade_path' => $this->template->blade_path,
                'category'   => 'professional',
            ])
            ->assertSessionHas('success');
    }

    public function test_update_replaces_thumbnail(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->put(route('admin.templates.update', $this->template), [
                'name'       => $this->template->name,
                'blade_path' => $this->template->blade_path,
                'category'   => $this->template->category,
                'thumbnail'  => UploadedFile::fake()->image('new.png', 300, 400),
            ]);

        $this->assertNotNull($this->template->fresh()->thumbnail_url);
        Storage::disk('public')->assertExists($this->template->fresh()->thumbnail_url);
    }

    public function test_update_requires_name(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.templates.update', $this->template), [
                'name'       => '',
                'blade_path' => $this->template->blade_path,
                'category'   => 'professional',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_basic_user_cannot_update_template(): void
    {
        $this->actingAs($this->basicUser)
            ->put(route('admin.templates.update', $this->template), [
                'name'       => 'Sneaky Update',
                'blade_path' => $this->template->blade_path,
                'category'   => 'professional',
            ])
            ->assertForbidden();
    }

    // =========================================================
    // Toggle
    // =========================================================

    public function test_admin_can_toggle_template_active_status(): void
    {
        $this->assertTrue($this->template->is_active);

        $this->actingAs($this->admin)
            ->patch(route('admin.templates.toggle', $this->template))
            ->assertRedirect();

        $this->assertFalse($this->template->fresh()->is_active);
    }

    public function test_toggle_switches_inactive_to_active(): void
    {
        $this->template->update(['is_active' => false]);

        $this->actingAs($this->admin)
            ->patch(route('admin.templates.toggle', $this->template));

        $this->assertTrue($this->template->fresh()->is_active);
    }

    public function test_toggle_redirects_with_success_flash(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.templates.toggle', $this->template))
            ->assertSessionHas('success');
    }

    public function test_basic_user_cannot_toggle_template(): void
    {
        $this->actingAs($this->basicUser)
            ->patch(route('admin.templates.toggle', $this->template))
            ->assertForbidden();
    }

    // =========================================================
    // Delete
    // =========================================================

    public function test_admin_can_delete_template(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('admin.templates.destroy', $this->template))
            ->assertRedirect(route('admin.templates.index'));

        $this->assertDatabaseMissing('cv_templates', ['id' => $this->template->id]);
    }

    public function test_delete_removes_thumbnail_from_storage(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('old.jpg');
        $path = $file->store('templates', 'public');
        $this->template->update(['thumbnail_url' => $path]);

        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->admin)
            ->delete(route('admin.templates.destroy', $this->template));

        Storage::disk('public')->assertMissing($path);
    }

    public function test_delete_redirects_with_success_flash(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('admin.templates.destroy', $this->template))
            ->assertSessionHas('success');
    }

    public function test_basic_user_cannot_delete_template(): void
    {
        $this->actingAs($this->basicUser)
            ->delete(route('admin.templates.destroy', $this->template))
            ->assertForbidden();
    }

    // =========================================================
    // Edit form — view data
    // =========================================================

    public function test_edit_passes_template_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.edit', $this->template))
            ->assertViewHas('template', $this->template);
    }

    public function test_edit_shows_template_name_in_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.edit', $this->template))
            ->assertSee($this->template->name);
    }
}
