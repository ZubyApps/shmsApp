<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\InvestigationsList;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestigationsListService
{
    public function __construct(private readonly InvestigationsList $investigationsList, private readonly HelperService $helperService,)
    {
    }

    public function create(Request $data, User $user): InvestigationsList
    {
        return DB::transaction(function () use ($data, $user) {
            $today = now()->toDateString();
            $lastNumber = $this->investigationsList::where('queue_date', $today)
                ->lockForUpdate()
                ->max('queue_number') ?? 0;
    
            return $user->investigationsLists()->create([
                'visit_id'   => $data->visitId,
                'walk_in_id' => $data->walkinId,
                'queue_number' => $lastNumber + 1,
            ]);
        });
    }

    public function getPaginatedList(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   = 'desc';
        $current    = CarbonImmutable::now();
        
        // Determine target date: use provided date or default to today
        $targetDate = $data->date ? Carbon::parse($data->date) : $current;

        $query = $this->investigationsList->select('id', 'user_id', 'visit_id', 'walk_in_id', 'created_at', 'status', 'queue_number')
            ->with([
                'user:id,username',
                'visit:id,patient_id,sponsor_id,admission_status,ward,bed_no,ward_id',
                'visit.patient:id,first_name,middle_name,last_name,card_no,phone',
                'visit.sponsor:id,name,category_name,sponsor_category_id',
                'visit.sponsor.sponsorCategory:id,pay_class',
                'visit.latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                // Specific filter for investigations vs imaging
                'visit.prescriptions' => function($q) {
                    $q->select('id', 'resource_id', 'visit_id', 'user_id', 'paid', 'created_at', 'test_sample', 'result', 'approved', 'rejected', 'result_date', 'result_by', 'sample_collected_at', 'approved_by')
                    ->whereHas('resource', function($sq) {
                        $sq->where('category', 'Investigations')
                            ->where('sub_category', '!=', 'Imaging');
                    })
                    ->with([
                        'resource:id,name,sub_category,category',
                        'user:id,username',
                        'thirdPartyServices.thirdParty:id,short_name',
                        'approvedBy:id,username',
                        'rejectedBy:id,username'
                    ]);
                },
                'walkIn:id,first_name,middle_name,last_name',
                'walkIn.prescriptions' => function($q) {
                    $q->select('id', 'resource_id', 'walk_in_id', 'user_id', 'paid', 'created_at', 'test_sample', 'result', 'approved', 'rejected', 'result_date', 'result_by', 'sample_collected_at', 'approved_by')
                    ->whereHas('resource', function($sq) {
                        $sq->where('category', 'Investigations')
                            ->where('sub_category', '!=', 'Imaging');
                    })
                    ->with([
                        'resource:id,name,sub_category,category',
                        'user:id,username',
                        'approvedBy:id,username',
                        'rejectedBy:id,username'
                    ]);
                }
            ]);

        // 1. Strict Date Filter (Always applied first)
        $query->whereDate('queue_date', $targetDate->toDateString());

        // 2. Search Logic (Wrapped in a single group to maintain the date filter)
        if (!empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

            $query->where(function($q) use ($searchTerm, $searchTermRaw) {
                
                // 1. If it's a number, prioritize the exact Queue Number
                if (is_numeric($searchTermRaw)) {
                    $q->where('queue_number', $searchTermRaw) // Exact match for "5"
                    ->orWhere('queue_number', 'LIKE', $searchTermRaw . '%'); // Or starts with "5"
                } else {
                    // 2. If it's text, search names and card numbers
                    $q->where('queue_number', 'LIKE', $searchTerm)
                    ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm)
                    ->orWhereRelation('visit.prescriptions.resource', 'name', 'LIKE', $searchTerm);
                }

                // 3. Name parts search (stays the same)
                $terms = array_filter(explode(' ', $searchTermRaw));
                if (count($terms) > 0 && !is_numeric($searchTermRaw)) {
                    $q->orWhere(function($nameQ) use ($terms) {
                        foreach ($terms as $term) {
                            $t = '%' . $term . '%';
                            $nameQ->whereHas('visit.patient', function($pq) use ($t) {
                                $pq->where('first_name', 'LIKE', $t)
                                ->orWhere('middle_name', 'LIKE', $t)
                                ->orWhere('last_name', 'LIKE', $t);
                            });
                        }
                    });
                }
            });
        }

        // 3. Sorting and Pagination
        // Note: Manual page calculation is usually handled by Laravel's paginate($length)
        // but I've kept your logic for custom start offsets if needed.
        return $query->orderBy($orderBy, $orderDir)
                    ->paginate(
                        $params->length, 
                        ['*'], 
                        'page', 
                        floor($params->start / $params->length) + 1
                    );
    }

    public function getLoadTransformer(): callable
    {
        return  function (InvestigationsList $investigationsList) {
           $visit = $investigationsList?->visit;
           $walkIn = $investigationsList?->walkIn;
           $latestConsultation = $visit?->latestConsultation;
           $prescriptions = $visit?->prescriptions ?? $walkIn?->prescriptions;
           
            return [
                'id'                => $investigationsList->id,
                'qNumber'           => $investigationsList->queue_number.'.',
                'queueBy'           => $investigationsList->user->username,
                'status'            => $investigationsList->status,
                'createdAt'         => (new Carbon($investigationsList->created_at))->format('d/m/y g:ia'),
                'patient'           => $visit?->patient->patientId() ?? $walkIn?->fullName(true).'(W)',
                'admissionStatus'   => $visit?->admission_status,
                'ward'              => $visit?->ward ? $this->helperService->displayWard($visit) : '',
                'wardId'            => $visit?->ward_id ?? '',
                'wardPresent'       => $visit?->wards?->visit_id == $visit?->id,
                'sponsor'           => $visit?->sponsor->name ?? '',
                'sponsorCategory'   => $visit?->sponsor->category_name ?? '',
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ??
                                            $latestConsultation?->provisional_diagnosis ??
                                            $latestConsultation?->assessment ?? '',
                'prescriptions'     => $prescriptions?->map(fn(Prescription $prescription) => [
                    'id'                => $prescription->id,
                    'type'              => $prescription->resource->category,
                    'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'resource'          => $prescription->resource->name,
                    'resourceSubCat'    => $prescription->resource->sub_category,
                    'payClass'          => $prescription?->visit?->sponsor?->sponsorCategory->pay_class,
                    'approved'          => $prescription->approved,
                    'rejected'          => $prescription->rejected,
                    'paidCheck'         => $prescription->paid,
                    'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                    'dr'                => $prescription->user->username,
                    'sample'            => $prescription->test_sample,
                    'result'            => $prescription->result,
                    'sent'              => $prescription->result_date ? (new Carbon($prescription->result_date))->format('d/m/y g:ia') : '',
                    'staff'             => $prescription->resultBy->username ?? '',
                    'thirdParty'        => $prescription?->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name,
                    'removalReason'     => $prescription->dispense_comment ? $prescription->dispense_comment . ' - ' . $prescription->discontinuedBy?->username : '',
                    'collected'         => $prescription->sample_collected_at ? (new Carbon($prescription->sample_collected_at))->format('d/m/y g:ia') : null,
                    'collectedBy'       => $prescription->sampleCollectedBy?->username,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                ])
            ];
         };
    }

    public function voidListEntry(InvestigationsList $investigationsList)
    {
        return $investigationsList->update([
            'status'            => $investigationsList->status == 3 ? 0 : 3 ,
        ]);
    }
}
