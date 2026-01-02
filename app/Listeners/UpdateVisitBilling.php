<?php

namespace App\Listeners;

use App\DataObjects\SponsorCategoryDto;
use App\Services\PaymentService;
use App\Events\PrescriptionCreated;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CapitationPaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;

use function Laravel\Prompts\info;

class UpdateVisitBilling
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CapitationPaymentService $capitationPaymentService
        )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PrescriptionCreated $event): void
    {
        if (!$event->visit) {
            return;
        }

        $visit = $event->visit;
        $resource = $event->resource;
        $isNhis = $event->isNhis;
        $dto = new SponsorCategoryDto(isNhis: $isNhis);
        
        // 1. Redistribute payments (the waterfall magic)
        $this->paymentService->applyPaymentsWaterfall($visit, $visit->totalPayments(), $dto);
        if ($isNhis){
            $sponsor = $event->visit->sponsor;
            $this->capitationPaymentService->seiveCapitationPayment($sponsor, $event->prescription->created_at, null);
        }

        // 2. Update the cached totals on the visit
        $visit->update([
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_paid'        => max($visit->totalPaidPrescriptions(), $visit->totalPayments()),// ? $visit->totalPaidPrescriptions() : $visit->totalPayments(),
                'total_nhis_bill'   => $isNhis ? $visit->totalNhisBills() : 0, 
                'total_capitation'  => $visit->totalPrescriptionCapitations(),
                'pharmacy_done_by'  => $event->resource && in_array($event->resource->category, ['Medications', 'Consumables']) ? null : $visit->pharmacy_done_by,
                'nurse_done_by'     => $event->resource->sub_category == 'Injectable' || $resource->category == 'Consumables' ? null : $visit->nurse_done_by,
                'hmo_done_by'       => null
            ]);
    }
}
