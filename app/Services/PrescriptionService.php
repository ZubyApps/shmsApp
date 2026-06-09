<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Visit;
use App\Models\WalkIn;
use App\Models\Resource;
use Carbon\CarbonImmutable;
use App\Models\NursingChart;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Models\MedicationChart;
use App\Models\MortuaryService;
use Illuminate\Support\Facades\DB;
use App\Events\PrescriptionCreated;
use App\Events\PrescriptionDeleted;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PrescriptionService
{
    public function __construct(
        private readonly Prescription $prescription, 
        private readonly ProcedureService $procedureService,
        private readonly HelperService $helperService
        )
    {
    }

    // public function createPrescription(Request $data, Resource $resource, User $user): Prescription
    // {
    //     return DB::transaction(function () use($data, $resource, $user) {
           
    //         if($data->walkInId){
    //             $prescription = $user->prescriptions()->create([
    //                 'resource_id'       => $resource->id,
    //                 'walk_in_id'        => $data->walkInId,
    //                 'qty_billed'        => $data->quantity,
    //                 'hms_bill'          => $resource->selling_price * $data->quantity,
    //                 'hms_bill_date'     => new Carbon(),
    //                 'hms_bill_by'       => $user->id,
    //             ]);

    //             $walkIn = $prescription->walkIn;
    //             $totalPayments = $walkIn->totalPayments();
    //             $prescriptions = $walkIn->prescriptions;

    //             $this->paymentService->noSponsorPaymentSeive($totalPayments, $prescriptions);

    //             $walkIn->update([
    //                 'total_bill'    => $walkIn->totalHmsBills(),
    //                 'total_paid'    => $walkIn->totalPaidPrescriptions() ?? $totalPayments,
    //             ]);

    //             return $prescription->load('walkIn');
    //         }

    //         if($data->mortuaryServiceId){
    //             $prescription = $user->prescriptions()->create([
    //                 'resource_id'       => $resource->id,
    //                 'mortuary_service_id' => $data->mortuaryServiceId,
    //                 'qty_billed'        => $data->quantity,
    //                 'hms_bill'          => $resource->selling_price * $data->quantity,
    //                 'hms_bill_date'     => new Carbon(),
    //                 'hms_bill_by'       => $user->id,
    //             ]);

    //             $mortuaryService = $prescription->mortuaryService;
    //             $totalPayments = $mortuaryService->totalPayments();
    //             $prescriptions = $mortuaryService->prescriptions;

    //             $this->paymentService->noSponsorPaymentSeive($totalPayments, $prescriptions);

    //             $mortuaryService->update([
    //                 'total_bill'    => $mortuaryService->totalHmsBills(),
    //                 'total_paid'    => $mortuaryService->totalPaidPrescriptions() ?? $totalPayments,
    //             ]);

    //             return $prescription->load('mortuaryService');
    //         }

    //         $bill = 0;
    //         $resourceSubCat = $resource->sub_category;

    //         $visit = Visit::findOrFail($data->visitId);
    //         $sponsor = $visit?->sponsor;

    //         if ($data->quantity){
    //             $bill = $resource->getSellingPriceForSponsor($sponsor) * $data->quantity;
    //         }

    //         $prescription = $user->prescriptions()->create([
    //             'resource_id'       => $resource->id,
    //             'prescription'      => $this->arrangePrescription($data),
    //             'consultation_id'   => $data->conId == 'null' ? null :  $data->conId,
    //             'visit_id'          => $data->visitId,
    //             'qty_billed'        => $this->determineBillQuantity($resource, $data),
    //             // 'qty_dispensed'     => $this->determineDispense($resource, $data),
    //             'hms_bill'          => $bill,
    //             'hms_bill_date'     => $data->quantity ? new Carbon() : null,
    //             'hms_bill_by'       => $data->quantity ? $user->id : null,
    //             'chartable'         => $this->determineChartable($resourceSubCat, $visit, $data->chartable),
    //             'note'              => $data->note,
    //             'route'             => $data->route,
    //             // 'doctor_on_call'    => $data->doc
    //         ]);

    //         if ($resourceSubCat == 'Procedure' || $resourceSubCat == 'Operation'){
    //             $this->procedureService->create($prescription, $user);
    //         }

    //         $isNhis = $sponsor->category_name == 'NHIS';

    //         $totalPayments = $visit->totalPayments();

    //         $visit->update([
    //             'total_hms_bill'    => $visit->totalHmsBills(),
    //             'total_paid'        => $totalPayments,
    //             'pharmacy_done_by'  => $resource->category == 'Medications' || $resource->category == 'Consumables' ? null : $visit->pharmacy_done_by,
    //             'nurse_done_by'     => $resourceSubCat == 'Injectable' || $resource->category == 'Consumables' ? null : $visit->nurse_done_by,
    //             'hmo_done_by'       => null
    //         ]);

    //         if ($isNhis){
    //             $prescription->update(['nhis_bill' => $bill]);
    //             $visit->update(['total_nhis_bill'   => $visit->totalNhisBills(), 'total_capitation'  => $visit->totalPrescriptionCapitations()]);
    //             $this->paymentService->prescriptionsPaymentSeiveNhis($totalPayments, $visit->prescriptions);
    //             $this->capitationPaymentService->seiveCapitationPayment($visit->sponsor, new Carbon($prescription->created_at));
    //         } else {
    //             $this->paymentService->prescriptionsPaymentSeive($totalPayments, $visit->prescriptions);
    //         }
            
    //         return $prescription->load('procedure');

    //     }, 2);
        
    // }

    // public function createPrescription(Request $data, Resource $resource, User $user): Prescription
    // {
    //     // ONLY load the parent models
    //     $visit = $data->visitId ? Visit::find($data->visitId) : null;
    //     $walkIn = $data->walkInId ? WalkIn::find($data->walkInId) : null;
    //     $mortuary = $data->mortuaryServiceId ? MortuaryService::find($data->mortuaryServiceId) : null;

    //     $bill = 0;
    //     $sponsor = $visit?->sponsor;
    //     $isNhis = $sponsor?->category_name == 'NHIS';
    //     $resourceSubCat = $resource->sub_category;

    //     if ($data->quantity){
    //         $bill = $resource->getSellingPriceForSponsor($sponsor) * $data->quantity;
    //     }

    //     $prescription = DB::transaction(function () use ($data, $resource, $user, $visit, $walkIn, $mortuary, $bill, $isNhis, $resourceSubCat) {
    //         $prescription = $user->prescriptions()->create([
    //             'resource_id'         => $resource->id,
    //             'visit_id'            => $visit?->id,
    //             'consultation_id'     => $data->conId == 'null' ? null :  $data->conId,
    //             'walk_in_id'          => $walkIn?->id,
    //             'mortuary_service_id' => $mortuary?->id,
    //             'qty_billed'          => $this->determineBillQuantity($resource, $data),
    //             'hms_bill'            => $bill,
    //             'nhis_bill'           => $isNhis ? $bill : 0,
    //             'hms_bill_date'     => $data->quantity ? new Carbon() : null,
    //             'hms_bill_by'       => $data->quantity ? $user->id : null,
    //             'chartable'         => $this->determineChartable($resourceSubCat, $visit, $data->chartable),
    //             'note'              => $data->note,
    //             'route'             => $data->route,
    //         ]);

    //         if ($resourceSubCat === 'Procedure' || $resourceSubCat === 'Operation') {
    //             $this->procedureService->create($prescription, $user);
    //         }

    //         return $prescription;
    //     });

    //     // Dispatch — listeners will load FRESH totals AFTER creation
    //     PrescriptionCreated::dispatch($prescription, $visit, $walkIn, $mortuary, $resource, $isNhis);

    //     return $prescription->load('procedure');
    // }

    public function createPrescription(Request $data, Resource $resource, User $user): Prescription
    {
        // --- STEP 1: PRE-TRANSACTION READS & CONTEXT GATHERING ---

        // 1. Load Parent Models (Efficiently find, or null)
        $visit = $data->visitId ? Visit::with('sponsor')->find($data->visitId) : null;
        $walkIn = $data->walkInId ? WalkIn::find($data->walkInId) : null;
        $mortuary = $data->mortuaryServiceId ? MortuaryService::find($data->mortuaryServiceId) : null;

        // 2. Determine Context and Price
        $sponsor = $visit?->sponsor; // Fetches sponsor if visit exists
        
        // Default to false if no sponsor/visit
        $isNhis = ($sponsor && $sponsor->category_name === 'NHIS');
        
        $resourceSubCat = $resource->sub_category;
        $quantity = (float)($data->quantity ?? 0); // Ensure quantity is a float/int
        $hasQuantity = $quantity > 0;
        $billArray = null;
        $bill = 0.00;

        if ($hasQuantity && $visit) {
            // Calculate bill once outside the transaction
            $billArray = $this->helperService->biller($resource, $sponsor, $data->quantity);
        } else {
            $bill = $resource->getSellingPriceForSponsor($sponsor) * $data->quantity;
        }
        
        // Prepare transaction variables
        $visitId = $visit?->id;
        $walkInId = $walkIn?->id;
        $mortuaryId = $mortuary?->id;

        // $conId = ($data->conId === 'null' || $data->conId === null) ? null : $data->conId;
        $conId = in_array($data->conId, ['null', null, ''], true) ? null : $data->conId;
        

        // --- STEP 2: DATABASE TRANSACTION (Atomic Write) ---

        $prescription = DB::transaction(function () use ($data, $resource, $user, $visitId, $walkInId, $mortuaryId, $billArray, $isNhis, $resourceSubCat, $hasQuantity, $conId, $bill) {
            // Prepare creation data
            $creationData = [
                'resource_id'           => $resource->id,
                'visit_id'              => $visitId,
                'prescription'          => $this->arrangePrescription($data),
                'consultation_id'       => $conId,
                'walk_in_id'            => $walkInId,
                'mortuary_service_id'   => $mortuaryId,
                'chartable'             => $this->determineChartable($resourceSubCat, $data->chartable),
                'note'                  => $data->note,
                'route'                 => $data->route,
                'doctor_on_call'        => $data->doc
            ];
            
            // Only set bill date/user if a quantity was billed
            if ($hasQuantity) {
                $creationData['qty_billed'] = $this->determineBillQuantity($resource, $data);
                $creationData['hms_bill']   = $billArray['bill'] ?? $bill;
                $creationData['nhis_bill']  = $isNhis ? $billArray['nhisBill'] : 0.0;
                $creationData['hms_bill_date'] = Carbon::now();
                $creationData['hms_bill_by'] = $user->id;
            }

            $prescription = $user->prescriptions()->create($creationData);

            // Nested Service Call (If required, keep inside for atomicity with prescription creation)
            if ($resourceSubCat === 'Procedure' || $resourceSubCat === 'Operation') {
                // The creation of the procedure MUST be atomic with the prescription.
                $this->procedureService->create($prescription, $user); 
            }

            return $prescription;
        });

        // --- STEP 3: SYNCHRONOUS RECALCULATION & DISPATCH ---

        // The event listener will now synchronously handle the total updates (faster than the old N+1 code).
        PrescriptionCreated::dispatch($prescription, $isNhis, $resource, $visit, $walkIn, $mortuary);

        // Conditional loading saves database overhead if it's not a procedure type
        return ($resourceSubCat === 'Procedure' || $resourceSubCat === 'Operation')
            ? $prescription->load('procedure')
            : $prescription;
    }

    public function arrangePrescription(Request $data)
    {
        return $data->dose ? $data->dose.$data->unit.' '.$data->frequency.' for '.$data->days.'day(s)' : null;
    }

    public function determineBillQuantity(Resource $resource, Request $data)
    {
        if ($resource->category == 'Medications' || $resource->category == 'Consumables'){
            return $resource->stock_level - $data->quantity < 0 ? 0 : $data->quantity ?? 0;
        }

        return $data->quantity ?? 0;
    }

    // public function determineChartable($subCategory, $visit, $chartable)
    // {
    //     return $subCategory == 'Injectable' || ($visit?->admission_status == 'Inpatient' && $subCategory == 'Pill') ? true : $chartable ?? false;
    // }

    public function determineChartable(string $subCategory, mixed $chartable)
    {
        return $subCategory == 'Injectable' ? true : $chartable ?? false;
    }
    
    public function createBulkPrescriptions(Collection $resources, Request $data, User $user, Visit $visit): int
    {
        $prescriptionData = [];
        $isNhis = $visit->sponsor->category_name == 'NHIS';
        $now        = now('Africa/Lagos');

        $resources->loadMissing([
            'sponsors:id,category_name', // For getSellingPriceForSponsor
            'resourceSubCategory.resourceCategory:id' // For the category-based percentage logic
        ]);

        // Ensure the sponsor's rules are also in memory
        $visit->sponsor->loadMissing('resourceCategories');

        foreach ($resources as $resource) {
            // if ($request->quantity > 0) {
            //     $sellingPrice = $resource->getSellingPriceForSponsor($visit->sponsor);
            //     $bill = $sellingPrice * $request->quantity;
            // }

            $billArray = $this->helperService->biller($resource, $visit->sponsor, $data->quantity, false);

            $prescriptionData[] = [
                'resource_id'           => $resource->id,
                'visit_id'              => $data->visitId,
                'prescription'          => $this->arrangePrescription($data),
                'consultation_id'       => $data->conId,
                'qty_billed'            => $this->determineBillQuantity($resource, $data),
                'hms_bill'              => $billArray['bill'],
                'nhis_bill'             => $isNhis ? $billArray['nhisBill'] : 0.0,
                'user_id'               => $user->id,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }
        
        if (empty($prescriptionData)) {
            return 0;
        }

        // Single database trip for insertion
        DB::table('prescriptions')->insert($prescriptionData);

        return count($prescriptionData);
    }

    public function getPaginatedInitialPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->prescription->select('id', 'resource_id', 'user_id', 'prescription', 'created_at', 'qty_billed', 'qty_dispensed', 'hms_bill', 'chartable', 'note', 'route')->with([
            'resource:id,name,category,stock_level', 
            'user:id,username', 
            'thirdPartyServices' => function ($query) {
                $query->select('id', 'prescription_id', 'third_party_id')
                ->with(['thirdParty:id,short_name']);
            }, 
        ])
        ->withExists([
            'procedure as hasProcedure',
            'medicationCharts as hasMedChart',
            'nursingCharts as hasNurseChart'
            ]);

        if (! empty($params->searchTerm)) {
            return $query->where('visit_id', $data->visitId)
                        ->where(function(Builder $query) use ($params) {
                            $query->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );

                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if (!$data->conId){
            return $query->where('visit_id', $data->visitId)
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        } else{
            return $query->where('consultation_id', $data->conId)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

       
    }

    public function getInitialLoadTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'category'          => $prescription->resource->category,
                'stock'             => $prescription->resource->stock_level,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed < 1 ? '' : $prescription->qty_billed,
                'hmsBill'           => $prescription->hms_bill ?? '',
                'by'                => $prescription->user->username,
                'chartable'         => $prescription->chartable ? 'Yes' : 'No',
                'note'              => $prescription->note,
                'route'             => $prescription->route,
                'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name ?? '',
                'dispensed'         => $prescription->qty_dispensed > 0,
                'procedure'         => $prescription->hasProcedure,
                'charted'           => $prescription->hasMedChart || $prescription->hasNurseChart,
            ];
         };
    }

    public function getPaginatedLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->prescription
                        ->select('id', 'resource_id', 'visit_id', 'consultation_id', 'user_id', 'paid', 'created_at', 'test_sample', 'result', 'approved', 'rejected', 'result_date', 'dispense_comment', 'result_by', 'discontinued_by', 'sample_collected_at', 'sample_collected_by', 'hms_bill', 'hmo_note', 'approved_by', 'rejected_by')->with([
                            'resource:id,name,sub_category,category', 
                            'user:id,username', 
                            'thirdPartyServices' => function ($query) {
                                $query->select('id', 'prescription_id', 'third_party_id')
                                ->with(['thirdParty:id,short_name']);
                            }, 
                            'visit' => function($query) {
                                $query->select('id', 'sponsor_id','patient_id')
                                ->with([
                                    'sponsor'  => function ($query) {
                                        $query->select('id', 'sponsor_category_id', 'name', 'category_name')
                                        ->with(['sponsorCategory:id,pay_class']);
                                    },
                                    'patient:id,first_name,middle_name,last_name,date_of_birth']);
                            },
                            'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment',
                            'resultBy:id,username',
                            'sampleCollectedBy:id,username',
                            'approvedBy:id,username',
                            'rejectedBy:id,username',
                        ]);

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->whereRelation('resource', 'category', 'Investigations')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLabTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'patient'           => $prescription->visit->patient->patientFullInfo(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'type'              => $prescription->resource->category,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'resource'          => $prescription->resource->name,
                'resourceSubCat'    => $prescription->resource->sub_category,
                'sponsorCategory'   => $prescription->visit->sponsor->category_name,
                'payClass'          => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'          => $prescription->approved,
                'rejected'          => $prescription->rejected,
                'paidCheck'         => $prescription->paid,
                'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ??
                                       $prescription->consultation?->provisional_diagnosis ??
                                       $prescription->consultation?->assessment,
                'dr'                => $prescription->user->username,
                'sample'            => $prescription->test_sample,
                'result'            => $prescription->result,
                'sent'              => $prescription->result_date ? (new Carbon($prescription->result_date))->format('d/m/y g:ia') : '',
                'staff'             => $prescription->resultBy->username ?? '',
                'staffFullName'     => $prescription->resultBy?->nameInFull() ?? '',
                'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name,
                'removalReason'     => $prescription->dispense_comment ? $prescription->dispense_comment . ' - ' . $prescription->discontinuedBy?->username : '',
                'collected'         => $prescription->sample_collected_at ? (new Carbon($prescription->sample_collected_at))->format('d/m/y g:ia') : null,
                'collectedBy'       => $prescription->sampleCollectedBy?->username,
                'hmoNote'           => $prescription->hmo_note ?? '',
                'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
            ];
         };
    }

    public function getPaginatedMedications(DataTableQueryParams $params, Request $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        $query = $this->prescription->select('id', 'resource_id', 'visit_id', 'consultation_id', 'user_id', 'paid', 'created_at', 'prescription', 'chartable', 'approved', 'rejected', 'held', 'discontinued', 'note', 'qty_billed', 'discontinued_by', 'discountinued_at', 'qty_dispensed', 'route', 'hms_bill', 'nhis_bill', 'held_by')->with([
            'resource:id,name,category', 
            'visit' => function($query) {
                $query->select('id', 'sponsor_id','patient_id')
                ->with([
                    'sponsor'  => function ($query) {
                        $query->select('id', 'sponsor_category_id', 'name', 'category_name')
                        ->with(['sponsorCategory:id,pay_class']);
                    },
                    'patient:id,first_name,middle_name,last_name,date_of_birth,card_no']);
            },
            'consultation:id',
            'medicationCharts' => function($query) {
                $query->select('id', 'prescription_id', 'user_id', 'given_by', 'created_at', 'dose_prescribed', 'scheduled_time', 'dose_given', 'time_given', 'not_given', 'note', 'status', 'dose_count')
                    ->with(['user:id,username', 'givenBy:id,username']);
            },
            'user:id,username',
            'discontinuedBy:id,username',
            'heldBy:id,username',
        ])
        ->withCount([
            'medicationCharts as doseCount',
            'medicationCharts as givenCount' => function (Builder $query) {
                $query->whereNotNull('dose_given');
            },
        ]);

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->whereRelation('resource', 'sub_category', 'Injectable')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getOtherPrescriptions(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->prescription->select('id', 'resource_id', 'visit_id', 'consultation_id', 'user_id', 'paid', 'created_at', 'prescription', 'chartable', 'approved', 'rejected', 'held', 'discontinued', 'note', 'qty_billed', 'discontinued_by', 'discountinued_at', 'qty_dispensed', 'route', 'hms_bill', 'nhis_bill', 'held_by')->with([
            'resource:id,name,category', 
            'user:id,username', 
            'visit' => function($query) {
                $query->select('id', 'sponsor_id','patient_id')
                ->with([
                    'sponsor'  => function ($query) {
                        $query->select('id', 'sponsor_category_id', 'name', 'category_name')
                        ->with(['sponsorCategory:id,pay_class']);
                    },
                    'patient:id,first_name,middle_name,last_name,date_of_birth,card_no']);
            },
            'consultation:id',
            'nursingCharts' => function($query) {
                $query->select('id', 'created_at', 'prescription_id', 'done_by', 'care_prescribed', 'scheduled_time', 'time_done', 'not_done', 'note', 'status', 'schedule_count')
                    ->with(['user:id,username', 'doneBy:id,username']);
            },
            'heldBy:id,username',
            'discontinuedBy:id,username',
        ])
        ->withCount([
            'nursingCharts as scheduleCount',
            'nursingCharts as doneCount' => function (Builder $query) {
                $query->whereNotNull('time_done');
            },
        ]);

        return $query->where($data->conId ? 'consultation_id': 'visit_id', $data->conId ? $data->conId : $data->visitId)
                    ->where(function(Builder $query) {
                        $query->whereRelation('resource', 'category', 'Medical Services')
                        ->orWhereRelation('resource', 'category', 'Consumables')
                        ->orWhereRelation('resource', 'category', 'Medications')
                        ->orWhere('chartable', true);
                    })
                    ->whereRelation('resource', 'sub_category', '!=', 'Injectable')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPrescriptionsTransformer(): callable
    {
        $carbon = New Carbon();
       return  function (Prescription $prescription) use ($carbon) {
            return [
                'id'                    => $prescription->id,
                'prescribedBy'          => $prescription?->user->username,
                'resource'              => $prescription->resource->name,
                'resourceCategory'      => $prescription->resource->category,
                'prescription'          => $prescription->prescription ?? '',
                'prescribed'            => ($carbon->parse($prescription->created_at))->format('D d/m/y g:ia'),
                'prescribedFormatted'   => ($carbon->parse($prescription->created_at))->format('Y-m-d\TH:i'),
                'qtyBilled'             => $prescription->qty_billed ?? '',
                'qtyDispensed'          => $prescription->qty_dispensed ?? '',
                'held'                  => $prescription->held,
                'heldBy'                => $prescription->heldBy?->username,
                'note'                  => $prescription->note,
                'route'                 => $prescription->route,
                'conId'                 => $prescription->consultation?->id,
                'visitId'               => $prescription->visit->id,
                'patient'               => $prescription->visit->patient->patientId(),
                'sponsor'               => $prescription->visit->sponsor->name,
                'sponsorCategory'       => $prescription->visit->sponsor->category_name,
                'payClass'              => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'              => $prescription->approved,
                'rejected'              => $prescription->rejected,
                'chartable'             => $prescription->chartable,
                'discontinued'          => $prescription->discontinued,
                'discontinuedBy'        => $prescription->discontinuedBy?->username,
                'discontinuedAt'       => $prescription->discountinued_at ? ($carbon->parse($prescription->discountinued_at))->format('d/m/y g:ia') : '',
                'paid'                  => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'              => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                'doseCount'             => $doseCount = $prescription->doseCount,
                'givenCount'            => $givenCount = $prescription->givenCount,//medicationCharts->where('dose_given', '!=', null)->count(),
                'doseComplete'          => $this->completed($doseCount, $givenCount),
                
                'medicationCharts'      => $prescription->medicationCharts?->map(fn(MedicationChart $medicationChart) => [
                    'id'                => $medicationChart->id ?? '',
                    'chartedAt'         => ($carbon->parse($medicationChart->created_at))->format('D d/m/y g:ia') ?? '',
                    'chartedBy'         => $medicationChart->user->username ?? '',
                    'dosePrescribed'    => $medicationChart->dose_prescribed ?? '',
                    'scheduledTime'     => ($carbon->parse($medicationChart->scheduled_time))->format('g:ia D d/m/y') ?? '',
                    'givenDose'         => $medicationChart->dose_given ?? 'Not yet given' ?? '',
                    'timeGiven'         => $medicationChart->time_given ? ($carbon->parse($medicationChart->time_given))->format('g:ia D d/m/Y') : '',
                    'givenBy'           => $medicationChart->givenBy->username ?? '',
                    'note'              => $medicationChart->not_given ? $medicationChart->not_given.' - '.$medicationChart->note ?? '' : $medicationChart->note ?? '' ,
                    'status'            => $medicationChart->status ?? '',
                    'doseCount'         => $medicationChart->dose_count,
                    'count'             => $prescription->doseCount,
                    'patient'           => $prescription->visit->patient->patientId() ?? ''
                ]),

                'scheduleCount'         => $scheduleCount = $prescription->scheduleCount,
                'doneCount'            => $doneCount = $prescription->doneCount,
                'serviceComplete'       => $this->completed($scheduleCount, $doneCount),

                'prescriptionCharts'    => $prescription->nursingCharts?->map(fn(NursingChart $nursingChart)=> [
                    'id'                => $nursingChart->id ?? '',
                    'chartedAt'         => ($carbon->parse($nursingChart->created_at))->format('D/m/y g:ia') ?? '',
                    'chartedBy'         => $nursingChart->user->username ?? '',
                    'carePrescribed'    => $nursingChart->care_prescribed ?? '',
                    'treatment'         => $prescription->resource->name,
                    'instruction'       => $prescription->note ?? '',
                    'scheduledTime'     => ($carbon->parse($nursingChart->scheduled_time))->format('g:ia D jS') ?? '',
                    'timeDone'          => $nursingChart->time_done ? ($carbon->parse($nursingChart->time_done))->format('g:ia D jS') : '',
                    'doneBy'            => $nursingChart->doneBy->username ?? '',
                    'note'              => $nursingChart->note ?? $nursingChart->not_done ?? '',
                    'status'            => $nursingChart->status ?? '',
                    'scheduleCount'     => $nursingChart->schedule_count,
                    'count'             => $prescription->scheduleCount,
                    'patient'           => $prescription->visit->patient->patientId() ?? ''
                ]),
            ];
         };
    }

    public function completed($count1, $count2): bool
    {
        return $count1 !== 0 && $count1 === $count2;
    }

    public function discontinue(Request $request, Prescription $prescription)
    {
        return $prescription->update([
            'discontinued'      => !$prescription->discontinued,
            'discontinued_by'   => $request->user()->id,
            'discountinued_at'  => Carbon::now()
        ]);
    }

    public function getEmergencyPrescriptions(DataTableQueryParams $params, $data)
    {
        $isPharm = ($data->viewer === 'pharmacy');

        // 1. Base Query with specific column selection
        $query = $this->prescription->query()
            ->select([
                'id', 'visit_id', 'resource_id', 'user_id', 'chartable', 'prescription', 
                'qty_billed', 'note', 'qty_dispensed', 'created_at', 'doctor_on_call', 
                'held_by', 'approved', 'rejected', 'paid', 'hms_bill', 'nhis_bill'
            ])
            ->with([
                'resource:id,name,stock_level,unit_description', 
                'user:id,username', 
                'visit' => function($query) {
                    $query->select(['id', 'patient_id', 'sponsor_id', 'closed', 'visit_type', 'admission_status', 'discharge_reason', 'discharge_remark', 'doctor_done_at', 'doctor_done_by', 'ward'])
                        ->with([
                            'doctorDoneBy:id,username',
                            'patient:id,first_name,middle_name,last_name,card_no',
                            'sponsor' => function($subQuery) {
                                $subQuery->select(['id', 'sponsor_category_id', 'name', 'category_name']) // Select ONLY what sponsor needs
                                        ->with(['sponsorCategory:id,pay_class']);
                            },
                            'wards:id,visit_id,short_name,bed_number'
                        ]);
                },
                'heldBy:id,username',
                'doctorOnCall:id,username'     
            ])
            ->withCount([
                'medicationCharts as doseCount',
                'medicationCharts as givenCount' => fn($q) => $q->whereNotNull('dose_given'),
            ])
            ->whereNotNull('visit_id')
            ->whereNull('consultation_id');

        // 2. Optimized Category Filter (Consolidated into one subquery)
        $categories = $isPharm 
            ? ['Medications', 'Medical Services', 'Consumables'] 
            : ['Medications', 'Medical Services', 'Consumables', 'Investigations'];

        $query->where(function($q) use ($categories) {
            $q->whereHas('resource', fn($res) => $res->whereIn('category', $categories))
            ->orWhere('chartable', true);
        });

        if ($isPharm) {
            $query->where('qty_dispensed', 0);
        }

        // 3. Search Logic using your Patient Scope
        if (!empty($params->searchTerm)) {
            $query->where(function($q) use ($params) {
                $q->whereHas('visit.patient', function($p) use ($params) {
                    // Utilizing your existing scope
                    $p->searchByName($params->searchTerm); 
                })
                // Still allow searching by the Resource name
                ->orWhereHas('resource', function($r) use ($params) {
                    $r->where('name', 'LIKE', '%' . $params->searchTerm . '%');
                });
            });
        }

        // 4. Clean Execution
        $page = ($params->length > 0) ? (($params->length + $params->start) / $params->length) : 1;
        
        return $query->orderBy('created_at', 'desc')
                    ->paginate($params->length, ['*'], 'page', $page);
    }

    public function getEmergencyPrescriptionsformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'visitId'           => $prescription->visit?->id,
                'patient'           => $prescription->visit?->patient->patientId() . ($prescription->visit->visit_type == 'ANC' ? ' (ANC)' : ''),
                'sponsor'           => $prescription->visit?->sponsor->name,
                'closed'            => $prescription->visit?->closed,
                'sponsorCategory'   => $prescription->visit?->sponsor->category_name,
                'sponsorCategoryClass' => $prescription->visit?->sponsor?->sponsorCategory?->pay_class,
                'visitType'         => $prescription->visit->visit_type,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'item'              => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'prescribedBy'      => $prescription->user->username,
                'doc'               => $prescription->doctorOnCall?->username,
                'note'              => $prescription->note,
                'admissionStatus'   => $prescription->visit?->admission_status,
                'prescribedFormatted'   => (new Carbon($prescription->created_at))->format('Y-m-d\TH:i'),
                'chartable'             => $prescription->chartable,
                'doseCount'             => $doseCount = $prescription->doseCount,
                'givenCount'            => $givenCount = $prescription->givenCount,
                'doseComplete'          => $this->completed($doseCount, $givenCount),
                'medicationCharts'      => $prescription->doseCount,
                'qtyBilled'             => $prescription->qty_billed,
                'qtyDispensed'          => $prescription->qty_dispensed,
                'stock'                 => $prescription->resource->stock_level,
                'unit'                  => $prescription->resource->unit_description,
                'approved'              => $prescription->approved,
                'rejected'              => $prescription->rejected,
                'paid'                  => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paid1'                 => $prescription->paid,
                'paidNhis'              => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit?->sponsor->category_name == 'NHIS',
                'discharged'        => $prescription->visit->discharge_reason,
                'reason'            => $prescription->visit->discharge_reason,
                'remark'            => $prescription->visit->discharge_remark ?? '',
                'doctorDone'        => $prescription->visit->doctorDoneBy?->username ?? '',
                'doctorDoneAt'      => $prescription->visit->doctor_done_at ? (new Carbon($prescription->visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'ward'              => $prescription->visit->ward ? $this->helperService->displayWard($prescription->visit) : '',
            ];
         };
    }

    public function confirm(Prescription $prescription)
    {
        $consultationId = $prescription->visit
                        ->consultations()
                        ->latest('id')
                        ->value('id');

         if (!$consultationId) {
                return response('Cannot confirm! A Doctor must complete a consultation first!', 403); // frontend is set up to receive this
            }

        return DB::transaction(function () use ($prescription, $consultationId) {

            // Update prescription + all related medication charts in one go (faster & fewer queries)
            $prescription->update(['consultation_id' => $consultationId]);

            // Bulk update all related medication charts instead of looping
            $prescription->medicationCharts()
                ->update(['consultation_id' => $consultationId]);

            // Optionally return a nice success response
            return response()->json(['message' => 'Prescription confirmed successfully.'], 200);
        });
    }

    public function processDeletion(Prescription $prescription)
    {
        // --- STEP 1: PRE-TRANSACTION READS & CONTEXT GATHERING ---
        
        // Identify the associated entity
        if ($prescription->visit_id) {
            // Eager load sponsor for the event listener
            $relatedModel = $prescription->visit()->with('sponsor')->first(); 
        } elseif ($prescription->walkIn) {
            $relatedModel = $prescription->walkIn;
        } elseif ($prescription->mortuaryService) {
            $relatedModel = $prescription->mortuaryService;
        } else {
            $relatedModel = null;
        }

        // Get Dispensed Quantity and Resource (if stock needs returning)
        $qtyDispensed = $prescription->qty_dispensed;
        $resource = ($qtyDispensed) ? $prescription->resource : null;

        // --- STEP 2: DATABASE TRANSACTION (Atomic Writes: Delete and Stock Return) ---

        $deleted = DB::transaction(function () use($prescription, $qtyDispensed, $resource) {

            // 1. Stock Return (If applicable)
            if ($qtyDispensed && $resource) {
                $resource->increment('stock_level', $qtyDispensed);
            }

            // 2. Delete the Prescription (1 Query)
            return $prescription->delete(); // Returns true/false
        });

        // --- STEP 3: DISPATCH EVENT (After Transaction Commit) ---
        
        // Dispatch the event to handle heavy recalculation and sieves
        if ($relatedModel) {
            PrescriptionDeleted::dispatch($relatedModel, $prescription->created_at);
        }
        
        return $deleted;
    }

    public function totalYearlyIncomeFromPrescription($data)
    {
        $currentDate = new Carbon();

        if ($data->year){

            return DB::table('prescriptions')
                            ->selectRaw('SUM(hms_bill) as bill, SUM(hmo_bill) as totalHmoBill, SUM(nhis_bill) as totalNhisBill, SUM(paid + capitation) as paid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $data->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('prescriptions')
                        ->selectRaw('SUM(hms_bill) as bill, SUM(hmo_bill) as totalHmoBill, SUM(nhis_bill) as totalNhisBill, SUM(paid + capitation) as paid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }

    public function hold(Request $data, Prescription $prescription, User $user)
    {
        return $prescription->update([
            'held' => $data->reason,
            'held_at' => new Carbon(),
            'held_by' => $user->id,
        ]);
    }

    public function totalYearlyIncomeFromPrescriptionCash($data)
    {
        $currentDate = new Carbon();

        if ($data->year){

            return DB::table('prescriptions')
                            // ->selectRaw('SUM(prescriptions.paid + prescriptions.capitation) as cashPaid, MONTH(prescriptions.created_at) as month, MONTHNAME(prescriptions.created_at) as month_name')
                            ->selectRaw('SUM(prescriptions.paid) as cashPaid, MONTH(prescriptions.created_at) as month, MONTHNAME(prescriptions.created_at) as month_name')
                            ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->whereYear('prescriptions.created_at', $data->year)
                            ->where(function(QueryBuilder $query) {
                                $query->where('sponsors.category_name', 'Individual')
                                ->orWhere('sponsors.category_name', 'Family')
                                ->orWhere('sponsors.category_name', 'NHIS')
                                ->orWhere('sponsors.category_name', 'Retainership');
                            })
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('prescriptions')
                        // ->selectRaw('SUM(prescriptions.paid + prescriptions.capitation) as cashPaid, MONTH(prescriptions.created_at) as month, MONTHNAME(prescriptions.created_at) as month_name')
                        ->selectRaw('SUM(prescriptions.paid) as cashPaid, MONTH(prescriptions.created_at) as month, MONTHNAME(prescriptions.created_at) as month_name')
                        ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->whereYear('prescriptions.created_at', $currentDate->year)
                        ->where(function(QueryBuilder $query) {
                            $query->where('sponsors.category_name', 'Individual')
                            ->orWhere('sponsors.category_name', 'Family')
                            ->orWhere('sponsors.category_name', 'NHIS')
                            ->orWhere('sponsors.category_name', 'Retainership');
                        })
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }

    public function getByResource(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'created_at';
        $orderDir = 'asc';
        $page     = ($params->length + $params->start) / $params->length;

        // 1. Initialize Base Query & Eager Loading
        $query = $this->prescription->select('id', 'consultation_id', 'visit_id', 'walk_in_id', 'created_at', 'hms_bill', 'hmo_bill', 'paid', 'qty_billed', 'qty_dispensed')
            ->with([
                'visit:id,patient_id,sponsor_id',
                'visit.patient:id,first_name,middle_name,last_name,card_no,sex,date_of_birth',
                'visit.sponsor:id,name,category_name',
                'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment,user_id',
                'consultation.user:id,username',
                'walkIn:id,first_name,middle_name,last_name,sex,date_of_birth',
            ])
            ->where('resource_id', $data->resourceId); // Direct ID check is faster than whereRelation

        // 2. Optimized Search Logic
        if (!empty($params->searchTerm)) {
            $termRaw = $params->searchTerm;
            $term = '%' . addcslashes($termRaw, '%_') . '%';

            $query->whereHas('visit', function ($q) use ($term, $termRaw) {
                $q->whereHas('patient', function ($pq) use ($term, $termRaw) {
                    $pq->searchByName($termRaw) // Assuming your Full-Text scope
                    ->orWhere('card_no', 'LIKE', $term);
                })
                ->orWhereHas('sponsor', function ($sq) use ($term) {
                    $sq->where('name', 'LIKE', $term)
                    ->orWhere('category_name', 'LIKE', $term);
                });
            });
        }

        // 3. SARGable Date Range Logic
        if ($data->startDate && $data->endDate) {
            $start = $data->startDate . ' 00:00:00';
            $end   = $data->endDate . ' 23:59:59';
        } else {
            $baseDate = $data->date ? CarbonImmutable::parse($data->date) : CarbonImmutable::now();
            $start    = $baseDate->startOfMonth()->toDateTimeString();
            $end      = $baseDate->endOfMonth()->toDateTimeString();
        }

        // 4. Final Execution
        return $query->whereBetween('created_at', [$start, $end])
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, ['*'], 'page', $page);
    }

    public function getByResourceTransformer(): callable
    {
        return  function (Prescription $prescription) {

            $pVisit         = $prescription?->visit;
            $pConsultation  = $prescription?->consultation;
            $pWalkIn        = $prescription?->walkIn;
            $dateOfBirth    = $pVisit?->patient->date_of_birth ?? $pWalkIn?->date_of_birth;

            return [
                    'id'                => $prescription->id,
                    'date'              => (new Carbon($prescription->created_at))->format('d/M/y g:ia'),
                    'patient'           => $pVisit?->patient->patientId() ?? $prescription?->walkIn?->fullName(),
                    'sex'               => $pVisit?->patient->sex ?? $pWalkIn?->sex,
                    'age'               => $dateOfBirth ? $this->helperService->twoPartDiffInTimePast($dateOfBirth) : '',
                    'sponsor'           => $pVisit?->sponsor->name,
                    'category'          => $pVisit?->sponsor->category_name  ?? "WalkIn",
                    'diagnosis'         => $pConsultation?->icd11_diagnosis ?? $pConsultation?->provisional_diagnosis ?? $pConsultation?->assessment,
                    'doctor'            => $pConsultation?->user?->username,
                    'Hmsbill'           => $prescription->hms_bill,
                    'Hmobill'           => $prescription->hmo_bill,
                    'paid'              => $prescription->paid,
                    'qtyBilled'         => $prescription->qty_billed,
                    'qtyDispensed'      => $prescription->qty_dispensed,
                ];
            };
    }
}