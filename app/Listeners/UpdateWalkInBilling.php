<?php

namespace App\Listeners;

use App\Services\PaymentService;
use App\Events\PrescriptionCreated;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateWalkInBilling
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly PaymentService $paymentService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PrescriptionCreated $event): void
    {
        if (!$event->walkIn) {
            return;
        }

        $dto = new SponsorCategoryDto();
        $this->paymentService->applyPaymentsWaterfall($event->walkIn, $event->walkIn->totalPayments(), $dto);
        
        $event->walkIn->update([
            'total_bill' => $event->walkIn->totalHmsBills(),
            'total_paid' => $event->walkIn->totalPaidPrescriptions() ?? $event->walkIn->totalPayments(),
        ]);

    }
}
