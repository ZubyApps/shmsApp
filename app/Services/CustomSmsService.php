<?php

declare(strict_types = 1);

namespace App\Services;

use App\Channels\CustomSmsChannel;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\SendSingleCustomSms;
use Illuminate\Support\Facades\Notification;

class CustomSmsService
{
     public function __construct(
        private readonly Patient $patient, 
        private readonly User $user
        )
    {
    }

    public function sendToHmsPatients(string $patientCategory, string $startDate, string $endDate, string $smsDetails)
    {
        $query = $this->patient->query();

        if ($patientCategory == 'registered'){
            $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        }

        if ($patientCategory == 'visited'){
            $query->whereHas('visits', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
                }
            );
        }

        $phoneString = $query->pluck('phone')->filter()->implode(',');

        if (empty($phoneString)) {
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);;
        }

        $this->sendCustomSms(
            'multiplePx',
            $phoneString,
            $smsDetails
        );  
    }

    public function sendToHmsStaff(string $designation, string $smsDetails)
    {
        $query = $this->user->where('date_of_exit', null);

        if ($designation !== 'All'){
            $query->whereRelation('designation', 'designation', '=', $designation);
        }

        $phoneString = $query->pluck('phone_number')->filter()->implode(',');

        if (empty($phoneString)) {
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);;
        }

        $this->sendCustomSms(
            'multipleStaff',
            $phoneString,
            $smsDetails
        );
    }

    public function sendCustomSms(string $recipient, string|array $phone, string $smsDetails)
    {
        Notification::route(CustomSmsChannel::class, $phone)->notify(new SendSingleCustomSms(
            $recipient,
            $phone,
            $smsDetails,
            request()->user()->username
        ));

        return response()->json(['message' => 'SMS queued successfully'], 200);
    }
}