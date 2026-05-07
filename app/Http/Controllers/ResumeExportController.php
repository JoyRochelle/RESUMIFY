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
        return view($cv->template->blade_path, compact('cv'));
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
