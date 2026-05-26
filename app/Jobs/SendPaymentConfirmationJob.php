<?php

namespace App\Jobs;

use App\Mail\PaymentConfirmationMail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Backoff intervals (seconds) between retries.
     */
    public $backoff = [10, 60, 300];

    public $user;
    public $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Transaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new PaymentConfirmationMail($this->user, $this->transaction));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('Payment confirmation email failed', [
            'user_id' => $this->user->id,
            'transaction_id' => $this->transaction->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
