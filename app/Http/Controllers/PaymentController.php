<?php

namespace App\Http\Controllers;

use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
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
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate payment token'], 500);
        }
    }

    public function callback(Request $request)
    {
        $payload = $request->all();
        $serverKey = config('services.midtrans.server_key');

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($calculatedSignature !== $signatureKey) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? '';
        $transactionId = $payload['transaction_id'] ?? '';

        $transaction = Transaction::where('midtrans_order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            if ($transaction->status !== 'success') {
                $transaction->update([
                    'status' => 'success',
                    'midtrans_transaction_id' => $transactionId,
                    'payment_method' => $paymentType,
                    'paid_at' => now(),
                ]);

                $user = $transaction->user;
                $user->update([
                    'role' => 'premium',
                    'ai_quota_used' => 0,
                    'ai_quota_reset_at' => now(),
                ]);

                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan' => 'premium',
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => now()->addMonth(),
                    ]
                );

                SendPaymentConfirmationJob::dispatch($user, $transaction);
            }
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $transaction->update(['status' => 'failed']);
        } elseif ($transactionStatus == 'pending') {
            $transaction->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'Callback handled']);
    }
}
