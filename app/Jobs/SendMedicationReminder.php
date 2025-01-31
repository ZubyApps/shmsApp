<?php

namespace App\Jobs;

use App\Models\MedicationChart;
use App\Services\ChurchPlusSmsService;
use App\Services\HelperService;
use App\Services\MedicationChartService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMedicationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly MedicationChart $medicationChart)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService, HelperService $helperService): void
    {
        $gateway = $helperService->nccTextTime() ? 1 : 2;

        $firstName = $this->medicationChart->visit->patient->first_name;
        
        
        $churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', pls be reminded of your medication by '. (new Carbon($this->medicationChart->scheduled_time))->format('g:iA') . ' today. Courtesy: Sandra Hospital Management System', $this->medicationChart->visit->patient->phone, 'SandraHosp', $gateway);
        
        info('medications', ['sent to' => $firstName]);
    }
}
