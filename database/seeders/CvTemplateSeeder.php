<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use Illuminate\Database\Seeder;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        CvTemplate::create([
            'name'       => 'Classic Professional',
            'is_premium' => false,
            'is_active'  => true,
        ]);

        CvTemplate::create([
            'name'       => 'Modern Minimal',
            'is_premium' => false,
            'is_active'  => true,
        ]);

        CvTemplate::create([
            'name'       => 'Executive Premium',
            'is_premium' => true,
            'is_active'  => true,
        ]);
    }
}
