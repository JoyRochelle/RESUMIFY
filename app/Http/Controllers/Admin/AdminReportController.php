<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Queries\AdminReportStatsQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function __construct(
        protected AdminReportStatsQuery $statsQuery
    ) {}
    public function index(Request $request): View
    {
        [$from, $to] = $this->parseDateRange($request);

        $stats = $this->statsQuery->get($from, $to);

        // Daily revenue breakdown for the period (for the chart)
        $dailyRevenue = Transaction::where('status', 'success')
            ->whereBetween('paid_at', [$from->startOfDay()->copy(), $to->endOfDay()->copy()])
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports', compact('stats', 'from', 'to', 'dailyRevenue'));
    }

    public function exportPdf(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);

        $stats    = $this->statsQuery->get($from, $to);
        $filename = 'report-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('admin.reports.pdf', compact('stats', 'from', 'to'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->parseDateRange($request);

        $filename = 'report-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($from, $to) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Metric', 'Value']);

            $stats = $this->statsQuery->get($from, $to);
            foreach ($stats as $label => $value) {
                fputcsv($handle, [ucwords(str_replace('_', ' ', $label)), $value]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Period', $from->toDateString() . ' to ' . $to->toDateString()]);
            fputcsv($handle, ['Generated At', now()->toDateTimeString()]);

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store',
            'Pragma'              => 'no-cache',
        ]);
    }

    private function parseDateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        // Ensure from <= to
        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }


}
