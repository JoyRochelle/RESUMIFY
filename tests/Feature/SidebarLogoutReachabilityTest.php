<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The desktop sidebars are `h-screen` columns whose nav list used to be
 * `flex-1` without `min-h-0`. A flex child cannot shrink below its content
 * height without `min-h-0`, so on short viewports (a 768px laptop at 110-125%
 * browser zoom, or a vertically resized window) the nav pushed the footer —
 * locale switcher and Log Out — past the bottom edge of a non-scrollable
 * container. Users had to zoom out to reach Log Out at all.
 */
class SidebarLogoutReachabilityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user  = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    public function test_user_sidebar_nav_can_shrink_and_scroll(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col space-y-2', false);
    }

    public function test_admin_sidebar_nav_can_shrink_and_scroll(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col space-y-2', false);
    }

    public function test_user_sidebar_footer_never_shrinks(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        // The block holding the locale switcher and Log Out must keep its full
        // height so the nav list absorbs the shortfall instead.
        $response->assertSee('shrink-0 pt-6 border-t border-primary/10 space-y-4', false);
    }

    public function test_admin_sidebar_footer_never_shrinks(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('mt-auto shrink-0 border-t border-primary/10 pt-6', false);
    }

    public function test_user_sidebar_aside_scrolls_as_a_last_resort(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('h-screen w-64 overflow-y-auto', false);
    }

    public function test_admin_sidebar_aside_scrolls_as_a_last_resort(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('h-screen w-64 shrink-0 flex-col space-y-8 overflow-y-auto', false);
    }

    public function test_logout_control_is_present_in_both_sidebars(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('logout'), false);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('logout'), false);
    }
}
