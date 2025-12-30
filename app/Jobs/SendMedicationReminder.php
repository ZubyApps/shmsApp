<?php

namespace App\Jobs;

use App\Models\MedicationChart;
use App\Services\ChurchPlusSmsService;
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

    public $timeout = 12;
    
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
    public function handle(ChurchPlusSmsService $churchPlusSmsService): void
    {     
        $gateway = 1;

        $firstName = $this->medicationChart->visit->patient->first_name;
        
        $churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', pls be reminded of your medication by '. (new Carbon($this->medicationChart->scheduled_time))->format('g:iA') . ' today. Courtesy: Sandra Hospital Management System', $this->medicationChart->visit->patient->phone, 'SandraHosp', $gateway);
        
        // $response == false ? '' : info('medications', ['sent to' => $firstName]);
    }
}
