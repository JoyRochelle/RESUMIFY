<?php

namespace Database\Factories;

use App\Models\CvTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvTemplate>
 */
class CvTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->words(3, true),
            'blade_path' => 'templates.default',
            'category'   => fake()->randomElement(['professional', 'creative', 'technology', 'managerial']),
            'is_active'  => true,
            'is_premium' => false,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function premium(): static
    {
        return $this->state(['is_premium' => true]);
    }
}
