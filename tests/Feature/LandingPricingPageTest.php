<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPricingPageTest extends TestCase
{
    public function test_pricing_page_loads_with_plan_cards(): void
    {
        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('Starter')
            ->assertSee('Premium PRO');
    }
}
