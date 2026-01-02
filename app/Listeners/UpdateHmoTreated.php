<?php

namespace App\Listeners;

use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\Events\PrescriptionTreated;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateHmoTreated
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
    // public function handle(PrescriptionTreated $event): void
    // {
    //     if (!$event->visit) {
    //         return;
    //     }
        
    //     if ($event->isNhis){
    //         $event->visit->update(['total_nhis_bill' => $event->visit->totalNhisBills()]);
    //     }else {
    //         $event->visit->update(['total_hms_bill'    => $event->visit->totalHmsBills()]);
    //     }

    //     $this->paymentService->applyPaymentsWaterfall($event->visit, $event->visit->totalPayments(), $event->isNhis);
    // }

    public function handle(PrescriptionTreated $event): void
    {
        // Check 1: Ensure visit exists before proceeding.
        if (!$event->visit) {
            return;
        }

        /** @var Visit $visit */
        $visit = $event->visit;
        $dto = new SponsorCategoryDto(isNhis: $event->isNhis);
        // 1. Run Waterfall Logic
        // This is necessary because changing the bill status (approval) can shift the 
        // allocation of existing payments on the prescriptions (the core logic of this event).
        $this->paymentService->applyPaymentsWaterfall(
            $visit, 
            $visit->totalPayments(),
            $dto
        );

        // --- 2. Single-Trip Bill Total Update Optimization ---
        
        $visitId = $visit->id;
        
        // Define the SQL subquery for the general (HMS) bill total
        $hmsBillSubquery = "(SELECT COALESCE(SUM(hms_bill), 0) FROM prescriptions WHERE visit_id = {$visitId})";

        // Define the SQL subquery for the NHIS bill total (or 0)
        $nhisBillSubquery = $event->isNhis
            ? "(SELECT COALESCE(SUM(nhis_bill), 0) FROM prescriptions WHERE visit_id = {$visitId})"
            : 0;

        // Perform the single UPDATE query for BILLING FIELDS ONLY
        DB::table('visits')
            ->where('id', $visitId)
            ->update([
                // Update total_hms_bill using raw SQL SUM
                'total_hms_bill'  => DB::raw($hmsBillSubquery),
                
                // Update total_nhis_bill using the conditional raw SQL SUM or 0
                'total_nhis_bill' => DB::raw($nhisBillSubquery),
                
                // NO total_paid UPDATE IS PERFORMED HERE
            ]);
    }
}
