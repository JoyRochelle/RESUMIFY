<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminLogController extends Controller
{
    public function index(Request $request): View
    {
        $tab  = $request->input('tab', 'ai');
        $from = $request->input('from');
        $to   = $request->input('to');

        $aiLogs = AiUsageLog::with('user:id,name,email')
            ->when($from, fn($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
            ->when($to,   fn($q) => $q->where('created_at', '<=', $to   . ' 23:59:59'))
            ->latest()
            ->paginate(50, ['*'], 'ai_page')
            ->withQueryString();

        $transactions = Transaction::with('user:id,name,email')
            ->when($from, fn($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
            ->when($to,   fn($q) => $q->where('created_at', '<=', $to   . ' 23:59:59'))
            ->latest()
            ->paginate(50, ['*'], 'fin_page')
            ->withQueryString();

        $totalAiCostMtd  = AiUsageLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('cost_usd');

        $totalRevenueMtd = Transaction::where('status', 'success')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $totalAiActions  = AiUsageLog::count();
        $totalTxCount    = Transaction::count();

        return view('admin.logs', compact(
            'tab', 'from', 'to',
            'aiLogs', 'transactions',
            'totalAiCostMtd', 'totalRevenueMtd',
            'totalAiActions', 'totalTxCount'
        ));
    }

    public function exportAiCsv(Request $request): StreamedResponse
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $filename = 'ai-usage-' . now()->format('Y-m-d') . '.csv';

        $response = response()->stream(function () use ($from, $to) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'User', 'Email', 'Action Type', 'Tokens Used', 'Cost USD', 'Created At']);

            AiUsageLog::with('user:id,name,email')
                ->when($from, fn($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
                ->when($to,   fn($q) => $q->where('created_at', '<=', $to   . ' 23:59:59'))
                ->oldest()
                ->cursor()
                ->each(function ($log) use ($handle) {
                    fputcsv($handle, [
                        $log->id,
                        $log->user?->name ?? 'N/A',
                        $log->user?->email ?? 'N/A',
                        $log->action_type,
                        $log->tokens_used,
                        number_format($log->cost_usd, 6),
                        $log->created_at?->toDateTimeString(),
                    ]);
                });

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store',
            'Pragma'              => 'no-cache',
        ]);

        return $response;
    }

    public function exportFinanceCsv(Request $request): StreamedResponse
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $filename = 'finance-' . now()->format('Y-m-d') . '.csv';

        $response = response()->stream(function () use ($from, $to) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'User', 'Email', 'Order ID', 'Amount', 'Payment Method', 'Status', 'Paid At', 'Created At']);

            Transaction::with('user:id,name,email')
                ->when($from, fn($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
                ->when($to,   fn($q) => $q->where('created_at', '<=', $to   . ' 23:59:59'))
                ->oldest()
                ->cursor()
                ->each(function ($tx) use ($handle) {
                    fputcsv($handle, [
                        $tx->id,
                        $tx->user?->name ?? 'N/A',
                        $tx->user?->email ?? 'N/A',
                        $tx->midtrans_order_id,
                        number_format($tx->amount, 2),
                        $tx->payment_method,
                        $tx->status,
                        $tx->paid_at?->toDateTimeString(),
                        $tx->created_at?->toDateTimeString(),
                    ]);
                });

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store',
            'Pragma'              => 'no-cache',
        ]);

        return $response;
    }
}
