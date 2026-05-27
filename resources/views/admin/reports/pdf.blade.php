<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Revenue Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #3d2e25;
            background: #ffffff;
            padding: 40px;
        }

        /* Header */
        .header { border-bottom: 2px solid #4f3b2f; padding-bottom: 16px; margin-bottom: 24px; }
        .header-title { font-size: 22px; font-weight: bold; color: #4f3b2f; }
        .header-sub { font-size: 10px; color: #8b7355; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }
        .header-meta { font-size: 10px; color: #8b7355; margin-top: 8px; }

        /* Stats table */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #8b7355;
            margin-bottom: 10px;
        }

        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .stats-table tr { border-bottom: 1px solid #f0ebe6; }
        .stats-table td { padding: 10px 12px; }
        .stats-table td:first-child { color: #6b5740; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-table td:last-child { text-align: right; font-size: 15px; font-weight: bold; color: #3d2e25; }
        .stats-table tr.highlight td:last-child { color: #0f6e56; }

        /* Daily table */
        .daily-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .daily-table th {
            background: #f5ede6;
            padding: 8px 12px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8b7355;
        }
        .daily-table th.right { text-align: right; }
        .daily-table td { padding: 9px 12px; border-bottom: 1px solid #f0ebe6; font-size: 10px; color: #3d2e25; }
        .daily-table td.right { text-align: right; font-weight: bold; }
        .daily-table tfoot td { border-top: 2px solid #4f3b2f; font-weight: bold; padding-top: 10px; font-size: 11px; }
        .daily-table tfoot td.right { color: #0f6e56; }

        /* Footer */
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e8e0d8; font-size: 9px; color: #a89880; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">Resumify — Revenue Report</div>
        <div class="header-sub">Administrative Finance Summary</div>
        <div class="header-meta">
            Period: {{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Generated: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="section-title">Key Metrics</div>
    <table class="stats-table">
        <tr class="highlight">
            <td>Total Revenue</td>
            <td>Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>New Users</td>
            <td>{{ number_format($stats['new_users']) }}</td>
        </tr>
        <tr>
            <td>Premium Conversions</td>
            <td>{{ number_format($stats['premium_conversions']) }}</td>
        </tr>
        <tr>
            <td>Total AI Calls</td>
            <td>{{ number_format($stats['total_ai_calls']) }}</td>
        </tr>
        <tr>
            <td>Total AI Cost (USD)</td>
            <td>${{ number_format($stats['total_ai_cost'], 4) }}</td>
        </tr>
        @if($stats['total_revenue'] > 0)
        <tr>
            <td>Avg Revenue per Conversion</td>
            <td>
                @if($stats['premium_conversions'] > 0)
                    Rp {{ number_format($stats['total_revenue'] / $stats['premium_conversions'], 0, ',', '.') }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        @endif
    </table>

</body>
</html>
