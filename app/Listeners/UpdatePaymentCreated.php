<?php

namespace App\Listeners;

use App\Models\Visit;
use App\Models\WalkIn;
use App\Events\PaymentCreated;
use App\Models\MortuaryService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePaymentCreated
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
    // public function handle(PaymentCreated $event): void
    // {
    //     if (($event->relatedModel instanceof Visit)) {
    //         $visit = $event->relatedModel->refresh();

    //         // 1. Determine Context (NHIS flag)
    //         $isNhis = $visit->sponsor->category_name === 'NHIS';

    //         // 2. Recalculate Total Payments (MUST be done inside the listener, after the event)
    //         $totalPayments = $visit->totalPayments();

    //         // 3. Run Optimized Waterfall Logic
    //         $this->paymentService->applyPaymentsWaterfall($visit, $totalPayments, $isNhis);

    //         // 4. Update Visit Totals
    //         $totalHmsBill = $visit->totalHmsBills();
    //         $totalPaid = $visit->totalPaidPrescriptions();
            
    //         $updateData = [
    //             'total_hms_bill' => $totalHmsBill,
    //             'total_nhis_bill' => $isNhis ? $visit->totalNhisBills() : 0,
    //         ];
            
    //         if ($visit->sponsor->category_name == 'HMO') {
    //             $updateData['total_paid'] = $totalPaid;
    //         } else {
    //             $updateData['total_paid'] = $totalPayments;
    //         }
            
    //         $visit->update($updateData);
    //     }

    //     if (($event->relatedModel instanceof WalkIn)) {
    //         $walkIn = $event->relatedModel->refresh();
            
    //         // WalkIn is implicitly Non-NHIS (no sponsor)
    //         $totalPayments = $walkIn->totalPayments(); 
            
    //         // Run Optimized Waterfall Logic (passing false for $isNhis)
    //         $this->paymentService->applyPaymentsWaterfall($walkIn, $totalPayments, false); 
            
    //         // Update WalkIn Totals
    //         $walkIn->update([
    //             'total_bill' => $walkIn->totalHmsBills(),
    //             'total_paid' => $walkIn->totalPaidPrescriptions() ?? $totalPayments,
    //         ]);
    //     }

    //     if (($event->relatedModel instanceof MortuaryService)) {
    //         $mortuaryService = $event->relatedModel->refresh();
            
    //         // mortuaryService is implicitly Non-NHIS (no sponsor)
    //         $totalPayments = $mortuaryService->totalPayments(); 
            
    //         // Run Optimized mortuaryService Logic (passing false for $isNhis)
    //         $this->paymentService->applyPaymentsWaterfall($mortuaryService, $totalPayments, false); 
            
    //         // Update mortuaryService Totals
    //         $walkIn->update([
    //             'total_bill' => $mortuaryService->totalHmsBills(),
    //             'total_paid' => $mortuaryService->totalPaidPrescriptions() ?? $totalPayments,
    //         ]);
    //     }
    // }

    public function handle(PaymentCreated $event): void
    {
        $model = $event->relatedModel;

        // Ensure we have the latest state of the related model
        $model->refresh();

        if ($model instanceof Visit) {
            $this->handleVisitUpdate($model);
        } elseif ($model instanceof WalkIn || $model instanceof MortuaryService) {
            $this->handleWalkInOrMortuaryUpdate($model);
        }
    }

    // --- Private Helper Methods ---

    private function handleVisitUpdate(Visit $visit): void
    {
        // 1. Determine Context
        $isNhis = $visit->sponsor->category_name === 'NHIS';

        // 2. Recalculate Total Payments (Must be done after payment creation)
        $totalPayments = $visit->totalPayments();

        // 3. Run Optimized Waterfall Logic (Multi-trip necessity, cannot be raw SQL)
        $this->paymentService->applyPaymentsWaterfall($visit, $totalPayments, $this->getSponsorDto($isNhis));

        // 4. Update Visit Totals (Single Database Trip Optimization)
        
        // Define SQL segments based on condition
        $visitId = $visit->id;
        $totalPaidSourceSql = ($visit->sponsor->category_name === 'HMO') 
        ? "(SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE visit_id = {$visitId})" 
        : $totalPayments;

        $totalNhisBillSql = $isNhis 
            ? "(SELECT COALESCE(SUM(nhis_bill), 0) FROM prescriptions WHERE visit_id = {$visitId})" 
            : 0;

        DB::table('visits')
            ->where('id', $visitId)
            ->update([
                // total_hms_bill wrap with COALESCE
                'total_hms_bill'  => DB::raw("(SELECT COALESCE(SUM(hms_bill), 0) FROM prescriptions WHERE visit_id = {$visitId})"),
                
                'total_paid'      => DB::raw($totalPaidSourceSql),
                
                'total_nhis_bill' => DB::raw($totalNhisBillSql),
            ]);
    }

    private function handleWalkInOrMortuaryUpdate(WalkIn|MortuaryService $model): void
    {
        $id = $model->id;
        $table = $model->getTable(); // 'walk_ins' or 'mortuary_services'
        
        // WalkIn/Mortuary are Non-NHIS (no sponsor), so $isNhis = false.
        $totalPayments = $model->totalPayments();
        
        // Run Optimized Waterfall Logic (Multi-trip necessity)
        $this->paymentService->applyPaymentsWaterfall($model, $totalPayments, $this->getSponsorDto()); 
        
        // Update Totals (Single Database Trip Optimization)
        
        // The total_paid needs to be totalPaidPrescriptions() (SUM) OR totalPayments if SUM is null.
        // We handle the SUM(paid) in SQL, and since totalPaidPrescriptions() is only null if no prescriptions exist, 
        // we can simplify the logic to rely on the SUM or 0 from the database.
        
        DB::table($table)
            ->where('id', $id)
            ->update([
                // Calculate Total Bill (SUM(hms_bill))
                'total_bill' => DB::raw("(SELECT COALESCE(SUM(hms_bill), 0) FROM prescriptions WHERE walk_in_id = {$id} OR mortuary_service_id = {$id})"),
                
                // Calculate Total Paid (SUM(paid) of prescriptions OR use the totalPayments static value)
                // Assuming the original logic that if totalPaidPrescriptions() is NULL, use totalPayments.
                // We will rely on PHP for the condition, but use raw SQL for the calculation.
                'total_paid' => DB::raw("
                    CASE
                        WHEN (SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE walk_in_id = {$id} OR mortuary_service_id = {$id}) IS NOT NULL 
                        THEN (SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE walk_in_id = {$id} OR mortuary_service_id = {$id})
                        ELSE {$totalPayments}
                    END
                ")
            ]);
    }

    private function getSponsorDto(?bool $isNhis = false)
    {
        return new SponsorCategoryDto(isNhis: $isNhis);
    }
}
