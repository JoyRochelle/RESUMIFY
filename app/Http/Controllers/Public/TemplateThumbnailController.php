<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CvTemplate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateThumbnailController extends Controller
{
    public function __invoke(CvTemplate $template): StreamedResponse
    {
        $path = $template->thumbnail_url;
        $disk = Storage::disk('public');

        abort_if(! $path || ! $disk->exists($path), 404);

        return $disk->response($path, headers: [
            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
        ]);
    }
}
