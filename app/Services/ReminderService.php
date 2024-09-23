<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Reminder;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    public function __construct(private readonly Reminder $reminder)
    {
    }

    public function create(Request $data, User $user): Reminder
    {
        return DB::transaction(function () use($data, $user){
            $sponsor = $data->sponsorId ? Sponsor::find($data->sponsorId) : null;
            $visit   = $data->visitId ? Visit::find($data->visitId) : null;
            $patientsMaxDays = $data->payDate ? (new Carbon($data->dateSet))->diffInDays($data->payDate) : null;

            $reminder = $user->reminders()->create([
                'month_sent_for'    => $data->monthSentFor ? new Carbon($data->monthSentFor) : null,
                'set_from'          => $data->dateSent ?? $data->dateSet,
                'max_days'          => $sponsor ? $sponsor->max_pay_days ?? 45 : $patientsMaxDays,
                'comment'           => $data->comment,
                'sponsor_category'  => $sponsor ? $sponsor->category_name : ($visit ? $visit->sponsor->category_name : null),
                'sponsor_id'        => $data->sponsorId ?? null,
                'visit_id'          => $data->visitId ?? null
            ]);
            
            return $reminder;
        });
    }

    public function getAllReminders(DataTableQueryParams $params, $data, $dept)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $nullClause = $dept == 'HMO' ? 'whereNull' : 'whereNotNull';
        $dateColumn = $dept == 'HMO' ? 'month_sent_for' : 'set_from';

        if (! empty($params->searchTerm)) {
            if($data->startDate && $data->endDate){
                if($dept == 'HMO'){
                    return $this->reminder
                        ->$nullClause('visit_id')
                        ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereBetween($dateColumn, [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
                return $this->reminder
                        ->$nullClause('visit_id')
                        ->where(function(Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%');
                        })
                        ->whereBetween($dateColumn, [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                if($dept == 'HMO'){
                    return $this->reminder
                            ->$nullClause('visit_id')
                            ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->whereMonth($dateColumn, $date->month)
                            ->whereYear($dateColumn, $date->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
                return $this->reminder
                        ->$nullClause('visit_id')
                        ->where(function(Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%');
                        })
                        ->whereMonth($dateColumn, $date->month)
                        ->whereYear($dateColumn, $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($dept == 'HMO'){
                return $this->reminder
                        ->$nullClause('visit_id')
                        ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->reminder
                        ->$nullClause('visit_id')
                        ->where(function(Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%');
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->startDate && $data->endDate){
            return $this->reminder
                    ->$nullClause('visit_id')
                    ->whereBetween($dateColumn, [$data->startDate, $data->endDate])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return $this->reminder
                    ->$nullClause('visit_id')
                    ->whereMonth($dateColumn, $date->month)
                    ->whereYear($dateColumn, $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->reminder
                    ->$nullClause('visit_id')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer($dept): callable
    {
        return  function (Reminder $reminder) use ($dept) {
            $date = new Carbon();
            $parameter = $dept == 'HMO' ? 'sponsor' : 'patient';
            $relation = $dept == 'HMO' ? 'sponsor' : 'visit';
            $column   = $dept == 'HMO' ? 'name' : 'patient';
            $additional = 'patientId';

            return [
                    'id'                => $reminder->id,
                    $parameter          =>  $dept == 'HMO' ? $reminder->$relation?->$column : $reminder->$relation?->$column->$additional(),
                    'monthSentFor'      => $reminder->month_sent_for ? (new Carbon($reminder->month_sent_for))->format('M Y') : null,
                    'setFrom'           => (new Carbon($reminder->set_from))->format('d/m/y g:ia'),
                    'daysAgo'           => $date->diffInDays($reminder->set_from),
                    'maxDays'           => $reminder->max_days,
                    'daysToPay'         => $reminder->confirmed_paid ? (new Carbon($reminder->set_from))->diffInDays($reminder->confirmed_paid) : '',
                    'firstReminder'     => $reminder->first_reminder ? $reminder->first_reminder . ' - ' . $reminder->firstReminderBy?->username : '',
                    'firstDate'         => $reminder->first_reminder_date ? (new Carbon($reminder->first_reminder_date))->format('d/m/Y g:ia') : '',
                    'secondReminder'    => $reminder->second_reminder ? $reminder->second_reminder . ' - ' . $reminder->secondReminderBy?->username : '',
                    'secondDate'        => $reminder->second_reminder_date ? (new Carbon($reminder->second_reminder_date))->format('d/m/Y g:ia') : '',
                    'finalReminder'     => $reminder->final_reminder ? $reminder->final_reminder . ' - ' . $reminder->finalReminderBy?->username : '',
                    'finalDate'         => $reminder->final_reminder_date ? (new Carbon($reminder->final_reminder_date))->format('d/m/Y g:ia') : '',
                    'remind'            => $reminder->remind ? 'Yes' : 'No',
                    'paid'              => $reminder->confirmed_paid ? (new Carbon($reminder->confirmed_paid))->format('d/m/y') . ' - ' . $reminder->confirmedPaidBy?->username : 'Pending',
                    'comment'           => $reminder->comment,
                    'createdAt'         => (new Carbon($reminder->created_at))->format('d/m/Y g:ia'),
                    'setBy'             => $reminder->user->username,
                ];
            };
    }

    public function getDueReminders(DataTableQueryParams $params, $data, $dept)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $nullClause = $dept == 'HMO' ? 'whereNull' : 'whereNotNull';

        if (! empty($params->searchTerm)) {
            if($dept == 'HMO'){
                return $this->reminder
                        ->$nullClause('visit_id')
                        ->whereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->reminder
                        ->$nullClause('visit_id')
                        ->where(function(Builder $query) use($params) {
                            $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%');
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->reminder
                    ->$nullClause('visit_id')
                    ->where('remind', true)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getDueReimndersTransformer($dept): callable
    {
       return  function (Reminder $reminder) use ($dept) {
        $date = new Carbon();
        $parameter = $dept == 'HMO' ? 'sponsor' : 'patient';
        $relation  = $dept == 'HMO' ? 'sponsor' : 'visit';
        $column    = $dept == 'HMO' ? 'name' : 'patient';
        $additional = 'patientId';
            return [
                'id'                => $reminder->id,
                $parameter          => $dept == 'HMO' ? $reminder->$relation?->$column : $reminder->$relation?->$column->$additional(),
                'monthSentFor'      => $reminder->month_sent_for ? (new Carbon($reminder->month_sent_for))->format('M Y') : null,
                'phone'             => $reminder->visit?->patient->phone,
                'daysAgo'           => $date->diffInDays($reminder->set_from),
                'maxDays'           => $reminder->max_days,
                'firstReminder'     => $reminder->first_reminder ? $reminder->first_reminder . ' - ' . $reminder->firstReminderBy?->username : null,
                'secondReminder'    => $reminder->second_reminder ? $reminder->second_reminder . ' - ' . $reminder->secondReminderBy?->username : null,
                'finalReminder'     => $reminder->final_reminder ? $reminder->final_reminder . ' - ' . $reminder->finalReminderBy?->username : null,
                'paid'              => $reminder->confirmed_paid ? (new Carbon($reminder->confirmed_paid))->format('d/m/y') . ' - ' . $reminder->confirmedPaidBy?->username : 'Pending',
                'comment'           => $reminder->comment,
                'createdAt'         => (new Carbon($reminder->created_at))->format('d/m/Y g:ia'),
                'setBy'             => $reminder->user->username,
            ];
         };
    }

    public function firstReminder(Request $data, Reminder $reminder, User $user)
    {
        return $reminder->update([
            'first_reminder'        => $data->reminder ? $data->reminder : null,
            'first_reminder_date'   => $data->reminder && $data->reminder !== 'Deferred' ? new Carbon() : null,
            'first_reminder_by'     => $data->reminder ? $user->id : null,
            'remind'                => $data->reminder ? false : $reminder->remind
        ]);
    }

    public function secondReminder(Request $data, Reminder $reminder, User $user)
    {
        return $reminder->update([
            'second_reminder'       => $data->reminder ? $data->reminder : null,
            'second_reminder_date'  => $data->reminder && $data->reminder !== 'Deferred' ? new Carbon() : null,
            'second_reminder_by'    => $data->reminder ? $user->id : null,
            'remind'                => $data->reminder ? false : $reminder->remind
        ]);
    }

    public function finalReminder(Request $data, Reminder $reminder, User $user)
    {
        return $reminder->update([
            'final_reminder'        => $data->reminder ? $data->reminder : null,
            'final_reminder_date'   => $data->reminder && $data->reminder !== 'Deferred' ? new Carbon() : null,
            'final_reminder_by'     => $data->reminder ? $user->id : null,
            'remind'                => $data->reminder ? false : $reminder->remind
        ]);
    }

    public function deleteFirstR(Reminder $reminder)
    {
        return $reminder->update(['first_reminder' => null]);
    }

    public function deleteSecondR(Reminder $reminder)
    {
        return $reminder->update(['second_reminder' => null]);
    }

    public function deleteFinalR(Reminder $reminder)
    {
        return $reminder->update(['final_reminder' => null]);
    }

    public function deletePaidR(Reminder $reminder)
    {
        return $reminder->update(['confirmed_paid' => null]);
    }

    public function notePayment(Request $data, Reminder $reminder, User $user)
    {
        return DB::transaction(function () use($data, $reminder, $user){
            if ($reminder->visit_id){
                $data->confirmedPaidDate ? $reminder->visit->patient->update(['flag' => false]) : '';
            }else{
                $data->confirmedPaidDate ? $reminder->sponsor->update(['flag' => false]) : '';
            }
    
            return $reminder->update([
                'confirmed_paid'        => $data->confirmedPaidDate ? new Carbon($data->confirmedPaidDate) : null,
                'confirmed_paid_by'     => $data->confirmedPaidDate ? $user->id : null,
                'remind'                => $data->confirmedPaidDate ? false : $reminder->remind
            ]);
        });
    }

}