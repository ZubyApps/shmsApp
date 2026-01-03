<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Models\WalkIn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalkInService
{
    public function __construct(
        private readonly WalkIn $walkIn, 
        private readonly HelperService $helperService, 
        private readonly Prescription $prescription
        )
    {
    }

    public function create(Request $data, User $user): WalkIn
    {
        return DB::transaction(function () use($data, $user){
            $walkIn = $user->walkIns()->create([
                "first_name"            => $data->firstName,
                "middle_name"           => $data->middleName,
                "last_name"             => $data->lastName,
                "date_of_birth"         => $data->dateOfBirth,
                "phone"                 => $data->phone,
                "sex"                   => $data->sex,
                "address"               => $data->address,
                "occupation"            => $data->occupation,
                "prev_xray"             => $data->prevXray,
                "date_of_xray"          => $data->dateOfXray,
                "clinical_diagnosis"    => $data->clinicalDiagnosis,
                "clinical_features"     => $data->clinicalFeatures,
            ]);

            return $walkIn;
        });
    }

    public function update(Request $data, WalkIn $walkIn, User $user): WalkIn
    {    
        $walkIn->update([
            "first_name"            => $data->firstName,
            "middle_name"           => $data->middleName,
            "last_name"             => $data->lastName,
            "date_of_birth"         => $data->dateOfBirth,
            "phone"                 => $data->phone,
            "address"               => $data->address,
            "occupation"            => $data->occupation,
            "sex"                   => $data->sex,
            "prev_xray"             => $data->prevXray,
            "date_of_xray"          => $data->dateOfXray,
            "clinical_diagnosis"    => $data->clinicalDiagnosis,
            "clinical_features"     => $data->clinicalFeatures,
        ]);

        return $walkIn;
    }

    public function getPaginatedWalkIns(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->walkIn->select('id', 'user_id', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'sex', 'phone', 'created_at')
                    ->with([
                        'user:id,username', 
                        'prescriptions' => function ($query) {
                                $query->select('id', 'walk_in_id', 'user_id', 'resource_id', 'created_at', 'result', 'result_date', 'hms_bill', 'result_by')
                                ->with(['resource:id,name', 'user:id,username', 'resultBy:id,username']);
                            },
                        'payments' => function ($query) {
                                $query->select('id', 'walk_in_id', 'user_id', 'pay_method_id', 'amount_paid', 'comment', 'created_at')
                                ->with(['payMethod:id,name', 'user:id,username']);
                            },
                    ])
                    ->withExists([
                        'prescriptions as hasPrescriptions',
                        'payments as hasPayments',
                        'prescriptions as hasLinkedPrescriptions' => function ($query) {
                                $query->whereNotNull('visit_id');
                            },
                    ])
                    ->withSum('prescriptions as billSum', 'hms_bill')
                    ->withSum('prescriptions as paidSum', 'paid');

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->whereRaw('CONCAT_WS(" ", first_name, middle_name, last_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", last_name, middle_name, first_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", first_name, last_name, middle_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", last_name, first_name, middle_name) LIKE ?', [$searchTerm])
                            ->orWhere('phone', 'LIKE', $searchTerm)
                            ->orWhere('sex', 'LIKE', $searchTerm)
                            ->orWhere('date_of_birth', 'LIKE', $searchTerm)
                            ->orWhereRelation('user', 'name', 'LIKE', $searchTerm );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(User $logginUser): callable
    {
       return  function (WalkIn $walkIn) use($logginUser) {
            return [
                'id'                => $walkIn->id,
                'name'              => $walkIn->fullName(true),
                'age'               => $walkIn->date_of_birth ? $this->helperService->twoPartDiffInTimePast($walkIn->date_of_birth): "",
                'sex'               => $walkIn->sex,
                'phone'             => $walkIn->phone,
                'createdAt'         => (new Carbon($walkIn->created_at))->format('d/m/Y'),
                'createdBy'         => $walkIn->user->username,
                'presCount'         => $walkIn->hasPrescriptions,
                'payCount'          => $walkIn->hasPayments,
                'billSum'           => $walkIn->billSum,
                'paidSum'           => $walkIn->paidSum,
                'isLinked'          => $walkIn->hasLinkedPrescriptions,

                'prescriptions'     => $walkIn->prescriptions->map(fn(Prescription $prescription) => [
                    'id'                => $prescription->id,
                    'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'requestedBy'       => $prescription->user->username,
                    'request'           => $prescription->resource->name,
                    'result'            => $prescription->result,
                    'resultDate'        => $prescription->result_date ? (new Carbon($prescription->result_date))->format('d/m/y g:ia') : '',
                    'resultBy'          => $prescription?->resultBy->username ?? '',
                    'bill'              => $prescription->hms_bill,
                ]),

                'payments'         => $walkIn->payments->map(fn(Payment $payment) => [
                    'id'            => $payment->id,
                    'date'          => (new Carbon($payment->created_at))->format('d/m/y g:ia'),
                    'receivedBy'    => $payment->user->username,
                    'amount'        => $payment->amount_paid,
                    'payMethod'     => $payment->payMethod->name,
                    'comment'       => $payment->comment,
                    'user'          => $logginUser->designation->access_level > 4
                ]),
                'payableUser'       => $logginUser->designation->access_level > 4 || $logginUser->designation->designation === 'Bill Officer',
                'staff'             => $logginUser->username
            ];
         };
    }

    // public function handleVisitLink($walkIn, $visit)
    // {
    //     return DB::transaction(function () use ($walkIn, $visit) {
    //         $consultation = $visit->latestConsultation()->first();
    
    //         $updateData = ['visit_id' => $visit->id];
    
    //         if ($consultation) {
    //             $updateData['consultation_id'] = $consultation->id;
    //         }
    
    //         $walkIn->prescriptions()
    //             ->whereNull('visit_id')
    //             ->update($updateData);
    
    //         $walkIn->payments()
    //             ->whereNull('visit_id')
    //             ->update(['visit_id' => $visit->id, 'patient_id' => $visit->patient->id]);
    
    //         if ($visit){
    //             $sums = $visit->prescriptions()
    //                     ->selectRaw('COALESCE(SUM(paid), 0) as totalPaid, COALESCE(SUM(hms_bill), 0) as totalHmsBill, COALESCE(SUM(nhis_bill), 0) as totalNhisBill')
    //                     ->first();
    
    //             $visit->update(['total_paid' => $sums->totalPaid,  'total_hms_bill' => $sums->totalHmsBill, 'total_nhis_bill' => $sums->totalNhisBill]);
    //         }
    
    //         return true;
    //     });
    // }

    // public function handleUnlinkVisit($walkIn)
    // {
    //     return DB::transaction(function () use ($walkIn) {
    
    //         $visitId = $walkIn->prescriptions()->whereNotNull('visit_id')->value('visit_id');

    //         $visit = $visitId ? Visit::find($visitId) : null;

    //         $walkIn->prescriptions()->update(['visit_id' => null, 'consultation_id' => null]);
    
    //         $walkIn->payments()->update(['visit_id' => null, 'patient_id' => null]);
    
    //         if ($visit){
    //             $sums = $visit->prescriptions()
    //                     ->selectRaw('COALESCE(SUM(paid), 0) as totalPaid, COALESCE(SUM(hms_bill), 0) as totalHmsBill, COALESCE(SUM(nhis_bill), 0) as totalNhisBill')
    //                     ->first();
    
    //             $visit->update(['total_paid' => $sums->totalPaid,  'total_hms_bill' => $sums->totalHmsBill, 'total_nhis_bill' => $sums->totalNhisBill]);
    //         }

    
    //         return true;
    //     });
    // }

        public function handleVisitLink($walkIn, $visit)
    {
        return DB::transaction(function () use ($walkIn, $visit) {
            // Use relationship directly to find the latest consultation ID
            $consultationId = $visit->latestConsultation()->value('id');
            
            $updateData = ['visit_id' => $visit->id];

            // Restoring your conditional logic
            if ($consultationId) {
                $updateData['consultation_id'] = $consultationId;
            }

            $walkIn->prescriptions()
                ->whereNull('visit_id')
                ->update($updateData);

            $walkIn->payments()
                ->whereNull('visit_id')
                ->update([
                    'visit_id' => $visit->id, 
                    // Optimization: Use patient_id from the visit object to avoid extra query
                    'patient_id' => $visit->patient_id 
                ]);

            // Use the new model method
            $visit->refreshTotals();

            return true;
        });
    }

    public function handleUnlinkVisit($walkIn)
    {
        return DB::transaction(function () use ($walkIn) {
            // Fetch the visit ID before we null it out
            $visitId = $walkIn->prescriptions()->whereNotNull('visit_id')->value('visit_id');

            $walkIn->prescriptions()->update(['visit_id' => null, 'consultation_id' => null]);
            $walkIn->payments()->update(['visit_id' => null, 'patient_id' => null]);

            if ($visitId) {
                $visit = Visit::find($visitId);
                $visit?->refreshTotals();
            }

            return true;
        });
    }
    
    public function getAllWalkInTests(WalkIn $walkIn)
    {   
            return $this->prescription
                        ->where('walk_in_id', $walkIn->id)
                        ->whereRelation('resource', 'category', 'Investigations')
                        ->where('result_date', '!=', null)
                        ->orderBy('created_at', 'asc')
                        ->get();
    }

    public function getWalkinBillSummaryTable($data)
    {
        return DB::table('prescriptions')
                        ->selectRaw('SUM(prescriptions.hms_bill) as totalBill, SUM(prescriptions.paid) as totalPaid, resources.'.$data->type.' as service, COUNT(resources.category) as types, SUM(prescriptions.qty_billed) as quantity')
                        ->leftJoin('resources', 'prescriptions.resource_id', '=', 'resources.id')
                        ->where('walk_in_id', $data->walkInId)
                        ->groupBy('service')
                        ->orderBy('service')
                        ->get()
                        ->toArray();   
    }
}