<?php

namespace App\Listeners;

use App\Events\CapitationPaymentCreated;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CapitationPaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCapitationPaymentCreated
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly CapitationPaymentService $capitationPaymentService
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CapitationPaymentCreated $event): void
    {
        
        $payment = $event->capitationPayment;
        
        // Ensure the related models are available
        $sponsor = $payment->sponsor; // Assumes relationship is loaded or lazy-loads efficiently
        $date = $payment->month_paid_for;
        $amount = (float)$payment->amount_paid;

        // Execute the highly optimized bulk update logic
        // This method contains its own single, necessary transaction block
        $this->capitationPaymentService->seiveCapitationPayment($sponsor, $date, $amount);
    }
}
