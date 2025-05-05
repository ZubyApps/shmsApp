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

    public $timeout = 12;
    
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
        $patientFirstName = $this->appointment->patient->first_name;
        $doctorUsername = $this->appointment->doctor->username;
        $appointmentTime = (new Carbon($this->appointment->date))->format('g:iA');
        $gateway = $helperService->nccTextTime() ? 1 : 1;

        info('appointments', ['patient' => $patientFirstName, 'doctor' => $doctorUsername]);

        $messageForDoctor = 'Dear ' . $doctorUsername . ', your appointment with ' . $patientFirstName . ' is today by ' . $appointmentTime . '. Courtesy- Sandra Hospital Management System';
        $messageForPatient = 'Dear ' . $patientFirstName . ', your appointment with ' . $doctorUsername . ' is today by ' . $appointmentTime . '. Courtesy- Sandra Hospital Management System';

        $churchPlusSmsService->sendSms($messageForDoctor, $this->appointment->doctor->phone_number, 'SandraHosp', $gateway);
        $churchPlusSmsService->sendSms($messageForPatient, $this->appointment->patient->phone, 'SandraHosp', $gateway);
    }
}
