<?php

namespace App\Services;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class TemplateThumbnailService
{
    private const WIDTH = 794;

    private const HEIGHT = 1123;

    /**
     * Render a template once in headless Chrome and store the screenshot as a
     * public thumbnail. Catalog pages can then load a cheap image instead of a
     * full iframe document for every card.
     */
    public function generate(CvTemplate $template, bool $force = false): string
    {
        if ($template->thumbnail_url && ! $force && Storage::disk('public')->exists($template->thumbnail_url)) {
            return $template->thumbnail_url;
        }

        $browser = $this->browserBinary();
        $htmlPath = $this->writePreviewHtml($template);
        $relativePath = "templates/previews/{$template->id}.png";
        $outputPath = Storage::disk('public')->path($relativePath);

        File::ensureDirectoryExists(dirname($outputPath));

        try {
            $this->captureScreenshot($browser, $htmlPath, $outputPath, true);
        } catch (RuntimeException $exception) {
            $this->captureScreenshot($browser, $htmlPath, $outputPath, false, $exception);
        } finally {
            File::delete($htmlPath);
        }

        if (! File::exists($outputPath) || File::size($outputPath) === 0) {
            throw new RuntimeException("Chrome did not produce a thumbnail for template {$template->id}.");
        }

        $template->forceFill(['thumbnail_url' => $relativePath])->save();

        return $relativePath;
    }

    private function captureScreenshot(
        string $browser,
        string $htmlPath,
        string $outputPath,
        bool $useNewHeadless,
        ?RuntimeException $previous = null
    ): void {
        $process = new Process([
            $browser,
            $useNewHeadless ? '--headless=new' : '--headless',
            '--disable-gpu',
            '--hide-scrollbars',
            '--no-sandbox',
            '--run-all-compositor-stages-before-draw',
            '--window-size='.self::WIDTH.','.self::HEIGHT,
            '--screenshot='.$outputPath,
            'file://'.$htmlPath,
        ]);

        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful()) {
            $message = trim($process->getErrorOutput()) ?: trim($process->getOutput());
            throw new RuntimeException(
                'Unable to generate template thumbnail: '.($message ?: 'Chrome exited unsuccessfully.'),
                previous: $previous
            );
        }
    }

    private function writePreviewHtml(CvTemplate $template): string
    {
        $directory = storage_path('framework/cache/template-thumbnails');
        File::ensureDirectoryExists($directory);

        $path = "{$directory}/{$template->id}.html";
        File::put($path, view($template->safeBladePath(), [
            'cv' => $this->sampleCv($template),
        ])->render());

        return $path;
    }

    private function sampleCv(CvTemplate $template): Cv
    {
        $cv = new Cv([
            'title' => 'Template Preview',
            'template_id' => $template->id,
        ]);

        $cv->setRelation('template', $template);
        $cv->setRelation('sections', $this->sampleSections());

        return $cv;
    }

    private function sampleSections(): Collection
    {
        return collect([
            new CvSection([
                'type' => 'personal_info',
                'title' => 'Personal Info',
                'content' => [
                    'name' => 'Nadia Pratama',
                    'email' => 'nadia@example.com',
                    'phone' => '+62 812 3456 7890',
                    'title' => 'Senior Product Manager',
                    'location' => 'Jakarta, Indonesia',
                    'summary' => 'Product leader with 8+ years of experience launching hiring, fintech, and SaaS products across Southeast Asia.',
                ],
            ]),
            new CvSection([
                'type' => 'work_experience',
                'title' => 'Work Experience',
                'content' => [
                    [
                        'title' => 'Senior Product Manager',
                        'company' => 'NusaTech',
                        'start_date' => '2022',
                        'end_date' => 'Present',
                        'description' => "Led a cross-functional squad shipping onboarding improvements for 1.2M monthly users.\nRaised activation by 18% through funnel research, pricing tests, and lifecycle experiments.",
                    ],
                    [
                        'title' => 'Product Manager',
                        'company' => 'Karya Digital',
                        'start_date' => '2019',
                        'end_date' => '2022',
                        'description' => "Owned B2B dashboard roadmap for enterprise customers.\nPartnered with engineering and sales to reduce implementation time by 32%.",
                    ],
                ],
            ]),
            new CvSection([
                'type' => 'education',
                'title' => 'Education',
                'content' => [
                    [
                        'degree' => 'B.S. Information Systems',
                        'school' => 'Universitas Indonesia',
                        'start_date' => '2014',
                        'end_date' => '2018',
                        'description' => 'Graduated with honors.',
                    ],
                ],
            ]),
            new CvSection([
                'type' => 'skills',
                'title' => 'Skills',
                'content' => [
                    ['name' => 'Product Strategy', 'level' => 'Advanced'],
                    ['name' => 'User Research', 'level' => 'Advanced'],
                    ['name' => 'A/B Testing', 'level' => 'Advanced'],
                    ['name' => 'SQL', 'level' => 'Intermediate'],
                ],
            ]),
        ]);
    }

    private function browserBinary(): string
    {
        $configured = env('TEMPLATE_THUMBNAIL_BROWSER');
        if ($configured && is_executable($configured)) {
            return $configured;
        }

        foreach ([
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ] as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('No Chrome/Chromium binary found. Set TEMPLATE_THUMBNAIL_BROWSER to generate template thumbnails.');
    }
}
