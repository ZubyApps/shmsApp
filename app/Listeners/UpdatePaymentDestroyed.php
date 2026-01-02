<?php

namespace App\Listeners;

use App\Models\Visit;
use App\Models\WalkIn;
use App\Models\MortuaryService;
use App\Events\PaymentDestroyed;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePaymentDestroyed
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
    // public function handle(PaymentDestroyed $event): void
    // {
    //     $model = $event->relatedModel;
        
    //     // Ensure the model instance is fresh before doing heavy recalculations
    //     $model->refresh(); 

    //     $isNhis = false;
    //     $updateData = [];

    //     // 1. Determine context and required updates based on model type
    //     if ($model instanceof Visit) {
    //         $isNhis = $model->sponsor->category_name === 'NHIS';
            
    //     } elseif ($model instanceof WalkIn) {
    //         // WalkIn has no sponsor, implicit non-NHIS
    //         $updateData['total_bill'] = $model->totalHmsBills(); 
            
    //     } elseif ($model instanceof MortuaryService) {
    //         // MortuaryService has no sponsor, implicit non-NHIS
    //         $updateData['total_bill'] = $model->totalHmsBills(); 
            
    //     } else {
    //         return; // Unknown model type
    //     }

    //     // 2. Run Waterfall Logic (Using the unified function)
    //     $totalPayments = $model->totalPayments();
    //     $this->paymentService->applyPaymentsWaterfall($model, $totalPayments, $isNhis);
        
    //     // 3. Finalize Update Data
    //     $totalPaid = $model->totalPaidPrescriptions();
        
    //     // Common updates
    //     $updateData['total_paid'] = $totalPaid ?? $totalPayments;

    //     if ($model instanceof Visit) {
    //         // Visit-specific updates
    //         $updateData['total_hms_bill'] = $model->totalHmsBills();
    //         $updateData['total_nhis_bill'] = $isNhis ? $model->totalNhisBills() : 0;
            
    //         if ($model->sponsor->category_name == 'HMO') {
    //             $updateData['total_paid'] = $totalPaid;
    //         } else {
    //             $updateData['total_paid'] = $totalPayments;
    //         }
    //     }
        
    //     // 4. Perform final update (1 Query)
    //     $model->update($updateData);
    
    // }

    public function handle(PaymentDestroyed $event): void
    {
        $model = $event->relatedModel;

        // Ensure we have the latest state (necessary safety for event listeners)
        $model->refresh();

        if ($model instanceof Visit) {
            $this->handleVisitUpdate($model);
        } elseif ($model instanceof WalkIn || $model instanceof MortuaryService) {
            $this->handleWalkInOrMortuaryUpdate($model);
        }
    }

    // --- Private Helper Methods ---

    /**
     * Handles recalculation and single-trip update for a Visit.
     */
    private function handleVisitUpdate(Visit $visit): void
    {
        // 1. Determine Context
        $isNhis = $visit->sponsor->category_name === 'NHIS';

        // 2. Recalculate Total Payments (Necessary PHP trip for the waterfall input)
        $totalPayments = $visit->totalPayments();

        // 3. Run Optimized Waterfall Logic (Multi-trip necessity, cannot be raw SQL)
        $this->paymentService->applyPaymentsWaterfall($visit, $totalPayments, $this->getSponsorDto($isNhis));

        // 4. Update Visit Totals (SINGLE Database Trip Optimization)
        $visitId = $visit->id;
        
        // // Define SQL segments for conditional 'total_paid' logic
        // $totalPaidSourceSql = ($visit->sponsor->category_name === 'HMO') 
        //     ? "(SELECT SUM(paid) FROM prescriptions WHERE visit_id = {$visitId})" // SUM(paid) for HMO
        //     : $totalPayments; // Static value for cash/individual payment (already retrieved)

        // // Define SQL segments for conditional 'total_nhis_bill' logic
        // $totalNhisBillSql = $isNhis 
        //     ? "(SELECT SUM(nhis_bill) FROM prescriptions WHERE visit_id = {$visitId})" 
        //     : 0;

        // DB::table('visits')
        //     ->where('id', $visitId)
        //     ->update([
        //         // total_hms_bill (calculated via SUM subquery)
        //         'total_hms_bill'  => DB::raw("(SELECT SUM(hms_bill) FROM prescriptions WHERE visit_id = {$visitId})"),
                
        //         // total_paid (calculated conditionally)
        //         'total_paid'      => DB::raw($totalPaidSourceSql),
                
        //         // total_nhis_bill (calculated conditionally)
        //         'total_nhis_bill' => DB::raw($totalNhisBillSql),
        //     ]);

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

    /**
     * Handles recalculation and single-trip update for WalkIn or MortuaryService.
     */
    private function handleWalkInOrMortuaryUpdate(WalkIn|MortuaryService $model): void
    {
        $id = $model->id;
        $table = $model->getTable(); 
        
        // Use the appropriate foreign key based on the table
        $foreignKey = ($model instanceof WalkIn) ? 'walk_in_id' : 'mortuary_service_id';

        // 1. Recalculate Total Payments
        $totalPayments = $model->totalPayments();
        
        // 2. Run Optimized Waterfall Logic (Multi-trip necessity)
        $this->paymentService->applyPaymentsWaterfall($model, $totalPayments, $this->getSponsorDto()); 
        
        // 3. Update Totals (SINGLE Database Trip Optimization)
        
        // The SQL logic to determine total_paid is more complex due to the "if null use totalPayments" rule.
        // We use DB::raw with CASE to handle it in one trip.
        
        DB::table($table)
            ->where('id', $id)
            ->update([
                // Calculate Total Bill (SUM(hms_bill))
                'total_bill' => DB::raw("(SELECT SUM(hms_bill) FROM prescriptions WHERE {$foreignKey} = {$id})"),
                
                // Calculate Total Paid (SUM(paid) or use the static $totalPayments value)
                'total_paid' => DB::raw("
                    CASE
                        WHEN (SELECT SUM(paid) FROM prescriptions WHERE {$foreignKey} = {$id}) IS NOT NULL 
                        THEN (SELECT SUM(paid) FROM prescriptions WHERE {$foreignKey} = {$id})
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
