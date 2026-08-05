<?php

namespace App\Http\Controllers\User\Resume;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\CvSection;
use App\Models\ChameleonAdaptation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ResumeExportController extends Controller
{
    use AuthorizesRequests;

    public function preview(Cv $cv) {
        \Illuminate\Support\Facades\Gate::authorize('view', $cv); // only owner can preview
        $cv->load(['template', 'sections']);

        $templateBlade = $cv->template->safeBladePath();
        if (request()->has('template_id')) {
            $previewTemplate = \App\Models\CvTemplate::find(request()->query('template_id'));
            if ($previewTemplate) {
                $templateBlade = $previewTemplate->safeBladePath();
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

    public function downloadPdf(Cv $cv): Response|RedirectResponse {
        \Illuminate\Support\Facades\Gate::authorize('view', $cv);

        $cv->load(['template', 'sections']);

        // Exporting a PDF is free on every plan. The premium boundary is the
        // design: a Premium account that downgrades keeps the resumes it built
        // on premium templates, and exporting one would hand over the paid
        // layout for nothing.
        if ($cv->template?->is_premium && !auth()->user()->canUsePremiumFeature('premium_templates')) {
            $message = 'This resume uses a Premium template. Switch it to a free template, or upgrade, to export it.';

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'error' => 'premium_template_required',
                    'message' => $message,
                    'upgrade_url' => route('user.upgrade-quota'),
                ], 402);
            }

            return redirect()->route('user.upgrade-quota')->with('error', $message);
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

        $isPdf = true;
        $html = view($cv->template->safeBladePath(), compact('cv', 'isPdf'))->render();
        $pdf  = Pdf::loadHTML($html)->setPaper('a4');

        return $pdf->download($cv->title . '.pdf');
    }
}
