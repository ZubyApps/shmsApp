<?php

namespace App\Jobs;

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
    public function __construct(
        private string $firstName, 
        private string $phone, 
        private string $scheduledTime
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService): void
    {     
        $gateway = 1;

        $time = (new Carbon($this->scheduledTime))->format('g:iA');
        
        $response = $churchPlusSmsService
        
        ->sendSms('Dear ' .$this->firstName. ', pls be reminded of your medication by '. $time . ' today. Courtesy: Ufor Hospital Management System', $this->phone, 'UforHosp', $gateway);
        
        $response == false ? '' : info('medications', ['sent to' => $this->firstName]);
    }
}
