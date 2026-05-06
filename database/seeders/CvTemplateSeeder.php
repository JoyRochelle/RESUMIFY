<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use Illuminate\Database\Seeder;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        CvTemplate::create([
            'name'         => 'Eleanor Vance',
            'blade_path'   => 'templates.eleanor-vance',
            'category'     => 'professional',
            'badge'        => 'ATS OK',
            'badge_color'  => 'blue',
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 1,
            'style_config' => [
                'primary_color'    => '#2563EB',
                'secondary_color'  => '#1E40AF',
                'background_color' => '#ffffff',
                'font_heading'     => 'Playfair Display',
                'font_body'        => 'Inter'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'The Creative',
            'blade_path'   => 'templates.the-creative',
            'category'     => 'creative',
            'badge'        => 'BEST SELLER',
            'badge_color'  => 'secondary',
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 2,
            'style_config' => [
                'primary_color'    => '#f43f5e',
                'secondary_color'  => '#ffe4e6',
                'background_color' => '#f8fafc',
                'font_heading'     => 'Poppins',
                'font_body'        => 'Poppins',
                'show_photo'       => true
            ]
        ]);

        CvTemplate::create([
            'name'         => 'The Architect',
            'blade_path'   => 'templates.the-architect',
            'category'     => 'technology',
            'badge'        => null,
            'badge_color'  => null,
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 3,
            'style_config' => [
                'primary_color'    => '#0ea5e9',
                'secondary_color'  => '#f1f5f9',
                'background_color' => '#ffffff',
                'font_heading'     => 'Roboto Mono',
                'font_body'        => 'Roboto',
                'show_photo'       => true
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Standard Ivory',
            'blade_path'   => 'templates.standard-ivory',
            'category'     => 'professional',
            'badge'        => null,
            'badge_color'  => null,
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 4,
            'style_config' => [
                'primary_color'    => '#111111',
                'secondary_color'  => '#333333',
                'background_color' => '#faf9f6',
                'font_heading'     => 'Lora',
                'font_body'        => 'Open Sans'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Modern Visual',
            'blade_path'   => 'templates.modern-visual',
            'category'     => 'creative',
            'badge'        => 'AI ENHANCED',
            'badge_color'  => 'purple',
            'is_premium'   => true,
            'is_active'    => true,
            'sort_order'   => 5,
            'style_config' => [
                'primary_color'    => '#10b981',
                'secondary_color'  => '#e2e8f0',
                'background_color' => '#ffffff',
                'font_heading'     => 'Montserrat',
                'font_body'        => 'Lato',
                'show_photo'       => true
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Sidebar Minimal',
            'blade_path'   => 'templates.sidebar-minimal',
            'category'     => 'managerial',
            'badge'        => null,
            'badge_color'  => null,
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 6,
            'style_config' => [
                'primary_color'    => '#3b82f6',
                'secondary_color'  => '#1e293b',
                'background_color' => '#ffffff',
                'font_heading'     => 'Source Sans Pro',
                'font_body'        => 'Source Sans Pro'
            ]
        ]);
    }
}
