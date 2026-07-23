<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageInterviewPersonaTest extends TestCase
{
    public function test_english_landing_page_names_the_interviewer_ms_sarah(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Rehearse with Ms. Sarah')
            ->assertDontSee('Bu Sari');
    }

    public function test_indonesian_landing_page_names_the_interviewer_ms_sarah(): void
    {
        config(['app.fallback_locale' => 'id']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Latihan dengan Ms. Sarah')
            ->assertDontSee('Bu Sari');
    }
}
