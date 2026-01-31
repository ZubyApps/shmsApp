<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Ward;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Sponsor;
use App\Models\Resource;
use Carbon\CarbonInterval;
use Carbon\CarbonImmutable;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResourceSubCategory;
use App\Services\PayPercentageService;
use App\DataObjects\SponsorCategoryDto;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder as QueryBuilder;

class VisitService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly PaymentService $paymentService, 
        private readonly Ward $ward,
        private readonly PayPercentageService $payPercentageService,
        )
    {
    }

    public function create(Request $data, Patient $patient, User $user)
    {
        return DB::transaction(function () use ($data, $patient, $user) {

            $sponsorArray = ['Individual', 'Family'];
                        
            Validator::make($data->all(), [
                'visitType' => [
                    'required',
                    // Custom rule to check for open ANC visit
                    function ($attribute, $value, $fail) use ($patient) {
                        if ($value === 'ANC' && $patient->visits()
                            ->where('visit_type', 'ANC')
                            ->where('closed', false)
                            ->exists()) {
                            $fail('This patient already has an open ANC visit.');
                        }
                    },
                ],
                'patient' => [
                    function ($attribute, $value, $fail) use ($patient, $data) {
                        if ($data->visitType == 'ANC' && strtolower($patient->sex) === 'male') {
                            $fail('This patient is male and cannot initiate an ANC visit.');
                        }
                    }
                ]
            ])->validate();                
    
            $visit = $user->visits()->create([
                    "patient_id" => $patient->id,
                    "sponsor_id" => $patient->sponsor->id,
                    "waiting_for"=> $data->doctor,
                    "visit_type" => $data->visitType,
            ]);

            $patientSponsor = $patient->sponsor->category_name;
            $visitType      = $visit->visit_type;
            $patientVisitsC = $patient->visits->count();
            $patientSponsorVisitsC = $patient->sponsor->visits->count();

            if ($visitType == 'Regular'){
                $patient->update([
                    "is_active" => true
                ]);
            }
    
            if (in_array($patientSponsor, $sponsorArray)){

                $subcat = ResourceSubCategory::firstOrCreate(['name' => 'Hospital Card'], [
                    'name' => 'Hospital Card',
                    'description' => 'Card opening for new patients. Individual, Family etc.',
                    'user_id'   => 1,
                    'resource_category_id' => 6
                ]);

                if ($patientVisitsC < 2 && $patientSponsor == 'Individual' && $patient->patient_type == 'Regular.New'){
                    
                    // if ($visitType == 'ANC'){
                    //     $resource = Resource::firstOrCreate(['name' => 'Antenatal Card'],[
                    //         'name'              => 'Antenatal Card',
                    //         'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                    //         'reorder_level'     => 0,
                    //         'purchase_price'    => 100,
                    //         'selling_price'     => 1000,
                    //         'unit_description'  => 1,
                    //         'category'          => 'Other Services',
                    //         'sub_category'      => 'Hospital Card',
                    //         'stock_level'       => 500,
                    //         'resource_sub_category_id' => $subcat->id,
                    //         'user_id'           => 1,
                    //     ]);

                    //     $prescription = $user->prescriptions()->create([
                    //         'resource_id'       => $resource->id,
                    //         'prescription'      => null,
                    //         'consultation_id'   => null,
                    //         'visit_id'          => $visit->id,
                    //         'qty_billed'        => 1,
                    //         'qty_dispensed'     => 1,
                    //         'hms_bill'          => $resource->selling_price,
                    //         'hms_bill_date'     => new Carbon(),
                    //         'hms_bill_by'       => $user->id,
                    //     ]);
    
                    //     $prescription->visit->update([
                    //         'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                    //     ]);
                        
                    // } else 
                    // if ($visitType == 'Regular') {
                        $resource = Resource::firstOrCreate(['name' => 'Individual Card'],[
                            'name'              => 'Individual Card',
                            'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                            'reorder_level'     => 0,
                            'purchase_price'    => 4000,
                            'selling_price'     => 5000,
                            'unit_description'  => 1,
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
                    // }
                }
    
                if ($patientSponsor == 'Family' && $patientSponsorVisitsC < 2 && $patientVisitsC < 2 && $patient->patient_type == 'Regular.New'){
    
                    $resource = Resource::firstOrCreate(['name' => 'Family Card'], [
                        'name'              => 'Family Card',
                        'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                        'reorder_level'     => 0,
                        'purchase_price'    => 30000,
                        'selling_price'     => 35000,
                        'unit_description'  => 1,
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

                if ($patientSponsor == 'Family' && $patientSponsorVisitsC < 2 && $patientVisitsC > 1){
                    $resource = Resource::firstOrCreate(['name' => 'Family Card Upgrade'], [
                        'name'              => 'Family Card Upgrade',
                        'flag'              => 'Family,HMO,NHIS,Individual,Retainership',
                        'reorder_level'     => 0,
                        'purchase_price'    => 150,
                        'selling_price'     => 2000,
                        'unit_description'  => 1,
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
                        // 'qty_dispensed'     => 1,
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
        $query = $this->visit->select('id', 'user_id', 'patient_id', 'sponsor_id', 'doctor_id', 'closed', 'visit_type', 'created_at', 'waiting_for')
                        ->with([
                        'sponsor:id,name,category_name,flag', 
                        'user:id,username', 
                        'patient' => function($query){
                            $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no', 'sex')
                            ->with(['flaggedBy:id,username']);
                        }, 
                        'antenatalRegisteration' => function($query) {
                                $query->select('id')
                                ->withCount([
                                    'ancVitalSigns as ancVitalSignsCount'
                                ]);
                            }, 
                        'waitingFor:id,username',
                        'doctor:id,username'
                    ])
                    ->withCount([
                        'prescriptions as emergencyPrescriptions' => function (Builder $query) {
                        $query->where('consultation_id', null)
                                ->whereRelation('resource', 'sub_category', '!=', 'Hospital Card');
                        },
                        // 'antenatalRegisteration.ancVitalSigns as ancVitalSignsCount',
                        'payments as paymentsCount',
                        'vitalSigns as vitalSignsCount',
                        'prescriptions as prescriptionsCount'
                    ])
                    ->whereNull('consulted');

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                            $query->where('created_at', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $query->where('closed', false)
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
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'came'              => (new Carbon($visit->created_at))->diffForHumans(['parts' => 2, 'short' => true]),
                'waitingFor'        => $visit->waitingFor->username ?? '',
                'doctor'            => $visit->doctor->username ?? '',
                // 'status'            => $visit->status,
                'vitalSigns'        => $visit->vitalSignsCount,
                'ancVitalSigns'     => $visit->ancVitalSignsCount,
                'emergency'         => $visit->emergencyPrescriptions,
                'closed'            => $visit->closed,
                'initiatedBy'       => $visit->user->username,
                'payments'          => $visit->paymentsCount,
                'prescriptions'     => $visit->prescriptionsCount,
                'visitType'         => $visit->visit_type,
            ];
         };
    }

    public function changeVisitSponsor(Request $data, Visit $visit, User $user)
    {
        // --- STEP 1: PRE-TRANSACTION READS & CONTEXT GATHERING ---

        // 1. Read the NEW sponsor model based on the ID provided in the request.
        // This happens OUTSIDE the transaction.
        $newSponsor = Sponsor::find($data->sponsor); 
        
        if (!$newSponsor) {
            return response('New sponsor not found.', 404);
        }

        $sponsorCatName = $newSponsor->category_name;
        $isNhis = ($sponsorCatName == 'NHIS');
        $isIndividualOrFamily = ($sponsorCatName == 'Individual' || $sponsorCatName == 'Family');

        // 2. Read Prescriptions and Total Payments (Required for the billing process)
        $prescriptions = $visit->prescriptions()->get(['id', 'hms_bill', 'approved']); 
        $totalPayments = (float)$visit->totalPayments();
        
        // --- STEP 2: DATABASE TRANSACTION (ATOMIC WRITES) ---

        return DB::transaction(function () use ($visit, $user, $newSponsor, $isNhis, $isIndividualOrFamily, $prescriptions, $totalPayments) {
            
            // 1. Update the Visit's Sponsor (1 Query)
            // The visit model's sponsor_id is now updated in the database.
            $visit->update([
                "sponsor_id"         => $newSponsor->id,
                "sponsor_changed_by" => $user->id,
            ]);

            $ids = $prescriptions->pluck('id')->toArray();

            // 2. Perform Bulk Prescription Updates based on NEW sponsor's category
            if (!empty($ids)) {
                
                if ($isNhis) {
                    // Prescription::where('visit_id', $visit->id)
                    // ->whereIn('id', $ids)
                    // ->update([
                    //     'nhis_bill' => DB::raw('CASE 
                    //         WHEN approved = 1 THEN hms_bill / 10
                    //         ELSE hms_bill 
                    //     END')
                    // ]);

                    Prescription::join('resources', 'prescriptions.resource_id', '=', 'resources.id')
                    ->where('prescriptions.visit_id', $visit->id)
                    ->whereIn('prescriptions.id', $ids)
                    ->update([
                        'prescriptions.nhis_bill' => DB::raw("CASE 
                            /* Rule 1: Approved + Medication/Consumable = 10% */
                            WHEN prescriptions.approved = 1 AND resources.category IN ('Medications', 'Consumables') 
                                THEN prescriptions.hms_bill / 10

                            /* Rule 2: Approved + Anything else = 0.0 */
                            WHEN prescriptions.approved = 1 
                                THEN 0

                            /* Rule 3: Not Approved = Full hms_bill */
                            ELSE prescriptions.hms_bill 
                        END")
                    ]);

                } elseif ($isIndividualOrFamily) {
                    Prescription::whereIn('id', $ids)
                        ->update([
                            'nhis_bill'     => 0,
                            'approved'      => false,
                            'approved_by'   => null,
                            'rejected'      => false,
                            'rejected_by'   => null
                        ]);
                        
                } else {
                    Prescription::whereIn('id', $ids)
                        ->update(['nhis_bill' => 0]); 
                }
            }
            
            $dto = new SponsorCategoryDto(isNhis: $isNhis);
            // 3. Apply Payments Waterfall (Uses the NEW $isNhis flag)
            $this->paymentService->applyPaymentsWaterfall($visit, $totalPayments, $dto);
            
            // 4. Recalculate and Update Visit Totals (1 Query)
            // Ensure $visit is up-to-date or re-fetch if necessary to run totalNhisBills()
            // For simplicity, relying on the function to use the newly updated prescriptions data.
            $visit->refreshTotals(); 

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

        if ($data->reason && $visit->wards){
            // $ward = $this->ward->find($visit->ward_id);
            // dd($ward?->visit_id === $visit->id ,  ' is ', $visit->wards->visit_id == $ward->visit_id);
            // $ward?->visit_id === $visit->id ? $ward->update(['visit_id' => null]) : '';
            $visit->wards->update(['visit_id' => null]);
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

            $noOpenVisit = $visit->patient->visits()->where('closed', false)->count() < 2;

            if ($visit->visit_type == 'Regular' || $noOpenVisit){
                $visit->patient()->update(['is_active' => false]);
            }
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

            if ($visit->visit_type == 'Regular'){
                $visit->patient()->update(['is_active' => true]);
            }
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
            if ($visit->vitalSigns()->exists()){
                $visit->vitalSigns()->delete();
            }

            if ($visit->antenatalRegisteration) {
            if ($visit->antenatalRegisteration->ancVitalSigns()->exists()) {
                $visit->antenatalRegisteration->ancVitalSigns()->delete();
            }
            $visit->antenatalRegisteration()->delete(); // Delete the AntenatalRegisteration
        }

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
        $query      = $this->visit->select('id', 'sponsor_id', 'doctor_id', 'patient_id', 'visit_type', 'admission_status', 'created_at', 'consulted' )
        ->with([
            'sponsor:id,name,category_name', 
            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment', 
            'patient:id,first_name,middle_name,last_name,card_no,phone,address,state_of_residence,sex,date_of_birth,next_of_kin,next_of_kin_phone', 
            'doctor:id,username', 
            'antenatalRegisteration:id'
        ])
        ->withCount(['consultations as consultationsCount'])
        ->WhereNotNull('consulted');

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            if ($data->startDate && $data->endDate){
                if ($data->filterListBy){
                    return $query->where(function (Builder $query) use($data) {
                            $query->where('admission_status', $data->filterListBy)
                            ->orWhere('visit_type', $data->filterListBy);
                        })
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
                return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $query->whereDay('created_at', $current->today())
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            if ($data->filterListBy){
                return $query->where(function (Builder $query) use($data) {
                        $query->where('admission_status', $data->filterListBy)
                        ->orWhere('visit_type', $data->filterListBy);
                    })
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereDate('created_at', $current->format('Y-m-d'))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->created_at))->format('d/M/y g:ia'),
                'seen'              => $visit->consulted ? (new Carbon($visit->consulted))->format('d/M/y g:ia') : '',
                'visitType'         => $visit->visit_type,
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
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? 
                                       $visit->latestConsultation?->provisional_diagnosis ?? 
                                       $visit->latestConsultation?->assessment,
                'ancCount'          => $visit->visit_type == 'ANC' ? $visit->consultationsCount : '',
                'closed'            => true
            ];
         };
    }

    public function getLinkTovisits(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->visit->select('id', 'patient_id', 'sponsor_id', 'doctor_id', 'consulted', 'admission_status', 'closed', 'visit_type', 'discount')->with([
            'sponsor:id,name,category_name', 
            'patient' => function($query){
                            $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                            ->with(['flaggedBy:id,username']);
                        }, 
            'doctor:id,username', 
            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment'
        ])
        ->WhereNotNull('consulted');

        if (! empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

            if ($patientId){ 
                return $query->where('patient_id', $patientId)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->whereNotNull('consulted')
                    ->where(function (Builder $query) use($searchTerm) {
                        $query->where('created_at', 'LIKE', $searchTerm)
                        ->orWhere(function($q) use ($searchTerm) {
                            $terms = array_filter(explode(' ', trim($searchTerm)));
                            foreach ($terms as $term) {
                                $q->where(function($subQuery) use ($term) {
                                    $subQuery->whereRelation('patient', 'first_name', 'LIKE', $term)
                                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $term)
                                            ->orWhereRelation('patient', 'last_name', 'LIKE', $term);
                                });
                            }
                        })
                        ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm);
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereDate('id', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLinkToVisitsTransfromer()
    {
        return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'conId'             => $visit->latestConsultation?->id,
                'came'              => $visit->consulted ? (new Carbon($visit->consulted))->format('d/m/y g:ia') : '',
                'patient'           => $visit->patient->patientId(),
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'doctor'            => $visit->doctor?->username,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'admissionStatus'   => $visit->admission_status,
                'visitType'         => $visit->visit_type,
                'closed'            => $visit->closed,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
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
