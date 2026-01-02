<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateWardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:ward-data {--dry-run : Show what would be updated without changing data}';//'app:migrate-ward-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate ward IDs to names/bed numbers in consultations and visits';//'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE: No changes will be made.');
        }

        // 1. Update consultations
        $consultSql = "
            UPDATE consultations c
            INNER JOIN wards w ON CAST(c.ward AS UNSIGNED) = w.id
            SET 
                c.ward = w.short_name,
                c.bed_no = w.bed_number
            WHERE c.ward IS NOT NULL 
              AND c.ward != '' 
              AND c.ward REGEXP '^[0-9]+$'
        ";

        if ($dryRun) {
            $count = DB::selectOne("SELECT COUNT(*) as total FROM consultations c WHERE c.ward IS NOT NULL AND c.ward != '' AND c.ward REGEXP '^[0-9]+$'")->total;
            $this->info("Consultations to update: $count");
        } else {
            DB::statement($consultSql);
            $this->info('Consultations updated successfully.');
        }

        // 2. Update visits
        $visitsSql = "
            UPDATE visits v
            INNER JOIN wards w ON CAST(v.ward AS UNSIGNED) = w.id
            SET 
                v.ward = w.short_name,
                v.bed_no = w.bed_number,
                v.ward_id = w.id
            WHERE v.ward IS NOT NULL 
              AND v.ward != '' 
              AND v.ward REGEXP '^[0-9]+$'
        ";

        if ($dryRun) {
            $count = DB::selectOne("SELECT COUNT(*) as total FROM visits v WHERE v.ward IS NOT NULL AND v.ward != '' AND v.ward REGEXP '^[0-9]+$'")->total;
            $this->info("Visits to update: $count");
        } else {
            DB::statement($visitsSql);
            $this->info('Visits updated successfully.');
        }

        if (! $dryRun) {
            $this->info('Ward data migration completed!');
        }

    }
}
