<?php

namespace App\Http\Controllers;

use App\Channels\CustomSmsChannel;
use App\Notifications\SendSingleCustomSms;
use App\Services\CustomSmsService;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CustomSmsController extends Controller
{

    public function __construct(
        private readonly HelperService $helperService,
        private readonly CustomSmsService $customSmsService
        )
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function sendSinglePatientSms(Request $request)
    {
        if (!$this->helperService->shouldNotify($request->phone, null, [])) {
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);
        }

        $this->customSmsService->sendCustomSms(
            $request->firstname,
            $request->phone,
            $request->smsDetails,
        );

        return response()->json(['message' => 'SMS queued successfully'], 200);
    }

    public function sendSingleStaffSms(Request $request)
    {
        if (!$this->helperService->shouldNotify($request->phone, null, [])) {
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);
        }

        $this->customSmsService->sendCustomSms(
            $request->username,
            $request->phone,
            $request->smsDetails,
        );

        return response()->json(['message' => 'SMS queued successfully'], 200);
    }

    public function sendNumberSms(Request $request)
    {
        if (!$this->helperService->shouldNotify($request->phone ?? $request->phones, null, [])) {
            info($request->phone ?? $request->phones);
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);
        }

        $this->customSmsService->sendCustomSms(
            'N/A',
            $request->phone ?? $request->phones,
            $request->smsDetails,
        );

        return response()->json(['message' => 'SMS queued successfully'], 200);
    }

    public function sendMultiPatientsSms(Request $request)
    {
        if (!$this->helperService->shouldNotify('00000000000', null, [])) {
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);
        }

        return $this->customSmsService->sendToHmsPatients(
                $request->patientCategory, 
                $request->startDate, 
                $request->endDate, 
                $request->smsDetails
            );  
    }

    public function sendMultiStaffSms(Request $request)
    {
        if (!$this->helperService->shouldNotify('00000000000', null, [])) {
            return response()->json(['message' => 'SMS cannot be sent at this time'], 403);
        }

        return $this->customSmsService->sendToHmsStaff(
                $request->designation,  
                $request->smsDetails
            );  
    }
}
