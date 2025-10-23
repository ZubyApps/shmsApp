<?php

namespace App\Jobs;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use App\Services\ChurchPlusSmsService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendCardNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 12;
    
    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Patient $patient)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService): void
    {
        $firstName = $this->patient->first_name;
        $cardNumber = $this->patient->card_no;
        $phoneNumber = $this->patient->phone;
        $gateway = 1;
        
        $message = 'Dear ' . $firstName . ', welcome to Sandra Hospital, your Hospital Card Number is (' . $cardNumber . ') courtesy: Sandra Hospital Management System';
        
        $response = $churchPlusSmsService->sendSms($message, $phoneNumber, 'SandraHosp', $gateway);

        $response == false ? '' : info('card number', ['sent to' => $firstName]);
    }
}
