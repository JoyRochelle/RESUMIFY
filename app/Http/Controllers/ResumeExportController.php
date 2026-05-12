<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResumeExportController extends Controller
{
    use AuthorizesRequests;

    // Browser preview
    public function preview(Cv $cv)
    {
        abort_unless($cv->user_id === auth()->id(), 403); // only owner can preview
        $cv->load(['template', 'sections']);

        $templateBlade = $cv->template->blade_path;
        if (request()->has('template_id')) {
            $previewTemplate = \App\Models\CvTemplate::find(request()->query('template_id'));
            if ($previewTemplate) {
                $templateBlade = $previewTemplate->blade_path;
                // Temporarily override the style config if needed, or just let it use the current CV's style
                $cv->setRelation('template', $previewTemplate);
            }
        }

        return view($templateBlade, compact('cv'));
    }

    // PDF download
    public function downloadPdf(Cv $cv)
    {
        abort_unless($cv->user_id === auth()->id(), 403);
        $cv->load(['template', 'sections']);

        $isPdf = true;
        $html = view($cv->template->blade_path, compact('cv', 'isPdf'))->render();
        $pdf  = Pdf::loadHTML($html)->setPaper('a4');

        return $pdf->download($cv->title . '.pdf');
    }
}
