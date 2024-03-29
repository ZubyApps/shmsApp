<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\search;

class HmoService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService,
        private readonly Sponsor $sponsor
        )
    {
        
    }

    public function getPaginatedVerificationList(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('verified_at', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('sponsor.sponsorCategory', 'name', '=', 'HMO')
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', '=', 'NHIS')
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', '=', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVerificationListTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patientId'         => $visit->patient->id,
                'patient'           => $visit->patient->patientId(),
                'staffId'           => $visit->patient->staff_id ?? '',
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'doctor'            => $visit->doctor->username ?? '',
                'codeText'          => $visit->verification_code,
                'phone'             => $visit->patient->phone,
                'status'            => $visit->verification_status,
                '30dayCount'        => $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
            ];
         };
    }

    public function verify(Request $request, Visit $visit): Visit
    {
       
            $visit->update([
                'verification_status'   => $request->status,
                'verification_code'     => $request->codeText,
                'verified_at'           => $request->status === 'Verified' ? new Carbon() : null,
                'verified_by'           => $request->user()->id,
            ]);

            return $visit;
    }

    public function getPaginatedAllConsultedHmoVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('closed', false)
            ->where('hmo_done_by', null)
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->where(function (Builder $query) {
                $query->whereRelation('sponsor', 'category_name', 'HMO')
                ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                ->orWhereRelation('sponsor', 'category_name', 'Retainership');
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', false)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', false)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', null)
                    ->where('closed', false)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllHmoConsultedVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'doctor'            => $visit->doctor->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'admissionStatus'   => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->admission_status,
                'patientType'       => $visit->patient->patient_type,
                'labPrescribed'     => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', '=', 'Investigations')
                                        ->count(),
                'labDone'           => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource.resourceSubCategory.resourceCategory', 'name', '=', 'Investigations')
                                        ->where('result_date','!=', null)
                                        ->count(),
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'thirtyDayCount'    => explode(".", $visit->patient->patient_type)[0] == 'ANC' ? $visit->consultations->count() : $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'hmoDoneBy'         => $visit->hmoDoneBy?->username,
                'closed'            => $visit->closed,

            ];
         };
    }

    public function getPaginatedAllPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

            if (! empty($params->searchTerm)) {
                return $this->prescription
                            ->where(function (Builder $query) use($data) {
                                $query->whereRelation('visit.sponsor.sponsorCategory', 'name', ($data->sponsor == 'NHIS' ? '' : 'HMO'))
                                ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', ($data->sponsor == 'NHIS' ? 'NHIS' : ''))
                                ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', ($data->sponsor == 'NHIS' ? '' : 'Retainership'));
                            })
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.resourceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.resourceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('approvedBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                                ->orWhereRelation('rejectedBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%');
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->sponsor == 'NHIS'){
                return $this->prescription
                    ->where('approved', false)
                    ->where('rejected', false)
                    ->whereRelation('visit.sponsor.sponsorCategory', 'name', 'NHIS')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->prescription
                    ->where('approved', false)
                    ->where('rejected', false)
                    ->where(function (Builder $query) {
                        $query->whereRelation('visit.sponsor.sponsorCategory', 'name', 'HMO')
                        ->orWhereRelation('visit.sponsor.sponsorCategory', 'name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllPrescriptionsTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'sponsorCategory'   => $prescription->visit->sponsor->category_name,
                'doctor'            => $prescription->user->username,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ?? 
                                       $prescription->consultation?->provisional_diagnosis ?? 
                                       $prescription->consultation?->assessment, 
                'resource'          => $prescription->resource->name,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'note'              => $prescription->note,
                'hmsBill'           => $prescription->hms_bill ?? '',
                'hmsBillDate'       => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                'hmoBill'           => $prescription->hmo_bill,
                'hmoBillBy'         => $prescription->hmoBillBy?->username,
                'paidHms'           => $prescription->visit->totalPayments() ?? '',
                'approved'          => $prescription->approved,
                'approvedBy'        => $prescription->approvedBy?->username,
                'rejected'          => $prescription->rejected,
                'rejectedBy'        => $prescription->rejectedBy?->username,
                'dispensed'         => $prescription->dispensed,
                'hmoDoneBy'         => $prescription->visit->hmoDoneBy?->username
            ];
         };
    }

    public function approve($data, Prescription $prescription, User $user)
    {
        if ($prescription->approved == true || $prescription->rejected == true){
            return response('Already treated by ' . $prescription->approvedBy->username ?? $prescription->rejectedBy->username, 222);
        }
        
        $prescription->update([
            'approved'         => true,
            'hmo_note'          => $data->note,
            'approved_by'      => $user->id,
        ]);


        return $prescription->visit->update([
            'total_nhis_bill'   => $prescription->visit->sponsor->category_name == 'NHIS' ? $prescription->visit->totalNhisBills() : $prescription->visit->total_nhis_bill,
        ]);
    }

    public function reject($data, Prescription $prescription, User $user)
    {
        if ($prescription->approved == true || $prescription->rejected == true){
            return response('Already treated by ' . $prescription->rejectedBy->username ??  $prescription->approvedBy->username, 222);
        }
        return  $prescription->update([
            'rejected'          => true,
            'hmo_note'          => $data->note,
            'rejected_by'       => $user->id,
        ]);
    }

    public function reset(Prescription $prescription)
    {
        $prescription->update([
            'approved'          => false,
            'hmo_note'          => null,
            'approved_by'       => null,
            'rejected'          => false,
            'hmo_note'          => null,
            'rejected_by'       => null,
        ]);

        return $prescription->visit->update([
            'total_hms_bill'    => $prescription->visit->totalHmsBills(),
            'total_nhis_bill'   => $prescription->visit->sponsor->category_name == 'NHIS' ? $prescription->visit->totalNhisBills() : $prescription->visit->total_nhis_bill,
        ]);
    }

    public function getPaginatedVisitPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

            if (! empty($params->searchTerm)) {
                return $this->prescription
                            ->where('visit_id', $data->visitId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('consultation', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.rescurceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('resource.rescurceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $this->prescription
                ->where('visit_id', $data->visitId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function saveBill(Request $data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
            'hmo_bill'       => $data->bill,
            'hmo_bill_date'  => new Carbon(),
            'hmo_bill_by'    => $user->id,
            'hmo_bill_note'  => $data->note
        ]);   
        
        return $prescription;
    }

    public function markAsSent( Visit $visit, User $user)
    {
        return $visit->update([
            'hmo_done_by'    => !$visit->hmo_done_by ? $user->id : null,
            'total_hmo_bill' => !$visit->hmo_done_by ? $visit->totalHmoBills() : 0,
        ]);
    }

    public function getSentBillsList(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current    = Carbon::now();

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->Where('hmo_done_by', '!=', null)
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
                    ->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getSentBillsTransformer(): callable
    {
        return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'sponsor'           => $visit->sponsor->name,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sentBy'            => $visit->hmoDoneBy?->username,
                'totalHmsBill'      => $visit->total_hms_bill,
                'totalHmoBill'      => $visit->total_hmo_bill,
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'closed'            => $visit->closed
            ];
        };
    }

    public function getReportSummaryTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current    = Carbon::now();

        if (! empty($params->searchTerm)) {
            return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(visits.id) as visitsCount')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                        ->where('visits.hmo_done_by', '!=', null)
                        ->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->groupBy('sponsor')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();
        }

        if ($data->category){
            if ($data->startDate && $data->endDate){
                return DB::table('visits')
                            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(visits.id) as visitsCount')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                            ->where('sponsors.category_name', $data->category)
                            ->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->where('visits.hmo_done_by', '!=', null)
                            ->groupBy('sponsor')
                            ->orderBy('sponsor')
                            ->orderBy('visitsCount')
                            ->get()
                            ->toArray();
            }
            return DB::table('visits')
                            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(visits.id) as visitsCount')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                            ->where('sponsors.category_name', $data->category)
                            ->where('visits.hmo_done_by', '!=', null)
                            ->groupBy('sponsor')
                            ->orderBy('sponsor')
                            ->orderBy('visitsCount')
                            ->get()
                            ->toArray();
        }

        if ($data->startDate && $data->endDate){
            return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(visits.id) as visitsCount')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                        ->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where('visits.hmo_done_by', '!=', null)
                        ->groupBy('sponsor')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();
        }

        return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(visits.id) as visitsCount')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                        ->whereMonth('visits.created_at', $current->month)
                        ->whereYear('visits.created_at', $current->year)
                        ->where('visits.hmo_done_by', '!=', null)
                        ->groupBy('sponsor')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();   
    }

    public function getVisitsForReconciliation(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

            if (! empty($params->searchTerm)) {
                return $this->visit
                            ->where('sponsor_id', $data->sponsorId)
                            ->where('consulted', '!=', null)
                            ->where(function (Builder $query) use($params) {
                                $query->where('icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->from && $data->to){
                return $this->visit
                        ->where('sponsor_id', $data->sponsorId)
                        ->where('consulted', '!=', null)
                        ->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $this->visit
                ->where('sponsor_id', $data->sponsorId)
                ->where('consulted', '!=', null)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsForReconciliationTransformer(): callable
    {
       return  function (Visit $visit) {
            $visit->update(['total_capitation' => $visit->totalPrescriptionCapitations()]);
            return [
                'id'                    => $visit->id,
                'came'                  => (new Carbon($visit->created_at))->format('D/m/y g:ia'),                
                'patient'               => $visit->patient->patientId(),
                'consultBy'             => $visit->doctor->username,
                'diagnosis'             => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                           Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                           Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment, 
                'sponsorCategory'       => $visit->sponsor->category_name,
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'closed'                => $visit->closed,
                'totalHmsBill'          => $visit->total_hms_bill,
                'totalHmoBill'          => $visit->total_hmo_bill,
                'totalNhisBill'         => $visit->total_nhis_bill,
                'totalCapitation'       => $visit->total_capitation,
                'totalPaid'             => $visit->total_paid,
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription)=> [
                    'id'                => $prescription->id ?? '',
                    'prescribed'        => (new Carbon($prescription->created_at))->format('D/m/y g:ia') ?? '',
                    'item'              => $prescription->resource->name,
                    'prescription'      => $prescription->prescription ?? '',
                    'qtyBilled'         => $prescription->qty_billed,
                    'unit'              => $prescription->resource->unit_description,
                    'hmoBill'           => $prescription->hmo_bill ?? '',
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'capitation'        => $prescription->capitation ?? '',
                    'approved'          => $prescription->approved, 
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'note'              => $prescription->note ?? '',
                    'status'            => $prescription->status ?? '',
                    'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                    'paid'              => $prescription->paid ?? '',
                ]),
            ];
         };
    }

    public function savePayment(Request $data, Prescription $prescription, User $user)
    {
        
        return DB::transaction(function () use($data, $prescription, $user) {
            
            $prescription->update([
                'paid'      => $data->amountPaid,
                'paid_by'   => $user->id
            ]);
            $prescription->visit->total_paid = $this->determineValueOfTotalPaid($prescription->visit);
            $prescription->visit->save();

            return $prescription;
        });
    }

    public function determineValueOfTotalPaid(Visit $visit)
    {
        return $visit->sponsor->category_name == 'NHIS' ? $visit->totalPaidPrescriptions() : 
        $visit->totalPaidPrescriptions() +  $visit->totalPayments();
    }

    public function getNhisSponsorsByDate(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();
        $searchDate = $data->date ? (new Carbon($data->date)) : null;

        if ($searchDate){
            return $this->sponsor
                    ->whereRelation('sponsorCategory', 'name', '=', 'NHIS')
                    ->whereHas('visits.prescriptions', function(Builder $query) use($searchDate){
                        $query->whereMonth('created_at', $searchDate->month)
                                ->whereYear('created_at', $searchDate->year);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        return $this->sponsor
                    ->whereRelation('sponsorCategory', 'name', '=', 'NHIS')
                    ->whereHas('visits.prescriptions', function(Builder $query) use($current){
                        $query->whereMonth('created_at', $current->month)
                              ->whereYear('created_at', $current->year);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));                        
    }

    public function getSponsorsByDateTransformer($data)
    {
        return function (Sponsor $sponsor) use ($data){
            $month      = (new Carbon($data->date))->month;
            $year       = (new Carbon($data->date))->year;
            $monthYear  = (new Carbon($data->date))->format('F Y');
            return [
                'id'                => $sponsor->id,
                'sponsor'           => $sponsor->name,
                'category'          => $sponsor->category_name,
                'patientsR'         => $sponsor->patients->count(),
                'patientsC'         => $sponsor->patients()->whereHas('visits', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
                'visitsC'           => $sponsor->visits()->whereMonth('created_at', $month)->count(),
                'visitsP'           => $sponsor->visits()->whereHas('prescriptions', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
                'prescriptions'     => $sponsor->through('visits')->has('prescriptions')->whereMonth('prescriptions.created_at', $month)->count(),
                'hmsBill'           => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_hms_bill'),
                'nhisBill'          => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_nhis_bill'),
                'paid'              => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_paid'),
                'capitationPayment' => $sponsor->capitationPayments()->whereMonth('month_paid_for', $month)->whereYear('month_paid_for', $year)->first()?->amount_paid,
                'monthYear'         => $monthYear,
            ];
        };
    }
}