<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Guards against language drift: every CV template renders in English.
 *
 * Four early-career templates (blank-page, compass, first-draft, sprout) had
 * hardcoded Indonesian section headings while the other 16 used English. This
 * scans the rendered template markup for Indonesian tokens so the drift cannot
 * silently return.
 */
class TemplateLanguageDriftTest extends TestCase
{
    /**
     * Indonesian words that only appear as user-visible heading/label text in
     * the CV templates. Kept narrow to avoid matching English substrings.
     */
    private const INDONESIAN_TOKENS = [
        'Pengalaman',
        'Pendidikan',
        'Keahlian',
        'Keterampilan',
        'Ringkasan',
        'Proyek',
        'Sertifikasi',
        'Sertifikat',
        'Organisasi',
        'Karier',
        'Karya',
        'Magang',
        'Tujuan Karier',
        'Prestasi',
        'Penghargaan',
    ];

    public function test_no_template_contains_indonesian_heading_text(): void
    {
        $dir = resource_path('views/templates');
        $files = glob($dir . '/*.blade.php');

        $this->assertNotEmpty($files, 'No CV template blade files were found.');

        $offenders = [];

        foreach ($files as $file) {
            $contents = file_get_contents($file);

            foreach (self::INDONESIAN_TOKENS as $token) {
                if (preg_match('/\b' . preg_quote($token, '/') . '\b/u', $contents)) {
                    $offenders[] = basename($file) . ' contains "' . $token . '"';
                }
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "CV templates must be entirely in English. Found Indonesian text:\n- "
                . implode("\n- ", $offenders)
        );
    }
}
