<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Http\Requests\StoreReminderRequestCash;
use App\Http\Requests\StoreReminderRequestHmo;
use App\Services\DatatablesService;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ReminderService $reminderService)
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

    public function destroy(Reminder $reminder)
    {
        return $reminder->destroy($reminder->id);
    }
}
