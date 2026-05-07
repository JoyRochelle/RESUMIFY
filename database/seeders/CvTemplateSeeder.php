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
            'category'     => 'startup_local',
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
            'category'     => 'bumn_government',
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

        CvTemplate::create([
            'name'         => 'BUMN Official',
            'blade_path'   => 'templates.bumn-standard',
            'category'     => 'bumn_government',
            'badge'        => 'ATS OK',
            'badge_color'  => 'blue',
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 7,
            'style_config' => [
                'primary_color'    => '#000000',
                'secondary_color'  => '#f3f4f6',
                'background_color' => '#ffffff',
                'font_heading'     => 'Arial',
                'font_body'        => 'Arial'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Government Pro',
            'blade_path'   => 'templates.government-pro',
            'category'     => 'bumn_government',
            'badge'        => null,
            'badge_color'  => null,
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 8,
            'style_config' => [
                'primary_color'    => '#1e40af',
                'secondary_color'  => '#dbeafe',
                'background_color' => '#ffffff',
                'font_heading'     => 'Times New Roman',
                'font_body'        => 'Times New Roman'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Tech Startup',
            'blade_path'   => 'templates.tech-startup',
            'category'     => 'startup_local',
            'badge'        => 'NEW',
            'badge_color'  => 'green',
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 9,
            'style_config' => [
                'primary_color'    => '#8b5cf6',
                'secondary_color'  => '#ede9fe',
                'background_color' => '#ffffff',
                'font_heading'     => 'Inter',
                'font_body'        => 'Inter'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Agile Developer',
            'blade_path'   => 'templates.agile-dev',
            'category'     => 'startup_local',
            'badge'        => null,
            'badge_color'  => null,
            'is_premium'   => false,
            'is_active'    => true,
            'sort_order'   => 10,
            'style_config' => [
                'primary_color'    => '#f59e0b',
                'secondary_color'  => '#fef3c7',
                'background_color' => '#ffffff',
                'font_heading'     => 'Fira Code',
                'font_body'        => 'Open Sans'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Corporate Elite',
            'blade_path'   => 'templates.corporate-elite',
            'category'     => 'professional',
            'badge'        => null,
            'badge_color'  => null,
            'is_premium'   => true,
            'is_active'    => true,
            'sort_order'   => 11,
            'style_config' => [
                'primary_color'    => '#0f172a',
                'secondary_color'  => '#e2e8f0',
                'background_color' => '#f8fafc',
                'font_heading'     => 'Merriweather',
                'font_body'        => 'Lato'
            ]
        ]);

        CvTemplate::create([
            'name'         => 'Portfolio Hero',
            'blade_path'   => 'templates.portfolio-hero',
            'category'     => 'creative',
            'badge'        => 'BEST SELLER',
            'badge_color'  => 'secondary',
            'is_premium'   => true,
            'is_active'    => true,
            'sort_order'   => 12,
            'style_config' => [
                'primary_color'    => '#ec4899',
                'secondary_color'  => '#fce7f3',
                'background_color' => '#ffffff',
                'font_heading'     => 'Oswald',
                'font_body'        => 'Montserrat',
                'show_photo'       => true
            ]
        ]);
    }
}
