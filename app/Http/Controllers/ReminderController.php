<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Models\Reminder;
use App\Http\Requests\StoreReminderRequestCash;
use App\Http\Requests\StoreReminderRequestHmo;
use App\Http\Resources\SmsDetailsResource;
use App\Notifications\OutstandingNotifier;
use App\Services\DatatablesService;
use App\Services\HelperService;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ReminderService $reminderService,
        private readonly HelperService $helperService,
        private readonly OutstandingNotifier $outstandingNotifier
        )
    {
        
    }

    public function storeHmo(StoreReminderRequestHmo $request)
    {
        return $this->reminderService->create($request, $request->user());
    }

    public function storeCash(StoreReminderRequestCash $request)
    {
        return $this->reminderService->create($request, $request->user());
    }

    public function loadHmoRemindersTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->reminderService->getAllReminders($params, $request, 'HMO');
       
        $loadTransformer = $this->reminderService->getLoadTransformer('HMO');

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadDueHmoRemindersTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->reminderService->getDueReminders($params, $request, 'HMO');
       
        $loadTransformer = $this->reminderService->getDueReimndersTransformer('HMO');

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadCashRemindersTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->reminderService->getAllReminders($params, $request, 'Cash');
       
        $loadTransformer = $this->reminderService->getLoadTransformer('Cash');

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadDueCashRemindersTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->reminderService->getDueReminders($params, $request, 'Cash');
       
        $loadTransformer = $this->reminderService->getDueReimndersTransformer('Cash');

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function updateFirstReminder(Request $request, Reminder $reminder)
    {
        return $this->reminderService->firstReminder($request, $reminder, $request->user());
    }

    public function updateSecondReminder(Request $request, Reminder $reminder)
    {
        return $this->reminderService->secondReminder($request, $reminder, $request->user());
    }

    public function updateFinalReminder(Request $request, Reminder $reminder)
    {
        return $this->reminderService->finalReminder($request, $reminder, $request->user());
    }

    public function updateConfirmedPayment(Request $request, Reminder $reminder)
    {
        return $this->reminderService->notePayment($request, $reminder, $request->user());
    }

    public function prepareSmsDetails(Request $request, Reminder $reminder)
    {
        return new SmsDetailsResource($reminder);
    }

    public function deleteFirstReminder(Reminder $reminder)
    {
        return $this->reminderService->deleteFirstR($reminder);
    }

    public function deleteSecondReminder(Reminder $reminder)
    {
        return $this->reminderService->deleteSecondR($reminder);
    }

    public function deleteFinalReminder(Reminder $reminder)
    {
        return $this->reminderService->deleteFinalR($reminder);
    }

    public function deletePaid(Reminder $reminder)
    {
        return $this->reminderService->deletePaidR($reminder);
    }

    public function sendSms(SendSmsRequest $request, Reminder $reminder)
    {
        if ($request->selectEl == 'firstReminderSelect'){
            $this->reminderService->firstReminder($request, $reminder, $request->user());
        }
        if ($request->selectEl == 'secondReminderSelect'){
            $this->reminderService->secondReminder($request, $reminder, $request->user());
        }
        if ($request->selectEl == 'finalReminderSelect'){
            $this->reminderService->finalReminder($request, $reminder, $request->user());
        }
        return $this->outstandingNotifier->toSms($reminder, $request->smsDetails, $request->phone);
    }

    public function destroy(Reminder $reminder)
    {
        return $reminder->destroy($reminder->id);
    }
}
