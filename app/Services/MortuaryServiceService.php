<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MortuaryService;
use App\Models\PatientPreForm;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MortuaryServiceService
{
    public function __construct(
        private readonly MortuaryService $mortuaryService, 
        )
    {
    }

    public function create(Request $data, User $user): MortuaryService
    {
        return DB::transaction(function () use($data, $user){
            $mortuaryService = $user->mortuaryServices()->create([
                "date_deposited"            => $data->dateOfDeposit,
                "deceased_name"             => $data->deceasedName,
                "deceased_sex"              => $data->sex,
                "depositors_name"           => $data->depositor,
                "depositors_phone"          => $data->depositorPhone,
                "depositors_address"        => $data->depositorAddress,
                "depositors_relationship"   => $data->depositorRship,
                "alt_collectors_name"       => $data->altCollectorName,
                "alt_collectors_address"    => $data->altCollectorAddress,
                "alt_collectors_phone"      => $data->altCollectorPhone,
                "alt_collectors_relationship"    => $data->altCollectorRship,
                "pickup_date"               => $data->pickUpDate,
            ]);

            return $mortuaryService;
        });
    }

    public function update(Request $data, MortuaryService $mortuaryService, User $user): MortuaryService
    {    
        $mortuaryService->update([
            "date_deposited"            => $data->dateOfDeposit,
            "deceased_name"             => $data->deceasedName,
            "deceased_sex"              => $data->sex,
            "depositors_name"           => $data->depositor,
            "depositors_phone"          => $data->depositorPhone,
            "depositors_address"        => $data->depositorAddress,
            "depositors_relationship"   => $data->depositorRship,
            "alt_collectors_name"       => $data->altCollectorName,
            "alt_collectors_address"    => $data->altCollectorAddress,
            "alt_collectors_phone"      => $data->altCollectorPhone,
            "alt_collectors_relationship"    => $data->altCollectorRship,
            "pickup_date"               => $data->pickUpDate,
        ]);

        return $mortuaryService;
    }

    public function getPaginatedMortuaryServices(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->mortuaryService->select('id', 'user_id', 'deceased_name', 'date_deposited', 'depositors_name', 'depositors_phone', 'depositors_address', 'depositors_relationship', 'date_collected', 'date_deposited', 'created_at', 'deceased_sex')
                        ->with([
                            'user:id,username',
                            'prescriptions' => function ($query){
                                $query->select('id', 'mortuary_service_id', 'resource_id', 'user_id', 'result', 'qty_billed', 'result_date', 'hms_bill', 'result_by', 'created_at')
                                ->with([
                                    'resource:id,name',
                                    'user:id,username',
                                    'resultBy:id,username'
                                ]);
                            },
                            'payments' => function($query){
                                $query->select('id', 'mortuary_service_id', 'user_id', 'pay_method_id', 'amount_paid', 'comment', 'created_at')
                                ->with([
                                    'user:id,username',
                                    'payMethod:id,name'
                                ]);
                            }
                        ])
                        ->withExists([
                            'prescriptions as presCount',
                            'payments as payCount'
                        ])
                        ->withSum(['prescriptions as hmsBill'], 'hms_bill')
                        ->withSum(['prescriptions as sumPaid'], 'paid');
;

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->where('deceased_name', 'LIKE', $searchTerm)
                            ->orWhere('depositors_name', 'LIKE', $searchTerm)
                            ->orWhere('alt_collectors_name', 'LIKE', $searchTerm)
                            ->orWhere('depositors_phone', 'LIKE', $searchTerm)
                            ->orWhere('alt_collectors_phone', 'LIKE', $searchTerm)
                            ->orWhere('date_of_birth', 'LIKE', $searchTerm)
                            ->orWhereRelation('user', 'name', 'LIKE', $searchTerm );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (MortuaryService $mortuaryService) {
            return [
                'id'                => $mortuaryService->id,
                'deceasedName'      => $mortuaryService->deceased_name,
                'dateOfDeposit'     => $mortuaryService->date_deposited ? (new Carbon($mortuaryService->date_deposited))->format('d/m/y g:ia'): "", 
                'depositor'         => $mortuaryService->depositors_name,
                'sex'               => $mortuaryService->deceased_sex,
                'depositorPhone'    => $mortuaryService->depositors_phone,
                'depositorAddress'  => $mortuaryService->depositors_address,
                'depositorRship'    => $mortuaryService->depositors_relationship,
                'daysSpent'         => (new Carbon($mortuaryService->date_collected))->diffInDays($mortuaryService->date_deposited ?? $mortuaryService->created_at),
                'dateCollected'     => $mortuaryService->date_collected ? (new Carbon($mortuaryService->date_collected))->format('d/m/y g:ia') : '',
                'dateCollectedRaw'  => $mortuaryService->date_collected,
                'dateCollectedBy'   => $mortuaryService?->dateCollectedBy?->username,
                'createdAt'         => (new Carbon($mortuaryService->created_at))->format('d/m/Y'),
                'createdBy'         => $mortuaryService->user->username,
                'presCount'         => $mortuaryService->prescriptions->count(),
                'payCount'          => $mortuaryService->payments->count(),
                'billSum'           => $mortuaryService->hmsBill,//prescriptions->sum('hms_bill'),
                'paidSum'           => $mortuaryService->sumPaid,//prescriptions->sum('paid'),
                'prescriptions'     => $mortuaryService->prescriptions->map(fn(Prescription $prescription) => [
                    'id'                => $prescription->id,
                    'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'requestedBy'       => $prescription->user->username,
                    'request'           => $prescription->resource->name,
                    'result'            => $prescription->result,
                    'quantity'          => $prescription->qty_billed,
                    'resultDate'        => $prescription->result_date ? (new Carbon($prescription->result_date))->format('d/m/y g:ia') : '',
                    'resultBy'          => $prescription?->resultBy->username ?? '',
                    'bill'              => $prescription->hms_bill,
                ]),

                'payments'         => $mortuaryService->payments->map(fn(Payment $payment) => [
                    'id'            => $payment->id,
                    'date'          => (new Carbon($payment->created_at))->format('d/m/y g:ia'),
                    'receivedBy'    => $payment->user->username,
                    'amount'        => $payment->amount_paid,
                    'payMethod'     => $payment->payMethod->name,
                    'comment'       => $payment->comment,
                    'user'          => auth()->user()->designation->access_level > 4
                ]),
                'payableUser'       => auth()->user()->designation->access_level > 3 || auth()->user()->designation->designation === 'Bill Officer'
            ];
         };
    }

    public function fillDateCollected(Request $data, MortuaryService $mortuaryService, User $user)
    {
        return DB::transaction(function () use($data, $mortuaryService, $user) {
            return $mortuaryService->update(['date_collected' => $data->dateCollected, 'date_collected_by' => $user->id]);
        });
    }
}