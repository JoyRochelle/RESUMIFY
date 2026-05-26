<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\ChameleonAdaptation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResumeExportController extends Controller
{
    use AuthorizesRequests;

    public function preview(Cv $cv) {
        \Illuminate\Support\Facades\Gate::authorize('view', $cv); // only owner can preview
        $cv->load(['template', 'sections']);

        $templateBlade = $cv->template->blade_path;
        if (request()->has('template_id')) {
            $previewTemplate = \App\Models\CvTemplate::find(request()->query('template_id'));
            if ($previewTemplate) {
                $templateBlade = $previewTemplate->blade_path;
                $cv->setRelation('template', $previewTemplate);
            }
        }

        if (request()->has('adaptation_id')) {
            $adaptation = ChameleonAdaptation::find(request()->query('adaptation_id'));
            if ($adaptation && $adaptation->cv_id === $cv->id) {
                $fakeSections = collect($adaptation->adapted_content)->map(function ($sec) use ($cv) {
                    return new CvSection([
                        'cv_id' => $cv->id,
                        'type' => $sec['type'] ?? 'unknown',
                        'title' => $sec['title'] ?? '',
                        'content' => $sec['content'] ?? [],
                    ]);
                });
                $cv->setRelation('sections', $fakeSections);
            }
        }

        return view($templateBlade, compact('cv'));
    }

    public function downloadPdf(Cv $cv) {
        \Illuminate\Support\Facades\Gate::authorize('view', $cv);
        $cv->load(['template', 'sections']);

        if (request()->has('adaptation_id')) {
            $adaptation = ChameleonAdaptation::find(request()->query('adaptation_id'));
            if ($adaptation && $adaptation->cv_id === $cv->id) {
                $fakeSections = collect($adaptation->adapted_content)->map(function ($sec) use ($cv) {
                    return new CvSection([
                        'cv_id' => $cv->id,
                        'type' => $sec['type'] ?? 'unknown',
                        'title' => $sec['title'] ?? '',
                        'content' => $sec['content'] ?? [],
                    ]);
                });
                $cv->setRelation('sections', $fakeSections);
            }
        }

        $isPdf = true;
        $html = view($cv->template->blade_path, compact('cv', 'isPdf'))->render();
        $pdf  = Pdf::loadHTML($html)->setPaper('a4');

        return $pdf->download($cv->title . '.pdf');
    }
}
