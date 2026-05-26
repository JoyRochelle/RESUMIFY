<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_webhook_handles_successful_payment()
    {
        Queue::fake();

        $user = User::factory()->create(['role' => 'basic']);
        
        config(['services.midtrans.server_key' => 'dummy_key']);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'midtrans_order_id' => 'TRX-123',
            'amount' => 49000,
            'status' => 'pending',
        ]);

        $payload = [
            'order_id' => 'TRX-123',
            'status_code' => '200',
            'gross_amount' => '49000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'gopay',
            'transaction_id' => 'midtrans-uuid-123',
        ];

        $signature = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'dummy_key');
        $payload['signature_key'] = $signature;

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Callback handled']);

        $transaction->refresh();
        $this->assertEquals('success', $transaction->status);
        
        $user->refresh();
        $this->assertEquals('premium', $user->role);
        $this->assertEquals(0, $user->ai_quota_used);
        
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan' => 'premium',
            'status' => 'active',
        ]);

        Queue::assertPushed(SendPaymentConfirmationJob::class);
    }
    
    public function test_midtrans_webhook_rejects_invalid_signature()
    {
        config(['services.midtrans.server_key' => 'dummy_key']);

        $payload = [
            'order_id' => 'TRX-123',
            'status_code' => '200',
            'gross_amount' => '49000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'invalid_signature_hash',
        ];

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Invalid signature']);
    }
}
