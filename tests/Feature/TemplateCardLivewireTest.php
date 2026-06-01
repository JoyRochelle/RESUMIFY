<?php

namespace Tests\Feature;

use App\Livewire\Admin\TemplateCard;
use App\Livewire\Admin\TemplateStats;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TemplateCardLivewireTest extends TestCase
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
            'name'       => 'Test Template',
            'category'   => 'professional',
            'is_active'  => true,
            'is_premium' => false,
            'sort_order' => 1,
        ]);
    }

    // =========================================================
    // Component renders
    // =========================================================

    public function test_template_card_component_renders(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->assertSee($this->template->name)
            ->assertSee($this->template->category);
    }

    public function test_template_card_shows_active_status(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->assertSee('Active');
    }

    public function test_template_card_shows_inactive_status_for_inactive_template(): void
    {
        $this->template->update(['is_active' => false]);
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template->fresh()])
            ->assertSee('Inactive');
    }

    public function test_template_card_shows_premium_badge_for_premium_template(): void
    {
        $this->template->update(['is_premium' => true]);
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template->fresh()])
            ->assertSee('Premium');
    }

    public function test_template_card_shows_iframe_preview(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->assertSee(route('admin.templates.preview', $this->template));
    }

    // =========================================================
    // Toggle — no page refresh
    // =========================================================

    public function test_toggle_deactivates_active_template_without_redirect(): void
    {
        $this->assertTrue($this->template->is_active);
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->call('toggle')
            ->assertHasNoErrors();

        $this->assertFalse($this->template->fresh()->is_active);
    }

    public function test_toggle_activates_inactive_template_without_redirect(): void
    {
        $this->template->update(['is_active' => false]);
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template->fresh()])
            ->call('toggle')
            ->assertHasNoErrors();

        $this->assertTrue($this->template->fresh()->is_active);
    }

    public function test_toggle_updates_status_badge_in_component(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->assertSee('Active')
            ->call('toggle')
            ->assertSee('Inactive');
    }

    public function test_toggle_can_be_called_multiple_times(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->call('toggle')   // active → inactive
            ->call('toggle')   // inactive → active
            ->assertHasNoErrors();

        $this->assertTrue($this->template->fresh()->is_active);
    }

    public function test_toggle_dispatches_template_toggled_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->call('toggle')
            ->assertDispatched('template-toggled');
    }

    // =========================================================
    // Delete — Livewire
    // =========================================================

    public function test_delete_removes_template_from_database(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->call('delete')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('cv_templates', ['id' => $this->template->id]);
    }

    public function test_delete_dispatches_template_deleted_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->call('delete')
            ->assertDispatched('template-deleted');
    }

    public function test_delete_removes_thumbnail_from_storage(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('thumb.jpg');
        $path = $file->store('templates', 'public');
        $this->template->update(['thumbnail_url' => $path]);

        Storage::disk('public')->assertExists($path);
        $this->actingAs($this->admin);

        Livewire::test(TemplateCard::class, ['template' => $this->template->fresh()])
            ->call('delete');

        Storage::disk('public')->assertMissing($path);
    }

    // =========================================================
    // Index page uses Livewire cards
    // =========================================================

    public function test_template_index_renders_livewire_card_components(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.index'))
            ->assertOk()
            ->assertSeeLivewire(TemplateCard::class);
    }

    public function test_template_index_shows_no_cards_when_no_templates(): void
    {
        CvTemplate::query()->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.templates.index'))
            ->assertOk()
            ->assertSee('No templates found');
    }

    public function test_template_index_renders_livewire_stats_component(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.templates.index'))
            ->assertSeeLivewire(TemplateStats::class);
    }

    // =========================================================
    // TemplateStats — live counts
    // =========================================================

    public function test_template_stats_shows_correct_counts(): void
    {
        CvTemplate::factory()->create(['is_active' => false]);
        CvTemplate::factory()->premium()->create();
        $this->actingAs($this->admin);

        Livewire::test(TemplateStats::class)
            ->assertSee('2') // total active (setUp creates 1 active, premium is also active)
            ->assertHasNoErrors();
    }

    public function test_template_stats_refreshes_on_template_toggled_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateStats::class)
            ->dispatch('template-toggled')
            ->assertHasNoErrors();
    }

    public function test_template_stats_refreshes_on_template_deleted_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TemplateStats::class)
            ->dispatch('template-deleted')
            ->assertHasNoErrors();
    }

    public function test_template_stats_active_count_updates_after_toggle(): void
    {
        $this->actingAs($this->admin);

        // Toggle the template to inactive
        Livewire::test(TemplateCard::class, ['template' => $this->template])
            ->call('toggle');

        // Stats should now show 0 active
        Livewire::test(TemplateStats::class)
            ->assertSee('0'); // 0 active after toggle
    }
}
