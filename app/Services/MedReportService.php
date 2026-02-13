<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\DeliveryNote;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\Visit;
use App\Services\HelperService;
use App\Services\PayPercentageService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class MedReportService
{
    public function __construct(
        private readonly Resource $resource, 
        private readonly HelperService $helperService,
        private readonly Prescription $prescription,
        private readonly DeliveryNote $deliveryNote,
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService
        )
    {
    }

    public function getMedServicesSummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();
        $dateConstraint = function ($query) use ($data, $current) {
                            if ($data->startDate && $data->endDate){
                                $query->whereBetween(
                                    'created_at', 
                                    [
                                        $data->startDate.' 00:00:00', 
                                        $data->endDate.' 23:59:59'
                                    ]
                                )
                                ->orderBy('created_at');
                            } else if ($data->date){
                                $date = new Carbon($data->date);
                                $query->whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->orderBy('created_at');
                            } else {
                                $query->whereMonth('created_at', $current->month)
                                        ->whereYear('created_at', $current->year)
                                        ->orderBy('created_at');
                            }
                        };
        $query = $this->resource->select('id', 'name', 'sub_category')
                    ->whereRelation('resourceSubCategory.resourceCategory', 'name', '=', 'Medical Services')
                    ->withCount(['prescriptions as prescriptionCount' => $dateConstraint])
                    ->withSum(['prescriptions as qtyBilled' => $dateConstraint], 'qty_billed');

        if (! empty($params->searchTerm)) {
            return $query
                        ->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $query->orderBy('prescriptionCount', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));       
    }

    public function getMedServicesTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'subCategory'       => $resource->sub_category,
                'prescriptions'     => $resource->prescriptionCount, //prescriptions->count(),
                'qtyPrescribed'     => $resource->qtyBilled ?? 0, //prescriptions->sum('qty_billed'),
            ];
         };
    }

    // public function getPatientsByResource(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   =  'asc';
    //     $current    = CarbonImmutable::now();

    //     if (! empty($params->searchTerm)) {
    //         if ($data->startDate && $data->endDate){
    //             return $this->prescription
    //                         ->whereRelation('resource', 'id', '=', $data->resourceId)
    //                         ->where(function (Builder $query) use($params) {
    //                             $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
    //                         })
    //                         ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
    //                         ->orderBy($orderBy, $orderDir)
    //                         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         if($data->date){
    //             $date = new Carbon($data->date);
    //             return $this->prescription
    //                 ->whereRelation('resource', 'id', '=', $data->resourceId)
    //                 ->where(function (Builder $query) use($params) {
    //                     $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
    //                 })
    //                 ->whereMonth('created_at', $date->month)
    //                 ->whereYear('created_at', $date->year)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         return $this->prescription
    //                         ->whereRelation('resource', 'id', '=', $data->resourceId)
    //                         ->where(function (Builder $query) use($params) {
    //                             $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                             ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
    //                         })
    //                         ->whereMonth('created_at', $current->month)
    //                         ->whereYear('created_at', $current->year)
    //                         ->orderBy($orderBy, $orderDir)
    //                         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->startDate && $data->endDate){
    //         return $this->prescription
    //             ->whereRelation('resource', 'id', '=', $data->resourceId)
    //             ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if($data->date){
    //         $date = new Carbon($data->date);
    //         return $this->prescription
    //             ->whereRelation('resource', 'id', '=', $data->resourceId)
    //             ->whereMonth('created_at', $date->month)
    //             ->whereYear('created_at', $date->year)
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

        
    //     return $this->prescription
    //             ->whereRelation('resource', 'id', '=', $data->resourceId)
    //             ->whereMonth('created_at', $current->month)
    //             ->whereYear('created_at', $current->year)
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    // public function getByResourceTransformer(): callable
    // {
    //     return  function (Prescription $prescription) {

    //         $pVisit = $prescription->visit;
    //         $pConsultation = $prescription->consultation;

    //         return [
    //                 'id'                => $prescription->id,
    //                 'date'              => (new Carbon($prescription->created_at))->format('d/M/y g:ia'),
    //                 'patient'           => $pVisit->patient->patientId(),
    //                 'sex'               => $prescription->visit->patient->sex,
    //                 'age'               => $this->helperService->twoPartDiffInTimePast($pVisit->patient->date_of_birth),
    //                 'sponsor'           => $pVisit->sponsor->name,
    //                 'category'          => $pVisit->sponsor->category_name,
    //                 'diagnosis'         => $pConsultation?->icd11_diagnosis ?? $pConsultation?->provisional_diagnosis,
    //                 'doctor'            => $pConsultation?->user?->username,
    //                 'Hmsbill'           => $prescription->hms_bill,
    //                 'Hmobill'           => $prescription->hmo_bill,
    //                 'paid'              => $prescription->paid,
    //             ];
    //         };
    // }

    public function getNewBirthsList(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();
        $query      = $this->deliveryNote->select('id', 'user_id', 'visit_id', 'date', 'time_of_delivery', 'sex', 'mode_of_delivery')
                        ->with([
                            'visit' => function($query){
                                $query->select('id', 'patient_id', 'sponsor_id', )
                                ->with([
                                    'patient:id,first_name,middle_name,last_name,card_no,date_of_birth',
                                    'sponsor:id,name,category_name'
                                ]);
                            }
                        ]);

        function applySearch(Builder $query, string $searchTerm): Builder
        {
            $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm )
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm )
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm )
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm )
                        ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', $searchTerm )
                        ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', $searchTerm );
                    });
        }

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                $query = applySearch($query, $params->searchTerm);
                return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                $query = applySearch($query, $params->searchTerm);
                return $query->whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            $query = applySearch($query, $params->searchTerm);
            return $query->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);
            return $query->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getNewBirthsTransformer(): callable
    {
        return  function (DeliveryNote $deliveryNote) {

            $dVisit = $deliveryNote->visit;

            return [
                    'id'                => $deliveryNote->id,
                    'date'              => (new Carbon($deliveryNote->date))->format('d-M-y'),
                    'timeofDelivery'    => (new Carbon($deliveryNote->time_of_delivery))->format('d-M-y g:ia'),
                    'mother'            => $dVisit->patient->patientId(),
                    'age'               => $this->helperService->twoPartDiffInTimePast($dVisit->patient->date_of_birth),
                    'sponsor'           => $dVisit->sponsor->name,
                    'category'          => $dVisit->sponsor->category_name,
                    'noteBy'            => $deliveryNote->user?->username,
                    'modeOfDelivery'    => $deliveryNote->mode_of_delivery,
                    'sex'               => $deliveryNote->sex,
                ];
            };
    }

    public function getVisitsByDischarge(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();
        
        $data->filterBy = $data->filterBy == "null" ? null : $data->filterBy;

        $query  =   $this->visit->select('id', 'patient_id', 'sponsor_id', 'doctor_id', 'consulted', 'admission_status', 'discharge_reason', 'total_hms_bill', 'total_paid', 'discount')
                        ->with([
                            'patient:id,first_name,middle_name,last_name,card_no,sex,date_of_birth,phone',
                            'doctor:id,username',
                            'sponsor' => function($query){
                                $query->select('id', 'sponsor_category_id', 'name', 'category_name')
                                ->with([
                                    'sponsorCategory:id,pay_class'
                                ]);
                            },
                            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment'
                        ])
                        ->where('consulted', '!=', null)
                        ->where('discharge_reason', $data->filterBy)
                        ->where(function (Builder $query) {
                            $query->where('admission_status', '=', 'Inpatient')
                            ->orWhere('admission_status', '=', 'Observation');
                        });
                        
        function applySearch(Builder $query, string $searchTerm): Builder
        {
            $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm )
                        ->orWhereRelation('patient.sponsor', 'name', 'LIKE', $searchTerm )
                        ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', $searchTerm );
                    });
        }

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                $query = applySearch($query, $params->searchTerm);
                return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                $query = applySearch($query, $params->searchTerm);
                return $query->whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            $query = applySearch($query, $params->searchTerm);
            return $query->whereMonth('created_at', $current->month)
                            ->whereYear('created_at', $current->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);
            return $query->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getByDischargeTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'date'              => (new Carbon($visit->consulted))->format('d/m/Y g:ia'),
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'sponsorCategory'       => $visit->sponsor->category_name,
                'sponsor'               => $visit->sponsor->name,
                'patient'               => $visit->patient->patientId(),
                'age'                   => $this->helperService->twoPartDiffInTimePast($visit->patient->date_of_birth),
                'sex'                   => $visit->patient->sex,
                'phone'                 => $visit->patient->phone,
                'doctor'                => $visit->doctor->username,
                'diagnosis'             =>  $visit->latestConsultation?->icd11_diagnosis ?? 
                                            $visit->latestConsultation?->provisional_diagnosis ?? 
                                            $visit->latestConsultation?->assessment ?? '',
                'admissionStatus'   => $visit->admission_status,
                'reason'            => $visit->discharge_reason,
                'totalHmsBill'      => $visit->total_hms_bill,
                'totalHmsPaid'      => $visit->total_paid,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
            ];
         };
    }

    public function getDischargeSummary(DataTableQueryParams $params, $data)
    {
        $current = CarbonImmutable::now();
        $query = DB::table('visits')
                    ->selectRaw('COUNT(DISTINCT(visits.sponsor_id)) as sponsorCount, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, visits.discharge_reason as reason')
                    ->where(function (QueryBuilder $query) {
                        $query->where('visits.admission_status', 'Inpatient')
                            ->orWhere('visits.admission_status', 'Observation');
                    })
                    ;

        if ($data->startDate && $data->endDate){
            return $query->whereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->groupBy('reason')
                        ->orderBy('patientsCount', 'desc')
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return $query->whereMonth('visits.created_at', $date->month)
                        ->whereYear('visits.created_at', $date->year)
                        ->groupBy('reason')
                        ->orderBy('patientsCount', 'desc')
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereMonth('visits.created_at', $current->month)
                    ->whereYear('visits.created_at', $current->year)
                    ->groupBy('reason')
                    ->orderBy('patientsCount', 'desc')
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current    = CarbonImmutable::now();
        $query      = $this->prescription->select('id', 'prescription', 'chartable', 'qty_billed', 'qty_dispensed', 'hms_bill', 'paid', 'created_at', 'user_id', 'resource_id', 'visit_id', 'walk_in_id')
                        ->with([
                            'user:id,username',
                            'resource:id,name',
                            'visit' => function($query){
                                $query->select('id', 'patient_id')
                                ->with([
                                    'patient:id,first_name,middle_name,last_name,card_no'
                                ]);
                            },
                            'walkIn:id,first_name,middle_name,last_name'
                        ])
                        ->withExists(['medicationCharts as hasMedicationCharts']);

        function applyPrescriptionSearch(Builder $query, string $searchTerm){
             $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->where('chartable', 'LIKE', $searchTerm )
                        ->orWhereRelation('resource', 'name', 'LIKE', $searchTerm )
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', $searchTerm );
                    });
        }

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                $query = applyPrescriptionSearch($query, $params->searchTerm);
                return $query->whereBetween('created_at', [str_replace('T', ' ', $data->startDate), str_replace('T', ' ', $data->endDate)])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                $query = applyPrescriptionSearch($query, $params->searchTerm);
                return $query->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            $query = applyPrescriptionSearch($query, $params->searchTerm);
            return $query->whereMonth('created_at', $current->month)
                            ->whereYear('created_at', $current->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $query->whereBetween('created_at', [str_replace('T', ' ', $data->startDate), str_replace('T', ' ', $data->endDate)])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);
            return $query->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $query->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllPrescriptionsTransformer(): callable
    {
        return  function (Prescription $prescription) {

            $pVisit = $prescription->visit;

            return [
                    'id'                => $prescription->id,
                    'date'              => (new Carbon($prescription->created_at))->format('d/M/y g:ia'),
                    'item'              => $prescription->resource->name,
                    'patient'           => $pVisit?->patient->patientId() ?? $prescription->walkIn?->fullName() . ' <span class="text-primary">(W)</span>',
                    'prescription'      => $prescription->prescription,
                    'chartable'         => $prescription->chartable > 0,
                    'charted'           => $prescription->hasMedicationCharts,
                    'qtyBilled'         => $prescription->qty_billed,
                    'qtyDispensed'      => $prescription->qty_dispensed,
                    'hmsBill'           => $prescription->hms_bill,
                    'paid'              => $prescription->paid,
                    'prescribedBy'      => $prescription->user->username,
                ];
            };
    }
}
