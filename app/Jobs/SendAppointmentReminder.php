<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\ChurchPlusSmsService;
use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Appointment $appointment)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService, HelperService $helperService): void
    {
        $firstName = $this->appointment->patient->first_name;
        $doctor    = $this->appointment->doctor->username;
        $date      = (new Carbon($this->appointment->date))->format('g:iA');
        $gateway = $helperService->nccTextTime() ? 1 : 2;
        
        info('appointments', ['patient' => $firstName, 'doctor' => $doctor]);

        $churchPlusSmsService
        ->sendSms('Dear ' .$doctor. ', your appointment with ' . $firstName . ' is today by '. $date . '. Courtesy- Sandra Hospital Management System', $this->appointment->doctor->phone_number, 'SandraHosp', $gateway);

        $churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', your appointment with ' .$doctor. ' is today by '. $date . '. Courtesy- Sandra Hospital Management System', $this->appointment->patient->phone, 'SandraHosp', $gateway);
    }
}
