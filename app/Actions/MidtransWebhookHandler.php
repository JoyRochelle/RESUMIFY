<?php

namespace App\Actions;

use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MidtransWebhookHandler
{
    public function handle(array $payload): JsonResponse
    {
        if (!$this->isSignatureValid($payload)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $transactionStatus = $payload['transaction_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? '';
        $midtransTransactionId = $payload['transaction_id'] ?? '';

        $result = DB::transaction(function () use (
            $orderId,
            $statusCode,
            $grossAmount,
            $transactionStatus,
            $paymentType,
            $midtransTransactionId
        ) {
            $transaction = Transaction::where('midtrans_order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return [
                    'response' => response()->json(['error' => 'Transaction not found'], 404),
                    'dispatch_confirmation' => false,
                ];
            }

            $isSuccessful = $statusCode === '200'
                && in_array($transactionStatus, ['settlement', 'capture'], true);

            if ($isSuccessful) {
                if (! $this->grossAmountMatches($grossAmount, (string) $transaction->amount)) {
                    return [
                        'response' => response()->json(['error' => 'Amount mismatch'], 422),
                        'dispatch_confirmation' => false,
                    ];
                }

                if ($transaction->status !== 'success') {
                    $user = User::whereKey($transaction->user_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $transaction->update([
                        'status' => 'success',
                        'midtrans_transaction_id' => $midtransTransactionId,
                        'payment_method' => $paymentType,
                        'paid_at' => now(),
                    ]);

                    $user->forceFill([
                        'role' => 'premium',
                        'ai_quota_used' => 0,
                        'ai_quota_reset_at' => now(),
                    ])->save();

                    Subscription::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'plan' => 'premium',
                            'status' => 'active',
                            'starts_at' => now(),
                            'ends_at' => now()->addMonth(),
                        ]
                    );

                    return [
                        'response' => response()->json(['message' => 'Callback handled']),
                        'dispatch_confirmation' => true,
                        'transaction' => $transaction->fresh(),
                        'user' => $user->fresh(),
                    ];
                }
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
                $transaction->update(['status' => 'failed']);
            } elseif ($transactionStatus === 'pending') {
                $transaction->update(['status' => 'pending']);
            }

            return [
                'response' => response()->json(['message' => 'Callback handled']),
                'dispatch_confirmation' => false,
            ];
        });

        if ($result['dispatch_confirmation']) {
            SendPaymentConfirmationJob::dispatch($result['user'], $result['transaction']);
        }

        return $result['response'];
    }

    private function grossAmountMatches(string $reported, string $expected): bool
    {
        return number_format((float) $reported, 2, '.', '') === number_format((float) $expected, 2, '.', '');
    }

    public function isSignatureValid(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $serverKey = config('services.midtrans.server_key');

        if ($signatureKey === '') {
            return false;
        }

        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($calculatedSignature, $signatureKey);
    }
}
