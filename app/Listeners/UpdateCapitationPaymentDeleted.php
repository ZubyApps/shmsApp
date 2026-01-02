<?php

namespace App\Listeners;

use App\Models\Sponsor;
use App\Events\CapitationPaymentDeleted;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CapitationPaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCapitationPaymentDeleted
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
    public function handle(CapitationPaymentDeleted $event): void
    {
        // 1. Find the Sponsor
        $sponsor = Sponsor::find($event->sponsorId);
        $date = $event->date;
        
        if (!$sponsor) {
            return;
        }

        // 2. Trigger the Re-Sieve
        // Since the CapitationPayment record was just deleted in the transaction,
        // the seiveCapitationPayment method will now execute the following:
        // a) Look up the SUM of all *remaining* Capitation Payments for the month (should be lower).
        // b) Spread that new total across all eligible prescriptions (BULK UPDATE).
        // c) Recalculate all related visits (BULK UPDATE).
        
        // We pass 'null' for the amount, forcing the service method to do the lookup inside its transaction.
        $this->capitationPaymentService->seiveCapitationPayment($sponsor, $date, null);
    }
}
