<?php

namespace App\Jobs;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use App\Services\HelperService;
use App\Services\ChurchPlusSmsService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendCardNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle(ChurchPlusSmsService $churchPlusSmsService, HelperService $helperService): void
    {
        $firstName = $this->patient->first_name;
        $gateway = $helperService->nccTextTime() ? 1 : 2;

        info('card number', ['sent to' => 'LastName']);

        $churchPlusSmsService
                    ->sendSms('Dear ' .$firstName. ', welcome to Sandra Hospital, your Hospital Card Number is '.'['.$this->patient->card_no.'] courtesy of our Hospital Management System', $this->patient->phone, 'SandraHosp', $gateway);
    }
}
