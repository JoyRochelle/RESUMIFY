<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks so we can delete old templates freely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cv_templates')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── 1. Netral / ATS-Safe ──────────────────────────────────────

        CvTemplate::create([
            'name'             => 'Foundation',
            'blade_path'       => 'templates.foundation',
            'category'         => 'professional',
            'experience_level' => 'general',
            'description'      => 'Template paling jujur untuk parser ATS. Satu kolom murni tanpa elemen dekoratif — pilihan default yang aman untuk semua persona.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 1,
            'style_config'     => [
                'primary_color'    => '#1f2937',
                'secondary_color'  => '#374151',
                'background_color' => '#ffffff',
                'font_heading'     => 'Source Serif 4',
                'font_body'        => 'Inter',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Slate',
            'blade_path'       => 'templates.slate',
            'category'         => 'professional',
            'experience_level' => 'general',
            'description'      => 'Hierarki tipografi tegas lewat ukuran & bold, bukan warna. Modern tanpa elemen yang berisiko gagal parsing ATS.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 2,
            'style_config'     => [
                'primary_color'    => '#334155',
                'secondary_color'  => '#64748b',
                'background_color' => '#ffffff',
                'font_heading'     => 'Inter',
                'font_body'        => 'Inter',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Quill',
            'blade_path'       => 'templates.quill',
            'category'         => 'professional',
            'experience_level' => 'general',
            'description'      => 'Rasa klasik-formal dengan sentuhan krem yang elegan. Cocok untuk siapa saja yang ragu memilih gaya "berani".',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 3,
            'style_config'     => [
                'primary_color'    => '#1a1a1a',
                'secondary_color'  => '#4a4a4a',
                'background_color' => '#faf7f2',
                'font_heading'     => 'Lora',
                'font_body'        => 'Source Sans 3',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Northbound',
            'blade_path'       => 'templates.northbound',
            'category'         => 'professional',
            'experience_level' => 'general',
            'description'      => 'Satu kolom dengan garis aksen kiri tipis sebagai identitas visual minimal. Aman ATS karena struktur tetap linear.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 4,
            'style_config'     => [
                'primary_color'    => '#1e3a8a',
                'secondary_color'  => '#3b5fc2',
                'background_color' => '#ffffff',
                'font_heading'     => 'Manrope',
                'font_body'        => 'Manrope',
            ]
        ]);

        // ── 2. Awal Karier (fresh_graduate) ─────────────────────────

        CvTemplate::create([
            'name'             => 'Sprout',
            'blade_path'       => 'templates.sprout',
            'category'         => 'professional',
            'experience_level' => 'fresh_graduate',
            'description'      => 'Untuk fresh graduate: IPK ditonjolkan, section Proyek & Organisasi Kampus setara dengan pengalaman kerja.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 5,
            'style_config'     => [
                'primary_color'    => '#16a34a',
                'secondary_color'  => '#15803d',
                'background_color' => '#ffffff',
                'font_heading'     => 'Manrope',
                'font_body'        => 'Manrope',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'First Draft',
            'blade_path'       => 'templates.first-draft',
            'category'         => 'professional',
            'experience_level' => 'fresh_graduate',
            'description'      => 'Section "Pengalaman Kerja" otomatis berlabel "Magang & Pengalaman Organisasi" jika field pengalaman kosong.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 6,
            'style_config'     => [
                'primary_color'    => '#374151',
                'secondary_color'  => '#6b7280',
                'background_color' => '#ffffff',
                'font_heading'     => 'Inter',
                'font_body'        => 'Inter',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Compass',
            'blade_path'       => 'templates.compass',
            'category'         => 'professional',
            'experience_level' => 'fresh_graduate',
            'description'      => 'Ada ringkasan "Tujuan Karier" 2 baris di atas — membantu recruiter cepat paham arah pelamar yang belum punya track record.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 7,
            'style_config'     => [
                'primary_color'    => '#1e293b',
                'secondary_color'  => '#334155',
                'background_color' => '#ffffff',
                'font_heading'     => 'Lato',
                'font_body'        => 'Lato',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Blank Page',
            'blade_path'       => 'templates.blank-page',
            'category'         => 'professional',
            'experience_level' => 'fresh_graduate',
            'description'      => 'Dirancang agar CV tetap terasa penuh meski hanya berisi pendidikan, 1-2 sertifikasi, dan kegiatan volunteer.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 8,
            'style_config'     => [
                'primary_color'    => '#111827',
                'secondary_color'  => '#6b7280',
                'background_color' => '#ffffff',
                'font_heading'     => 'Open Sans',
                'font_body'        => 'Open Sans',
            ]
        ]);

        // ── 3. Korporat & Industri Besar ────────────────────────────

        CvTemplate::create([
            'name'             => 'Marlowe',
            'blade_path'       => 'templates.marlowe',
            'category'         => 'professional',
            'experience_level' => 'senior',
            'description'      => 'Header tebal nama+jabatan di atas, gaya formal-klasik yang familiar buat HR korporat besar maupun BUMN.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 9,
            'style_config'     => [
                'primary_color'    => '#1a1a1a',
                'secondary_color'  => '#374151',
                'background_color' => '#ffffff',
                'font_heading'     => 'Playfair Display',
                'font_body'        => 'Inter',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Whitfield',
            'blade_path'       => 'templates.whitfield',
            'category'         => 'professional',
            'experience_level' => 'senior',
            'description'      => 'Garis pembatas antar-section tegas, tipografi besar untuk nama. Format CV konservatif untuk perusahaan besar.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 10,
            'style_config'     => [
                'primary_color'    => '#1e3a8a',
                'secondary_color'  => '#1e40af',
                'background_color' => '#ffffff',
                'font_heading'     => 'Georgia',
                'font_body'        => 'Times New Roman',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'The Ledger',
            'blade_path'       => 'templates.the-ledger',
            'category'         => 'professional',
            'experience_level' => 'senior',
            'description'      => 'Terinspirasi laporan tahunan: pencapaian terukur (%, Rp, tim) ditonjolkan. Cocok untuk finance, operasional, manajemen.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 11,
            'style_config'     => [
                'primary_color'    => '#1a1a1a',
                'secondary_color'  => '#92722a',
                'background_color' => '#ffffff',
                'font_heading'     => 'Merriweather',
                'font_body'        => 'Merriweather',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Boardroom',
            'blade_path'       => 'templates.boardroom',
            'category'         => 'professional',
            'experience_level' => 'senior',
            'description'      => 'Layout 70/30 dengan sidebar ringkas (kontak, skill, sertifikasi). Kesan eksekutif untuk posisi manajerial.',
            'badge'            => 'ATS MED',
            'badge_color'      => 'amber',
            'is_premium'       => true,
            'is_active'        => true,
            'sort_order'       => 12,
            'style_config'     => [
                'primary_color'    => '#1f2937',
                'secondary_color'  => '#374151',
                'background_color' => '#f9fafb',
                'font_heading'     => 'Lora',
                'font_body'        => 'Lato',
                'show_photo'       => true,
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Heritage',
            'blade_path'       => 'templates.heritage',
            'category'         => 'professional',
            'experience_level' => 'senior',
            'description'      => 'Tanpa warna sama sekali, paling konservatif. Untuk industri sangat tradisional: perbankan, instansi pemerintah.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 13,
            'style_config'     => [
                'primary_color'    => '#000000',
                'secondary_color'  => '#000000',
                'background_color' => '#ffffff',
                'font_heading'     => 'Times New Roman',
                'font_body'        => 'Times New Roman',
            ]
        ]);

        // ── 4. Startup & Teknologi ───────────────────────────────────

        CvTemplate::create([
            'name'             => 'Pulse',
            'blade_path'       => 'templates.pulse',
            'category'         => 'technology',
            'experience_level' => 'general',
            'description'      => 'Skill-first: tech stack ditonjolkan di atas riwayat pengalaman. Cocok untuk software engineer yang melamar startup.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 14,
            'style_config'     => [
                'primary_color'    => '#7c3aed',
                'secondary_color'  => '#6d28d9',
                'background_color' => '#ffffff',
                'font_heading'     => 'Inter',
                'font_body'        => 'Inter',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Sandbox',
            'blade_path'       => 'templates.sandbox',
            'category'         => 'technology',
            'experience_level' => 'general',
            'description'      => 'Section khusus "Proyek Pribadi" dengan GitHub/portofolio ditonjolkan setara pengalaman formal — relevan untuk kultur startup.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 15,
            'style_config'     => [
                'primary_color'    => '#ea580c',
                'secondary_color'  => '#c2410c',
                'background_color' => '#ffffff',
                'font_heading'     => 'Poppins',
                'font_body'        => 'Poppins',
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Loop',
            'blade_path'       => 'templates.loop',
            'category'         => 'technology',
            'experience_level' => 'general',
            'description'      => 'Riwayat pengalaman sebagai garis waktu vertikal sederhana. Untuk kandidat dengan banyak transisi karier cepat.',
            'badge'            => 'ATS MED',
            'badge_color'      => 'amber',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 16,
            'style_config'     => [
                'primary_color'    => '#0d9488',
                'secondary_color'  => '#0f766e',
                'background_color' => '#ffffff',
                'font_heading'     => 'Roboto',
                'font_body'        => 'Roboto',
                'show_photo'       => true,
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Northstar',
            'blade_path'       => 'templates.northstar',
            'category'         => 'technology',
            'experience_level' => 'general',
            'description'      => 'Setiap bullet pengalaman punya ruang untuk angka pencapaian (%, jumlah user, Rp) yang menonjol — data-driven culture.',
            'badge'            => 'ATS OK',
            'badge_color'      => 'blue',
            'is_premium'       => false,
            'is_active'        => true,
            'sort_order'       => 17,
            'style_config'     => [
                'primary_color'    => '#2563eb',
                'secondary_color'  => '#1d4ed8',
                'background_color' => '#ffffff',
                'font_heading'     => 'Inter',
                'font_body'        => 'Inter',
            ]
        ]);

        // ── 5. Kreatif & Personal Branding ──────────────────────────

        CvTemplate::create([
            'name'             => 'Canvas',
            'blade_path'       => 'templates.canvas',
            'category'         => 'creative',
            'experience_level' => 'general',
            'description'      => 'Dua kolom dengan foto, untuk desainer/marketing/content creator. Skor ATS mungkin lebih rendah.',
            'badge'            => 'ATS LOW',
            'badge_color'      => 'red',
            'is_premium'       => true,
            'is_active'        => true,
            'sort_order'       => 18,
            'style_config'     => [
                'primary_color'    => '#ec4899',
                'secondary_color'  => '#db2777',
                'background_color' => '#ffffff',
                'font_heading'     => 'Poppins',
                'font_body'        => 'Poppins',
                'show_photo'       => true,
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Aperture',
            'blade_path'       => 'templates.aperture',
            'category'         => 'creative',
            'experience_level' => 'general',
            'description'      => 'Area khusus thumbnail karya/link portofolio. Untuk fotografer, videographer, UI/UX designer yang prioritasnya showcase visual.',
            'badge'            => 'ATS LOW',
            'badge_color'      => 'red',
            'is_premium'       => true,
            'is_active'        => true,
            'sort_order'       => 19,
            'style_config'     => [
                'primary_color'    => '#1a1a1a',
                'secondary_color'  => '#eab308',
                'background_color' => '#1a1a1a',
                'font_heading'     => 'Montserrat',
                'font_body'        => 'Montserrat',
                'show_photo'       => true,
            ]
        ]);

        CvTemplate::create([
            'name'             => 'Mosaic',
            'blade_path'       => 'templates.mosaic',
            'category'         => 'creative',
            'experience_level' => 'general',
            'description'      => 'Grid terstruktur untuk profesi event/brand/social media yang ingin kesan ekspresif tapi tetap rapi. Skor ATS mungkin lebih rendah.',
            'badge'            => 'ATS LOW',
            'badge_color'      => 'red',
            'is_premium'       => true,
            'is_active'        => true,
            'sort_order'       => 20,
            'style_config'     => [
                'primary_color'    => '#7c3aed',
                'secondary_color'  => '#ea580c',
                'background_color' => '#ffffff',
                'font_heading'     => 'Oswald',
                'font_body'        => 'Inter',
                'show_photo'       => true,
            ]
        ]);

        // Re-point any CVs that had old template IDs (now deleted) to the first new template
        $firstTemplate = DB::table('cv_templates')->orderBy('sort_order')->first();
        if ($firstTemplate) {
            DB::table('cvs')
                ->whereNotIn('template_id', DB::table('cv_templates')->pluck('id')->toArray())
                ->update(['template_id' => $firstTemplate->id]);
        }
    }
}
