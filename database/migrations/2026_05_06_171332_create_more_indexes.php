<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. VISITS TABLE: The core of Doctor, Pharmacy, and Billing lists
        Schema::table('visits', function (Blueprint $table) {
            // DOCTOR & GENERAL: Speeds up filtered worklists (Outpatient/Inpatient/ANC) 
            // and the 'consulted' order direction.
            $table->index(['closed', 'consulted'], 'idx_consulted_status');
            $table->index(['closed', 'created_at'], 'idx_created_at_status');

            // BILLING & HMO: Speeds up "Cash" vs "HMO" pay_class filtering and sponsor lookups.
            $table->index(['sponsor_id', 'closed', 'created_at'], 'idx_visits_finance_lookup');
            $table->index(['discharge_reason', 'admission_status'], 'visits_discharge_status_idx');
            // Covers: nurse_done_by check, closed check, AND the sorting by consulted
            $table->index(['nurse_done_by', 'closed', 'consulted'], 'idx_nurse_worklist_active');
            // For doctor's "My Patients" filter
            $table->index(['doctor_id', 'doctor_done_by', 'closed'], 'doc_my_patients_idx');
            // For general listing
            $table->index(['consulted', 'admission_status', 'visit_type'], 'doc_listing_idx');
            // Optimizes the main HMO dashboard view (where hmo_done_by is null)
            $table->index(['hmo_done_by', 'consulted', 'admission_status'], 'hmo_dashboard_main_idx');
        });

        // 2. PATIENTS TABLE: Your search engine
        Schema::table('patients', function (Blueprint $table) {
            $table->index('phone');
        });

        // 3. PRESCRIPTIONS TABLE: High-traffic Pharmacy & Billing lines
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->index(['approved', 'rejected', 'created_at'], 'pending_prescriptions_idx');
            $table->index(['visit_id', 'created_at'], 'idx_pres_visit_history');
            $table->index(['result_date', 'dispense_comment', 'created_at'], 'prescriptions_lab_pending_idx');
            $table->index('discontinued', 'discontinued_idx');
            $table->index(['visit_id', 'chartable', 'discontinued'], 'presc_charting_lookup_idx');
            // Covers the labPrescribed and labDone counts
            $table->index(['visit_id', 'result_date'], 'presc_lab_status_idx');
            // This index covers pharmacyItems() + billed/dispensed checks
            // Assuming pharmacyItems() filters by category/sub_category
            $table->index(['visit_id', 'qty_billed', 'qty_dispensed'], 'pharmacy_worklist_idx');
        });

        // 5. RESOURCES TABLE: Inventory & Pricing
        Schema::table('resources', function (Blueprint $table) {
            // Speeds up drug name searches and category filtering
            $table->index(['sub_category']);
            $table->index(['category']);
        });

        // 6. SPONSORS TABLE: Category filtering
        Schema::table('sponsors', function (Blueprint $table) {
            // Speeds up filtering visits by pay_class (Cash/HMO)
            $table->index('category_name');
        });

        // medication_charts optimization
        Schema::table('medication_charts', function (Blueprint $table) {
            // This supports the main dashboard: Unfinished meds by time
            $table->index(['status', 'scheduled_time'], 'med_charts_pending_time_idx');
            
            $table->index(['prescription_id', 'created_at'], 'med_charts_idx');
            // Supports the join/count logic
            $table->index(['visit_id', 'created_at'], 'idx_med_charts_visit_history');
            $table->index(['visit_id', 'dose_given'], 'med_charts_given_count_idx');
        });

        Schema::table('nursing_charts', function (Blueprint $table) {
            $table->index(['status', 'scheduled_time'], 'nursing_charts_pending_time_idx');
            // For: time_done counts
            $table->index(['visit_id', 'created_at'], 'idx_med_charts_visit_history');
            $table->index(['visit_id', 'time_done'], 'nursing_charts_done_idx');
            $table->index(['prescription_id', 'created_at'], 'nursing_charts_idx');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex('idx_consulted_status');
            $table->dropIndex('idx_created_at_status');
            $table->dropIndex('idx_visits_finance_lookup');
            $table->dropIndex('visits_discharge_status_idx');
            $table->dropIndex('idx_nurse_worklist_active');
            $table->dropIndex('doc_my_patients_idx');
            $table->dropIndex('doc_listing_idx');
            $table->dropIndex('hmo_dashboard_main_idx');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['phone']);
        });
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex('pending_prescriptions_idx');
            $table->dropIndex('idx_pres_visit_history');
            $table->dropIndex('prescriptions_lab_pending_idx');
            $table->dropIndex('discontinued_idx');
            $table->dropIndex('presc_charting_lookup_idx');
            $table->dropIndex('presc_lab_status_idx');
            $table->dropIndex('pharmacy_worklist_idx');
        });
        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex(['sub_category']);
            $table->dropIndex(['category']);
            
        });
        Schema::table('sponsors', fn(Blueprint $table) => $table->dropIndex('idx_sponsors_category'));
        Schema::table('medication_charts', function (Blueprint $table) {
            $table->dropIndex('med_charts_pending_time_idx');
            $table->dropIndex('med_charts_idx');
            $table->dropIndex('idx_med_charts_visit_history');
            $table->dropIndex('med_charts_given_count_idx');
        });
        Schema::table('nursing_charts', function (Blueprint $table) {
            $table->dropIndex('idx_med_charts_visit_history');
            $table->dropIndex('nursing_charts_done_idx');
            $table->dropIndex('nursing_charts_idx');
        });
    }
};