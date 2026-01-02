<?php

namespace App\Listeners;

use App\Models\Visit;
use App\Models\WalkIn;
use App\Models\MortuaryService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\Events\PrescriptionDeleted;
use App\DataObjects\SponsorCategoryDto;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CapitationPaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePrescriptionDeleted
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
    public function handle(PrescriptionDeleted $event): void
    {
        $model = $event->relatedModel->refresh(); // Refresh for data integrity
        $isNhis = ($model instanceof Visit && $model->sponsor->category_name === 'NHIS');

        // 1. Run Waterfall Logic (Necessary Trip)
        $totalPayments = $model->totalPayments();
        
        $dto = new SponsorCategoryDto(isNhis: $isNhis);
        // Use the unified waterfall method for all models
        $this->paymentService->applyPaymentsWaterfall($model, $totalPayments, $dto);

        // 2. Perform Capitation Sieve (If applicable)
        if ($isNhis && $model->sponsor) {
            // Note: Use the original prescription created_at time from the event
            $this->capitationPaymentService->seiveCapitationPayment($model->sponsor, $event->prescriptionCreatedAt);
        }

        // 3. Single-Trip Total Update (Optimal Efficiency)
        if ($model instanceof Visit) {
            $this->updateVisitTotals($model, $totalPayments, $isNhis);
        } elseif ($model instanceof WalkIn || $model instanceof MortuaryService) {
            $this->updateWalkInOrMortuaryTotals($model, $totalPayments);
        }
    }
    
    // --- PRIVATE UPDATE METHODS (Using single-trip DB::raw) ---

    private function updateVisitTotals(Visit $visit, float $totalPayments, bool $isNhis): void
    {
        $visitId = $visit->id;

        // SQL segments for conditional logic
        $totalPaidSourceSql = ($visit->sponsor->category_name === 'HMO') 
            ? "(SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE visit_id = {$visitId})"
            : $totalPayments; 

        $totalNhisBillSql = $isNhis 
            ? "(SELECT COALESCE(SUM(nhis_bill), 0) FROM prescriptions WHERE visit_id = {$visitId})" 
            : 0;
            
        $totalCapitationSql = $isNhis 
            ? "(SELECT COALESCE(SUM(capitation_fee), 0) FROM prescriptions WHERE visit_id = {$visitId})"
            : 0;

        DB::table('visits')
            ->where('id', $visitId)
            ->update([
                'total_hms_bill'  => DB::raw("(SELECT COALESCE(SUM(hms_bill), 0) FROM prescriptions WHERE visit_id = {$visitId})"),
                'total_nhis_bill' => DB::raw($totalNhisBillSql),
                'total_capitation'=> DB::raw($totalCapitationSql), // NEW
                'total_paid'      => DB::raw($totalPaidSourceSql),
            ]);
        info('prescription update ran');
    }

    private function updateWalkInOrMortuaryTotals(WalkIn|MortuaryService $model, float $totalPayments): void
    {
        $id = $model->id;
        $table = $model->getTable(); 
        $foreignKey = ($model instanceof WalkIn) ? 'walk_in_id' : 'mortuary_service_id';
        
        DB::table($table)
            ->where('id', $id)
            ->update([
                'total_bill' => DB::raw("(SELECT COALESCE(SUM(hms_bill), 0) FROM prescriptions WHERE {$foreignKey} = {$id})"),
                // The SUM(paid) or totalPayments logic
                'total_paid' => DB::raw("
                    CASE
                        WHEN (SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE {$foreignKey} = {$id}) IS NOT NULL 
                        THEN (SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE {$foreignKey} = {$id})
                        ELSE {$totalPayments}
                    END
                ")
            ]);
    }
}
