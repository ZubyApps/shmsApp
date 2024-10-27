<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Resource;
use App\Models\ResourceSubCategory;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitService
{
    public function __construct(private readonly Visit $visit, private readonly PaymentService $paymentService, private readonly Ward $ward)
    {
    }

    public function create(Request $data, Patient $patient, User $user): Visit
    {
        return DB::transaction(function () use ($data, $patient, $user) {

            $sponsorArray = ['Individual', 'Family'];

            $patient->update([
                "is_active" => true
            ]); 
    
            $visit = $user->visits()->create([
                    "patient_id" => $patient->id,
                    "sponsor_id" => $patient->sponsor->id,
                    "waiting_for"=> $data->doctor
            ]);
    
            if (in_array($patient->sponsor->category_name, $sponsorArray)){

                $subcat = ResourceSubCategory::firstOrCreate(['name' => 'Hospital Card'], [
                    'name' => 'Hospital Card',
                    'description' => 'Card opening for new patients. Individual, Family etc.',
                    'user_id'   => 1,
                    'resource_category_id' => 6
                ]);
    
                if ($patient->visits->count() < 2 && $patient->sponsor->category_name == 'Individual'){
                    
                    if ($patient->patient_type == 'ANC'){
                        $resource = Resource::firstOrCreate(['name' => 'Antenatal Card'],[
                            'name'              => 'Antenatal Card',
                            'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                            'reorder_level'     => 0,
                            'purchase_price'    => 100,
                            'selling_price'     => 1000,
                            'unit_description'  => 'Service(s)',
                            'category'          => 'Other Services',
                            'sub_category'      => 'Hospital Card',
                            'stock_level'       => 500,
                            'resource_sub_category_id' => $subcat->id,
                            'user_id'           => 1,
                        ]);

                        $prescription = $user->prescriptions()->create([
                            'resource_id'       => $resource->id,
                            'prescription'      => null,
                            'consultation_id'   => null,
                            'visit_id'          => $visit->id,
                            'qty_billed'        => 1,
                            'qty_dispensed'     => 1,
                            'hms_bill'          => $resource->selling_price,
                            'hms_bill_date'     => new Carbon(),
                            'hms_bill_by'       => $user->id,
                        ]);
    
                        $prescription->visit->update([
                            'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                        ]);
                        
                    } else if ($patient->patient_type == 'Regular.New') {
                        $resource = Resource::firstOrCreate(['name' => 'Individual Card'],[
                            'name'              => 'Individual Card',
                            'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                            'reorder_level'     => 0,
                            'purchase_price'    => 200,
                            'selling_price'     => 2000,
                            'unit_description'  => 'Service(s)',
                            'category'          => 'Other Services',
                            'sub_category'      => 'Hospital Card',
                            'stock_level'       => 1000,
                            'resource_sub_category_id' => $subcat->id,
                            'user_id'           => 1,
                        ]);

                        $prescription = $user->prescriptions()->create([
                            'resource_id'       => $resource->id,
                            'prescription'      => null,
                            'consultation_id'   => null,
                            'visit_id'          => $visit->id,
                            'qty_billed'        => 1,
                            'qty_dispensed'     => 1,
                            'hms_bill'          => $resource->selling_price,
                            'hms_bill_date'     => new Carbon(),
                            'hms_bill_by'       => $user->id,
                        ]);
    
                        $prescription->visit->update([
                            'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                        ]);
                    }
                }
    
                if ($patient->sponsor->category_name == 'Family' && $patient->sponsor->visits->count() < 2 && $patient->visits->count() < 2 && $patient->patient_type == 'Regular.New'){
    
                    $resource = Resource::firstOrCreate(['name' => 'Family Card'], [
                        'name'              => 'Family Card',
                        'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                        'reorder_level'     => 0,
                        'purchase_price'    => 350,
                        'selling_price'     => 3500,
                        'unit_description'  => 'Service(s)',
                        'category'          => 'Other Services',
                        'sub_category'      => 'Hospital Card',
                        'stock_level'       => 1000,
                        'resource_sub_category_id' => $subcat->id,
                        'user_id'           => 1,
                    ]);
    
                    $prescription = $user->prescriptions()->create([
                        'resource_id'       => $resource->id,
                        'prescription'      => null,
                        'consultation_id'   => null,
                        'visit_id'          => $visit->id,
                        'qty_billed'        => 1,
                        'qty_dispensed'     => 1,
                        'hms_bill'          => $resource->selling_price,
                        'hms_bill_date'     => new Carbon(),
                        'hms_bill_by'       => $user->id,
                    ]);

                    $prescription->visit->update([
                        'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                    ]);
                }

                if ($patient->sponsor->category_name == 'Family' && $patient->sponsor->visits->count() < 2 && $patient->visits->count() > 1){
                    $resource = Resource::firstOrCreate(['name' => 'Family Card Upgrade'], [
                        'name'              => 'Family Card Upgrade',
                        'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                        'reorder_level'     => 0,
                        'purchase_price'    => 150,
                        'selling_price'     => 1500,
                        'unit_description'  => 'Service(s)',
                        'category'          => 'Other Services',
                        'sub_category'      => 'Hospital Card',
                        'stock_level'       => 1000,
                        'resource_sub_category_id' => $subcat->id,
                        'user_id'           => 1,
                    ]);
    
                    $prescription = $user->prescriptions()->create([
                        'resource_id'       => $resource->id,
                        'prescription'      => null,
                        'consultation_id'   => null,
                        'visit_id'          => $visit->id,
                        'qty_billed'        => 1,
                        'qty_dispensed'     => 1,
                        'hms_bill'          => $resource->selling_price,
                        'hms_bill_date'     => new Carbon(),
                        'hms_bill_by'       => $user->id,
                    ]);

                    $prescription->visit->update([
                        'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                    ]);
                }
            }
    
            return $visit;
        });
    }

    public function getPaginatedWaitingVisits(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('consulted', null)
                        ->where(function (Builder $query) use($params) {
                            $query->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->visit
                    ->where('consulted', null)
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getWaitingListTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'patientId'         => $visit->patient->id,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'patient'           => $visit->patient->patientId(),
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'came'              => (new Carbon($visit->created_at))->diffForHumans(['parts' => 2, 'short' => true]),
                'waitingFor'        => $visit->waitingFor->username ?? '',
                'doctor'            => $visit->doctor->username ?? '',
                'patientType'       => $visit->patient->patient_type,
                'status'            => $visit->status,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'emergency'         => $visit->prescriptions()->where('consultation_id', null)->whereRelation('resource', 'sub_category', '!=', 'Hospital Card')->count(),
                'closed'            => $visit->closed,
                'initiatedBy'       => $visit->user->username,
                'payments'          => $visit->payments->count(),
                'prescriptions'     => $visit->prescriptions->count()
            ];
         };
    }

    public function changeVisitSponsor(Request $data, Visit $visit, User $user)
    {
        return DB::transaction(function () use ($data, $visit, $user) {
                    $visit->update([
                        "sponsor_id" => $data->sponsor,
                        "sponsor_changed_by" => $user->id,
                    ]);

                    $prescriptions  = $visit->prescriptions;
                    $totalPayments  = $visit->payments;
            
                    if ($visit->sponsor->category_name == 'NHIS'){
                        foreach($prescriptions as $prescription){
                            $prescription->update(['nhis_bill' => $prescription->hms_bill/10]);
                        }
                        $this->paymentService->prescriptionsPaymentSeiveNhis($totalPayments, $prescription->visit->prescriptions());
                    }

                    return $visit;
                });

    }

    public function discharge(Request $data, Visit $visit, User $user)
    {
        $visit->update([
            "discharge_reason"  => $data->reason ? $data->reason : null,
            "discharge_remark"  => $data->reason ? $data->remark : null,
            "doctor_done_by"    => $data->reason ? $user->id : null,
            "doctor_done_at"    => $data->reason ? new Carbon() : null,
        ]);

        if ($data->reason){
            $ward = $this->ward->find($visit->ward);
            $ward?->visit_id == $visit->id ? $ward->update(['visit_id' => null]) : '';
        }

        return $visit;
    }

    public function close(User $user, Visit $visit)
    {
        return DB::transaction(function () use($user, $visit){
            $visit->update([
                'closed'            => true, 
                'closed_opened_at'  => new Carbon(), 
                'closed_opened_by'  => $user->id
            ]);

            $visit->patient()->update(['is_active' => false]);
        });
    }

    public function open(User $user, Visit $visit)
    {
        return DB::transaction(function () use($user, $visit){
            $visit->update([
                'closed'            => false, 
                'closed_opened_at'  => new Carbon(), 
                'closed_opened_by'  => $user->id
            ]);
            $visit->patient()->update(['is_active' => true]);
        });
    }

    public function review(Request $data, Visit $visit)
    {
        return $visit->update([
            'reviewed' => $data->review
        ]);
    }

    public function resolve(Visit $visit)
    {
        return $visit->update([
            'resolved' => !$visit->resolved
        ]);
    }

    public function delete($visit)
    {
        return DB::transaction(function() use($visit){
            $visit->patient()->update(['is_active' => false]);
            $visit->destroy($visit->id);
        });
    }

    public function getVisitSummaryBySponsor(DataTableQueryParams $params, $data)
    {
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {

            if($data->date){
                $date = new Carbon($data->date);

                return DB::table('visits')
                ->selectRaw('sponsors.name as sponsor, sponsors.category_name as category, COUNT(visits.patient_id) as patientsCount, SUM(CASE WHEN admission_status = "Outpatient" THEN 1 ELSE 0 END) AS outpatients, SUM(CASE WHEN admission_status = "Inpatient" THEN 1 ELSE 0 END) AS inpatients, SUM(CASE WHEN admission_status = "Observation" THEN 1 ELSE 0 END) AS observations')
                ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                ->where(function (QueryBuilder $query) use($params) {
                    $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->orWhere('sponsors.category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                })
                ->whereMonth('visits.created_at', $date->month)
                ->whereYear('visits.created_at', $date->year)
                ->groupBy('sponsor', 'category')
                ->orderBy('sponsor')
                ->orderBy('patientsCount')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return DB::table('visits')
            ->selectRaw('sponsors.name as sponsor, sponsors.category_name as category, COUNT(visits.patient_id) as patientsCount, SUM(CASE WHEN admission_status = "Outpatient" THEN 1 ELSE 0 END) AS outpatients, SUM(CASE WHEN admission_status = "Inpatient" THEN 1 ELSE 0 END) AS inpatients, SUM(CASE WHEN admission_status = "Observation" THEN 1 ELSE 0 END) AS observations')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->where(function (QueryBuilder $query) use($params) {
                $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                ->orWhere('sponsors.category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
            })
            ->whereMonth('visits.created_at', $current->month)
            ->whereYear('visits.created_at', $current->year)
            ->groupBy('sponsor', 'category')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('visits')
            ->selectRaw('sponsors.name as sponsor, sponsors.category_name as category, COUNT(visits.patient_id) as patientsCount, SUM(CASE WHEN admission_status = "Outpatient" THEN 1 ELSE 0 END) AS outpatients, SUM(CASE WHEN admission_status = "Inpatient" THEN 1 ELSE 0 END) AS inpatients, SUM(CASE WHEN admission_status = "Observation" THEN 1 ELSE 0 END) AS observations')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->where('consulted', '!=', null)
            ->whereMonth('visits.created_at', $date->month)
            ->whereYear('visits.created_at', $date->year)
            ->groupBy('sponsor', 'category')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return DB::table('visits')
            ->selectRaw('sponsors.name as sponsor, sponsors.category_name as category, COUNT(visits.patient_id) as patientsCount, SUM(CASE WHEN admission_status = "Outpatient" THEN 1 ELSE 0 END) AS outpatients, SUM(CASE WHEN admission_status = "Inpatient" THEN 1 ELSE 0 END) AS inpatients, SUM(CASE WHEN admission_status = "Observation" THEN 1 ELSE 0 END) AS observations')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->where('consulted', '!=', null)
            ->whereMonth('visits.created_at', $current->month)
            ->whereYear('visits.created_at', $current->year)
            ->groupBy('sponsor', 'category')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                if ($data->filterListBy){
                    return $this->visit
                        ->Where('consulted', '!=', null)
                        ->where(function (Builder $query) use($data) {
                            $query->where('admission_status', $data->filterListBy)
                            ->orWhereRelation('patient', 'patient_type', $data->filterListBy);
                        })
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
                return $this->visit
                        ->Where('consulted', '!=', null)
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->visit
                        ->Where('consulted', '!=', null)
                        ->whereDay('created_at', $current->today())
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            if ($data->filterListBy){
                return $this->visit
                    ->Where('consulted', '!=', null)
                    ->where(function (Builder $query) use($data) {
                        $query->where('admission_status', $data->filterListBy)
                        ->orWhereRelation('patient', 'patient_type', $data->filterListBy);
                    })
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->visit
                    ->Where('consulted', '!=', null)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->Where('consulted', '!=', null)
                    ->whereDate('created_at', $current->format('Y-m-d'))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->created_at))->format('d/M/y g:ia'),
                'seen'              => (new Carbon($visit->consulted))->format('d/M/y g:ia'),
                'patientType'       => explode(".", $visit->patient->patient_type)[0],
                'doctor'            => $visit->doctor?->username ?? '',
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'patient'           => $visit->patient->patientId(),
                'phone'             => $visit->patient->phone,
                'address'           => $visit->patient?->address,
                'state'             => $visit->patient->state_of_residence,
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'nok'               => $visit->patient?->next_of_kin,
                'nokPhone'          => $visit->patient?->next_of_kin_phone,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'status'            => $visit->admission_status,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'ancCount'          => explode(".", $visit->patient->patient_type)[0] == 'ANC' ? $visit->consultations->count() : '',
                'closed'            => true
            ];
         };
    }

    public function averageWaitingTime(CarbonImmutable $day, $startDay, $endDay)
    {
        $averageWaitingTime = DB::table('visits')
                    ->selectRaw('AVG(TIME_TO_SEC(consulted) - TIME_TO_SEC(created_at)) AS averageWaitingTime')
                    ->whereBetween('created_at', [$day->$startDay(), $day->$endDay()])
                    ->get()->first()?->averageWaitingTime;

        return $averageWaitingTime ? CarbonInterval::seconds($averageWaitingTime)->cascade()->forHumans() : $averageWaitingTime;
    }
}
