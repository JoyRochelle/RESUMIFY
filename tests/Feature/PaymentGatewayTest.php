<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: build a valid Midtrans webhook payload with correct signature.
     */
    private function buildWebhookPayload(
        string $orderId,
        string $transactionStatus,
        string $paymentType = 'gopay',
        string $transactionId = 'midtrans-uuid-123',
        string $statusCode = '200',
        string $grossAmount = '49000.00',
    ): array {
        $serverKey = config('services.midtrans.server_key');
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return [
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'transaction_status' => $transactionStatus,
            'payment_type'       => $paymentType,
            'transaction_id'     => $transactionId,
            'signature_key'      => $signature,
        ];
    }

    /**
     * Helper: create a pending transaction for a given user.
     */
    private function createPendingTransaction(User $user, string $orderId = 'TRX-123'): Transaction
    {
        return Transaction::create([
            'user_id'           => $user->id,
            'midtrans_order_id' => $orderId,
            'amount'            => 49000,
            'status'            => 'pending',
        ]);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Settlement (Happy Path)
    // ────────────────────────────────────────────────

    public function test_midtrans_webhook_handles_successful_payment()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'settlement');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Callback handled']);

        $transaction->refresh();
        $this->assertEquals('success', $transaction->status);
        $this->assertEquals('gopay', $transaction->payment_method);
        $this->assertEquals('midtrans-uuid-123', $transaction->midtrans_transaction_id);
        $this->assertNotNull($transaction->paid_at);

        $user->refresh();
        $this->assertEquals('premium', $user->role);
        $this->assertEquals(0, $user->ai_quota_used);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan'    => 'premium',
            'status'  => 'active',
        ]);

        Queue::assertPushed(SendPaymentConfirmationJob::class);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Capture (Credit Card variant)
    // ────────────────────────────────────────────────

    public function test_midtrans_webhook_handles_capture_status()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'capture', 'credit_card');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('success', $transaction->status);
        $this->assertEquals('credit_card', $transaction->payment_method);

        $user->refresh();
        $this->assertEquals('premium', $user->role);

        Queue::assertPushed(SendPaymentConfirmationJob::class);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Signature Verification
    // ────────────────────────────────────────────────

    public function test_midtrans_webhook_rejects_invalid_signature()
    {
        config(['services.midtrans.server_key' => 'dummy_key']);

        $payload = [
            'order_id'           => 'TRX-123',
            'status_code'        => '200',
            'gross_amount'       => '49000.00',
            'transaction_status' => 'settlement',
            'signature_key'      => 'invalid_signature_hash',
        ];

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Invalid signature']);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Idempotency — Duplicate webhooks
    // ────────────────────────────────────────────────

    public function test_duplicate_webhook_does_not_double_upgrade()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'settlement');

        // First webhook — should process
        $this->postJson('/payment/callback', $payload)->assertStatus(200);

        // Second webhook (duplicate) — should be idempotent
        $response = $this->postJson('/payment/callback', $payload);
        $response->assertStatus(200);

        // Verify only 1 subscription exists (not 2)
        $this->assertDatabaseCount('subscriptions', 1);

        // Verify job was dispatched only once
        Queue::assertPushed(SendPaymentConfirmationJob::class, 1);

        // Verify user state is still correct
        $user->refresh();
        $this->assertEquals('premium', $user->role);
        $this->assertEquals(0, $user->ai_quota_used);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Cancel status
    // ────────────────────────────────────────────────

    public function test_webhook_handles_cancelled_payment()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'cancel');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Callback handled']);

        $transaction->refresh();
        $this->assertEquals('failed', $transaction->status);

        // User should NOT be upgraded
        $user->refresh();
        $this->assertEquals('basic', $user->role);

        // No email should be sent
        Queue::assertNotPushed(SendPaymentConfirmationJob::class);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Deny status
    // ────────────────────────────────────────────────

    public function test_webhook_handles_denied_payment()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'deny');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('failed', $transaction->status);

        $user->refresh();
        $this->assertEquals('basic', $user->role);

        Queue::assertNotPushed(SendPaymentConfirmationJob::class);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Expire status
    // ────────────────────────────────────────────────

    public function test_webhook_handles_expired_payment()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'expire');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('failed', $transaction->status);

        $user->refresh();
        $this->assertEquals('basic', $user->role);

        Queue::assertNotPushed(SendPaymentConfirmationJob::class);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Pending status
    // ────────────────────────────────────────────────

    public function test_webhook_handles_pending_payment()
    {
        Queue::fake();
        config(['services.midtrans.server_key' => 'dummy_key']);

        $user = User::factory()->create(['role' => 'basic']);
        $transaction = $this->createPendingTransaction($user);

        $payload = $this->buildWebhookPayload('TRX-123', 'pending');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('pending', $transaction->status);

        // User should NOT be upgraded
        $user->refresh();
        $this->assertEquals('basic', $user->role);

        Queue::assertNotPushed(SendPaymentConfirmationJob::class);
    }

    // ────────────────────────────────────────────────
    // WEBHOOK: Unknown transaction (order_id not found)
    // ────────────────────────────────────────────────

    public function test_webhook_returns_404_for_unknown_transaction()
    {
        config(['services.midtrans.server_key' => 'dummy_key']);

        $payload = $this->buildWebhookPayload('TRX-UNKNOWN-999', 'settlement');

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Transaction not found']);
    }

    // ────────────────────────────────────────────────
    // CREATE: Snap token generation (requires auth)
    // ────────────────────────────────────────────────

    public function test_create_payment_requires_authentication()
    {
        $response = $this->postJson('/payment/create');

        // Should redirect or return 401/302 since user is not authenticated
        $this->assertTrue(in_array($response->status(), [401, 302, 403]));
    }

    public function test_create_payment_stores_pending_transaction()
    {
        config([
            'services.midtrans.server_key' => 'dummy_key',
            'services.midtrans.client_key' => 'dummy_client_key',
            'services.midtrans.is_production' => false,
        ]);

        $user = User::factory()->create(['role' => 'basic']);

        // We can't test the actual Snap::getSnapToken() without mocking the SDK,
        // but we can verify the transaction record is created.
        // The Snap SDK will throw in test env, which is caught and returns 500.
        $response = $this->actingAs($user)->postJson('/payment/create');

        // Either success (if Midtrans SDK mock is available) or 500 (SDK failure in test)
        if ($response->status() === 200) {
            $response->assertJsonStructure(['snap_token']);
        } else {
            // Even if the Snap call fails, verify the transaction was created
            $this->assertDatabaseHas('transactions', [
                'user_id' => $user->id,
                'amount'  => 49000,
                'status'  => 'pending',
            ]);
        }
    }
}
