<?php

namespace App\Listeners;

use App\Services\PaymentService;
use App\Events\PrescriptionCreated;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateMortuaryBilling
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(PrescriptionCreated $event): void
    {
        if (!$event->mortuary) {
            return;
        }

        $dto = new SponsorCategoryDto();
        
        $this->paymentService->applyPaymentsWaterfall($event->walkIn, $event->walkIn->totalPayments(), $dto);
        
        $event->mortuary->update([
            'total_bill' => $event->mortuary->totalHmsBills(),
            'total_paid' => $event->mortuary->totalPaidPrescriptions() ?? $event->mortuary->totalPayments(),
        ]);

    }
}
