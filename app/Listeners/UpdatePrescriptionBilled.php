<?php

namespace App\Listeners;

use App\DataObjects\SponsorCategoryDto;
use App\Events\PrescriptionBilled;
use App\Services\PaymentService;
use App\Services\TotalsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePrescriptionBilled
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly PaymentService $paymentService, private readonly TotalsService $totalsService)
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
        $patientId = $visit->patient_id;
        $dto = new SponsorCategoryDto(isNhis: $event->isNhis);
        // 1. Redistribute payments (the waterfall magic)
        $this->paymentService->applyPaymentsWaterfall($visit, $visit->totalPayments(), $dto);

        // 2. Update totals on the visit
        $this->totalsService->syncVisitTotals($visit);
        // $visit->update([
        //         'total_hms_bill'    => $visit->totalHmsBills(),
        //         'total_nhis_bill'   => $event->isNhis ? $visit->totalNhisBills() : 0,
        //         'total_capitation'  => $event->isNhis ? $visit->totalPrescriptionCapitations() : 0
        //     ]);

        // DB::table('patients')
        // ->where('id', $patientId)
        // ->update([
        //     'total_bill' => DB::raw("(
        //         SELECT SUM(
        //             CASE 
        //                 WHEN sponsors.category_name = 'NHIS' THEN visits.total_nhis_bill 
        //                 ELSE visits.total_hms_bill 
        //             END
        //         ) 
        //         FROM visits 
        //         JOIN sponsors ON visits.sponsor_id = sponsors.id 
        //         WHERE visits.patient_id = {$patientId}
        //     )"),
        //     // 'total_paid'     => DB::raw("(SELECT SUM(total_paid) FROM visits WHERE patient_id = {$patientId})"),
        //     // 'total_discount' => DB::raw("(SELECT SUM(discount) FROM visits WHERE patient_id = {$patientId})"),
        // ]);

    }
}
