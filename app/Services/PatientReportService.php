<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PatientReportService
{
    public function __construct(
        private readonly Patient $patient, 
        private readonly Sponsor $sponsor,
        private readonly SponsorCategory $sponsorCategory,
        private readonly HelperService $helperService
        )
    {
    }

    public function getSummaryBySex(DataTableQueryParams $params, $data)
    {
        return DB::table('patients')
            ->selectRaw('sex, COUNT(patients.id) as patientsCount')
            ->groupBy('sex')
            ->orderBy('patientsCount', 'desc')
            ->get()
            ->toArray();

    }

    public function getSummaryByAge(DataTableQueryParams $params, $data)
    {
        $currentDate = new CarbonImmutable();
        
        return DB::table('patients')
            ->selectRaw("SUM(CASE WHEN DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) AS zeroTo3m, SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)) THEN 1 ELSE 0 END) AS threeTo12m, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(1)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(5)->year}) THEN 1 ELSE 0 END) AS oneTo5yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(5)->year} AND YEAR(date_of_birth) > {$currentDate->subYears(13)->year}) THEN 1 ELSE 0 END) AS fiveto13yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(13)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(18)->year}) THEN 1 ELSE 0 END) AS thirteenTo18yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(18)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(48)->year}) THEN 1 ELSE 0 END) AS eighteenTo48yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(48)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(63)->year}) THEN 1 ELSE 0 END) AS fortyEightTo63yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(63)->year}) THEN 1 ELSE 0 END) AS above63yrs , sex"
            )
            ->groupBy('sex')
            ->get()
            ->toArray();
    }

    public function getPatientsDistribution1(DataTableQueryParams $params, $data)
    {
        return DB::table('patients')
            ->selectRaw('COUNT(DISTINCT(sponsors.id)) as sponsorCount, sponsor_categories.name as category, COUNT(patients.id) as patientsCount, SUM(CASE WHEN sex = "Female" THEN 1 ELSE 0 END) AS female, SUM(CASE WHEN sex = "Male" THEN 1 ELSE 0 END) AS male')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->groupBy('category')
            ->orderBy('patientsCount', 'desc')
            ->get()
            ->toArray();
    }

    public function getPatientsDistribution2(DataTableQueryParams $params, $data)
    {
        if (! empty($params->searchTerm)) {
            return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(patients.id) as patientsCount, SUM(CASE WHEN sex = "Female" THEN 1 ELSE 0 END) AS female, SUM(CASE WHEN sex = "Male" THEN 1 ELSE 0 END) AS male')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->groupBy('sponsor')
            ->orderBy('patientsCount', 'desc')
            ->get()
            ->toArray();
        }

        return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(patients.id) as patientsCount, SUM(CASE WHEN sex = "Female" THEN 1 ELSE 0 END) AS female, SUM(CASE WHEN sex = "Male" THEN 1 ELSE 0 END) AS male')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->groupBy('sponsor')
            ->orderBy('patientsCount', 'desc')
            ->get()
            ->toArray();
    }

    public function getBySponsor(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'first_name';
        $orderDir   =  'asc';
        $current    = Carbon::now();

        if (! empty($params->searchTerm)) {

            return $this->patient
                        ->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use($params) {
                            $query->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

              
        return $this->patient
                ->where('sponsor_id', $data->sponsorId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getBySponsorTransformer(): callable
    {
        return  function (Patient $patient) {
            return [
                    'id'                => $patient->id,
                    'patient'           => $patient->patientId(),
                    'phone'             => $patient->phone,
                    'sex'               => $patient->sex,
                    'age'               => $this->helperService->twoPartDiffInTimePast($patient->date_of_birth),
                    'count'             => $patient->visits()->count(),
                    'totalHms'          => $patient->allHmsBills(),
                    'totalHmo'          => $patient->allHmoBills(),
                    'totalNhis'         => $patient->sponsor?->category_name === 'NHIS' ? $patient->allNhisBills() : 0,
                    'totalPaid'         => $patient->allPaid(),
                    'outstanding'       => $this->determineOutstanding($patient)
                ];
            };
    }

    public function determineOutstanding(Patient $patient)
    {
        $isNhis = $patient->sponsor?->category_name === 'NHIS';
        $isHmo  = $patient->sponsor?->category_name === 'HMO';

        if ($isNhis){
            return $patient->allNhisBills() - $patient->allPaid();
        }
        if ($isHmo){
            return $patient->allHmsBills() - $patient->allPaid();
        }

        return $patient->allHmsBills() - $patient->allPaid();        
    }

    public function getPatientFrequency(DataTableQueryParams $params, $data)
    {
        if (! empty($params->searchTerm)) {
            return $this->patient
                        ->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->withSum('visits as totalPaid', 'total_paid')
                        ->orderBy('totalPaid', 'desc')
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($params->orderBy == 'visitCount'){
            return $this->patient
                    ->withCount('visits as visitOrder')
                    ->orderBy('visitOrder', 'desc')
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        return $this->patient
                ->withSum('visits as visitOrder', 'total_paid')
                ->orderBy('visitOrder', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getFrequencyTransformer(): callable
    {        
        return  function (Patient $patient) {
            return [
                    'id'                => $patient->id,
                    'patient'           => $patient->patientId(),
                    'age'               => $this->helperService->twoPartDiffInTimePast($patient->date_of_birth),
                    'phone'             => $patient->phone,
                    'sponsor'           => $patient->sponsor->name,
                    'category'          => $patient->sponsor->category_name,
                    'visitCount'        => $patient->visits()->count(),
                    'totalHmsBill'      => $patient->allHmsBills(),
                    'totalHmoBill'      => $patient->allHmoBills(),
                    'totalNhisBill'     => $patient->sponsor?->category_name === 'NHIS' ? $patient->allNhisBills() : 0,
                    'totalPaid'         => $patient->allPaid(),
                ];
            };
    }

    public function getRegSummary(DataTableQueryParams $params, $data)
    {
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {
            return $this->sponsor
                ->with(
                    [
                        'patients'=> function ($query) use ($data, $current) {
                            if ($data->startDate && $data->endDate){
                                $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                ->orderBy('created_at');
                            } else if($data->date){
                                $date = new Carbon($data->date);
                                $query->whereMonth('created_at', $date->month)
                                      ->whereYear('created_at', $date->year)
                                      ->orderBy('created_at');
                            } else {
                                $query->whereMonth('created_at', $current->month)
                                ->whereYear('created_at', $current->year)
                                ->orderBy('created_at');
                            }
                        }
                    ]
                )
                ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                ->orderBy('name')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->sponsor
                ->with(
                    [
                        'patients'=> function ($query) use ($data, $current) {
                            if ($data->startDate && $data->endDate){
                                $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                ->orderBy('created_at');
                            } else if($data->date){
                                $date = new Carbon($data->date);
                                $query->whereMonth('created_at', $date->month)
                                      ->whereYear('created_at', $date->year)
                                      ->orderBy('created_at');
                            } else {
                                $query->whereMonth('created_at', $current->month)
                                      ->whereYear('created_at', $current->year)
                                ->orderBy('created_at');
                            }
                        }
                    ]
                )
                ->orderBy('name')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getRegTransformer(): callable
    {
        
        return  function (Sponsor $sponsor) {
            return [
                'id'                => $sponsor->id,
                'sponsor'           => $sponsor->name,
                'category'          => $sponsor->category_name,
                'female'            => $sponsor->patients->where('sex', 'Female')->count(),
                'male'              => $sponsor->patients->where('sex', 'Male')->count(),
                'patientCount'      => $sponsor->patients->count(),
            ];
        };
    }

    public function getBySponsorMonth(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = Carbon::now();

        if (! empty($params->searchTerm)) {

            if ($data->startDate && $data->endDate){
                return $this->patient
                        ->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use($params) {
                            $query->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));    
                }

            if ($data->date){
                $date = new Carbon($data->date);
                return $this->patient
                    ->where('sponsor_id', $data->sponsorId)
                    ->where(function (Builder $query) use($params) {
                        $query->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->patient
                        ->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use($params) {
                            $query->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->whereMonth('created_at', $current->month)
                        ->whereYear('created_at', $current->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->patient
                ->where('sponsor_id', $data->sponsorId)
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        } 

        if ($data->date){
            $date = new Carbon($data->date);
            return $this->patient
                ->where('sponsor_id', $data->sponsorId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        } 
        
        return $this->patient
                ->where('sponsor_id', $data->sponsorId)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }
}
