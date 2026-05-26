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
}
