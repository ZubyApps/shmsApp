<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncFinancialTotals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'finance:sync-totals {--scope=all : Options: all, visits, patients, sponsors}';

    /**
     * The console command description.
     */
    protected $description = 'Performs a high-performance SQL-level reconciliation of all financial totals.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scope = $this->option('scope');
        $this->info("Starting financial reconciliation (Scope: {$scope})...");

        try {
            DB::transaction(function () use ($scope) {
                // STEP 1: Sync Visits (The Foundation)
                if (in_array($scope, ['all', 'visits'])) {
                    $this->comment('Updating Visit totals from Prescriptions and Payments...');
                    DB::statement("
                        UPDATE visits v
                        SET 
                            v.total_hms_bill = (SELECT COALESCE(SUM(p.hms_bill), 0) FROM prescriptions p WHERE p.visit_id = v.id),
                            v.total_nhis_bill = (
                                SELECT CASE 
                                    WHEN (SELECT category_name FROM sponsors WHERE id = v.sponsor_id) = 'NHIS' 
                                    THEN (SELECT COALESCE(SUM(nhis_bill), 0) FROM prescriptions WHERE visit_id = v.id)
                                    ELSE 0 
                                END
                            ),
                            v.total_paid = (
                                SELECT GREATEST(
                                    (SELECT COALESCE(SUM(paid), 0) FROM prescriptions WHERE visit_id = v.id),
                                    (SELECT COALESCE(SUM(amount_paid), 0) FROM payments WHERE visit_id = v.id)
                                )
                            )
                    ");
                }

                // STEP 2: Sync Patients (The Chameleon Logic)
                if (in_array($scope, ['all', 'patients'])) {
                    $this->comment('Updating Patient totals based on Visit history...');
                    DB::statement("
                        UPDATE patients p
                        SET 
                            p.total_bill = (
                                SELECT COALESCE(SUM(
                                    CASE 
                                        WHEN s.category_name = 'NHIS' THEN v.total_nhis_bill 
                                        ELSE v.total_hms_bill 
                                    END
                                ), 0)
                                FROM visits v 
                                JOIN sponsors s ON v.sponsor_id = s.id 
                                WHERE v.patient_id = p.id
                            ),
                            p.total_paid = (SELECT COALESCE(SUM(v2.total_paid), 0) FROM visits v2 WHERE v2.patient_id = p.id),
                            p.total_discount = (SELECT COALESCE(SUM(v3.discount), 0) FROM visits v3 WHERE v3.patient_id = p.id)
                    ");
                }

                // STEP 3: Sync Sponsors (Corporate/Family Only)
                if (in_array($scope, ['all', 'sponsors'])) {
                    $this->comment('Updating Corporate/Family Sponsor totals...');
                    DB::statement("
                        UPDATE sponsors sp
                        SET 
                            sp.total_bill = (SELECT COALESCE(SUM(v.total_hms_bill), 0) FROM visits v WHERE v.sponsor_id = sp.id),
                            sp.total_discount = (SELECT COALESCE(SUM(v.discount), 0) FROM visits v WHERE v.sponsor_id = sp.id),
                            sp.total_paid = (
                                SELECT GREATEST(
                                    (SELECT COALESCE(SUM(pr.paid), 0) FROM prescriptions pr JOIN visits vi ON pr.visit_id = vi.id WHERE vi.sponsor_id = sp.id),
                                    (SELECT COALESCE(SUM(v2.total_paid), 0) FROM visits v2 WHERE v2.sponsor_id = sp.id)
                                )
                            )
                        WHERE sp.category_name IN ('Family', 'Retainership')
                    ");
                }
            });

            $this->info('Success: All financial totals have been synchronized.');

        } catch (\Exception $e) {
            $this->error('Reconciliation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
