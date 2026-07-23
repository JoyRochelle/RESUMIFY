<?php

namespace App\Console\Commands;

use App\Models\CvTemplate;
use App\Services\TemplateThumbnailService;
use Illuminate\Console\Command;
use Throwable;

class GenerateTemplateThumbnails extends Command
{
    protected $signature = 'templates:thumbnails
        {--template= : Generate only one template thumbnail by id}
        {--force : Regenerate thumbnails even when thumbnail_url already exists}';

    protected $description = 'Generate cached image thumbnails for CV template catalog cards';

    public function handle(TemplateThumbnailService $thumbnails): int
    {
        $query = CvTemplate::query()->orderBy('sort_order')->orderBy('name');

        if ($templateId = $this->option('template')) {
            $query->whereKey($templateId);
        }

        $templates = $query->get();

        if ($templates->isEmpty()) {
            $this->warn('No templates found.');

            return self::SUCCESS;
        }

        $failed = 0;
        $force = (bool) $this->option('force');

        $this->components->info("Generating thumbnails for {$templates->count()} template(s)...");

        foreach ($templates as $template) {
            try {
                $path = $thumbnails->generate($template, $force);
                $this->line("  <info>OK</info> {$template->name} <comment>{$path}</comment>");
            } catch (Throwable $exception) {
                $failed++;
                $this->line("  <error>FAIL</error> {$template->name} {$exception->getMessage()}");
            }
        }

        if ($failed > 0) {
            $this->error("Finished with {$failed} failed thumbnail(s).");

            return self::FAILURE;
        }

        $this->components->success('Template thumbnails generated.');

        return self::SUCCESS;
    }
}
