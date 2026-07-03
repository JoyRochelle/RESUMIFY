<?php

namespace App\Http\Controllers\Billing;

use App\Actions\MidtransWebhookHandler;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $orderId = 'TRX-' . strtoupper(Str::random(10)) . '-' . time();
        $amount = 49000; // Rp 49.000 for Premium

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'midtrans_order_id' => $orderId,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        try {
            $snapTransaction = Snap::createTransaction($params);

            return response()->json([
                'snap_token' => $snapTransaction->token,
                'redirect_url' => $snapTransaction->redirect_url,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate payment token'], 500);
        }
    }

    public function callback(Request $request, MidtransWebhookHandler $handler)
    {
        return $handler->handle($request->all());
    }
}
