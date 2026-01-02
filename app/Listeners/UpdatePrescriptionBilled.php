<?php

namespace App\Listeners;

use App\Services\PaymentService;
use App\Events\PrescriptionBilled;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePrescriptionBilled
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
    public function handle(PrescriptionBilled $event): void
    {
        if (!$event->visit) {
            return;
        }

        $visit = $event->visit;
        $dto = new SponsorCategoryDto(isNhis: $event->isNhis);
        // 1. Redistribute payments (the waterfall magic)
        $this->paymentService->applyPaymentsWaterfall($visit, $visit->totalPayments(), $dto);

        // 2. Update totals on the visit
        $visit->update([
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $event->isNhis ? $visit->totalNhisBills() : 0,
                'total_capitation'  => $event->isNhis ? $visit->totalPrescriptionCapitations() : 0
            ]);

    }
}
