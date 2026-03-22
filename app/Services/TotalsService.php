<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Visit;
use App\Models\Sponsor;
use Illuminate\Support\Facades\DB;

class TotalsService
{
    /**
     * Syncs a single Visit's bill totals from its prescriptions.
     * This is the "Foundation" sync.
     */
    public function syncVisitTotals(Visit $visit): void
    {
        $visitId = $visit->id;
        $isNhis = $visit->sponsor->category_name === 'NHIS';
        $isHMO = $visit->sponsor->category_name === 'HMO';

        DB::table('visits')
            ->where('id', $visitId)
            ->update([
                // 1. Always sum the HMS Bill from prescriptions
                'total_hms_bill'  => DB::raw("(SELECT COALESCE(SUM(hms_bill), 0) FROM prescriptions WHERE visit_id = $visitId)"),

                // 2. Conditional NHIS Bill
                'total_nhis_bill' => $isNhis 
                    ? DB::raw("(SELECT COALESCE(SUM(nhis_bill), 0) FROM prescriptions WHERE visit_id = $visitId)") 
                    : 0,

                'total_capitation' => $isNhis 
                    ? DB::raw("(SELECT COALESCE(SUM(capitation), 0) FROM prescriptions WHERE visit_id = {$visitId})")
                    : 0,

                // 3. THE SAFETY NET: Use the GREATEST of itemized 'paid' OR total 'amount_paid'
                // This ensures if Waterfall fails, the Visit total still reflects the cash received.
                'total_paid'      => $isHMO ? DB::raw("(SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE visit_id = {$visitId})") : DB::raw("
                    GREATEST(
                        (SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE visit_id = $visitId),
                        (SELECT COALESCE(SUM(amount_paid), 0) FROM payments WHERE visit_id = $visitId)
                    )
                "),
            ]);
            
        // Chain the update to Patient and Sponsor
        $this->syncPatientTotals($visit->patient_id);
        $this->syncSponsorTotals($visit->sponsor);
    }

    /**
     * Syncs the Patient's lifetime totals.
     * Accounts for the "Chameleon" sponsor logic (NHIS vs Cash).
     */
    public function syncPatientTotals(int $patientId): void
    {
        DB::table('patients')
            ->where('id', $patientId)
            ->update([
                'total_bill' => DB::raw("(
                    SELECT SUM(
                        CASE 
                            WHEN sponsors.category_name = 'NHIS' THEN visits.total_nhis_bill 
                            ELSE visits.total_hms_bill 
                        END
                    ) 
                    FROM visits 
                    JOIN sponsors ON visits.sponsor_id = sponsors.id 
                    WHERE visits.patient_id = $patientId
                )"),
                'total_paid'     => DB::raw("(SELECT COALESCE(SUM(total_paid), 0) FROM visits WHERE patient_id = $patientId)"),
                'total_discount' => DB::raw("(SELECT COALESCE(SUM(discount), 0) FROM visits WHERE patient_id = $patientId)"),
            ]);
    }

    /**
     * Syncs Corporate/Family Sponsor totals.
     * Only runs for specific categories to keep the database lean.
     */
    public function syncSponsorTotals(Sponsor $sponsor): void
    {
        if (!$this->sponsorsAllowed($sponsor, ['Family', 'Retainership'])) {
            return;
        }

        $sponsorId = $sponsor->id;

        DB::table('sponsors')
            ->where('id', $sponsorId)
            ->update([
                'total_bill' => DB::raw("(SELECT COALESCE(SUM(total_hms_bill), 0) FROM visits WHERE sponsor_id = $sponsorId)"),
                'total_discount' => DB::raw("(SELECT COALESCE(SUM(discount), 0) FROM visits WHERE sponsor_id = $sponsorId)"),
                'total_paid'     => DB::raw("(SELECT COALESCE(SUM(total_paid), 0) FROM visits WHERE sponsor_id = $sponsorId)"),
            ]);
    }

    /**
     * Helper to determine if a sponsor needs aggregate tracking.
     */
    private function sponsorsAllowed(Sponsor $sponsor, array $categories): bool
    {
        return in_array($sponsor->category_name, $categories);
    }
}