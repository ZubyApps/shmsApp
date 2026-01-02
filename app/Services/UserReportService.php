<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class UserReportService
{
    public function __construct(private readonly User $user) {}

    /**
     * Centralized logic to handle "This Month", "Date Range", or "Current Month"
     */
    private function applyDateFilter(Builder $query, $data, string $column = 'created_at'): Builder
    {
        if ($data->date) {
            $date = new CarbonImmutable($data->date);
            return $query->whereMonth($column, $date->month)->whereYear($column, $date->year);
        }

        if ($data->startDate && $data->endDate) {
            return $query->whereBetween($column, [
                $data->startDate . ' 00:00:00', 
                $data->endDate . ' 23:59:59'
            ]);
        }

        $current = new CarbonImmutable();
        return $query->whereMonth($column, $current->month)->whereYear($column, $current->year);
    }

    public function staffActivitiesByDesignation(DataTableQueryParams $params, $data)
    {
        $query = $this->user->select('id', 'username', 'date_of_employment')
            ->whereRelation('designation', 'designation', $data->designation)
            ->when($params->searchTerm, function ($query) use ($params) {
                $term = '%' . addcslashes($params->searchTerm, '%_') . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('firstname', 'LIKE', $term)
                      ->orWhere('middlename', 'LIKE', $term)
                      ->orWhere('lastname', 'LIKE', $term)
                      ->orWhere('username', 'LIKE', $term);
                });
            });

        // DYNAMIC COUNTING: The database only counts what is relevant to the table you are viewing.
        $query = match ($data->designation) {
            'Doctor' => $query->withCount([
                'visits as visits_count'                         => fn($q) => $this->applyDateFilter($q, $data),
                'doctorVisits as doctor_visits_count'           => fn($q) => $this->applyDateFilter($q, $data),
                'consultations as consultations_count'          => fn($q) => $this->applyDateFilter($q, $data),
                'prescriptions as prescriptions_count'          => fn($q) => $this->applyDateFilter($q, $data),
                'discontinuedPrescriptions as discontinued_count' => fn($q) => $this->applyDateFilter($q, $data),
                'surgeryNotes as surgery_notes_count'            => fn($q) => $this->applyDateFilter($q, $data),
                'vitalSigns as vital_signs_count'               => fn($q) => $this->applyDateFilter($q, $data),
                'ancVitalSigns as anc_vitals_count'              => fn($q) => $this->applyDateFilter($q, $data),
            ]),

            'Nurse' => $query->withCount([
                'vitalSigns as vital_signs_count'               => fn($q) => $this->applyDateFilter($q, $data),
                'ancVitalSigns as anc_vitals_count'              => fn($q) => $this->applyDateFilter($q, $data),
                'discontinuedPrescriptions as discontinued_count' => fn($q) => $this->applyDateFilter($q, $data),
                'prescriptions as prescriptions_count'          => fn($q) => $this->applyDateFilter($q, $data),
                'deliveryNotes as delivery_notes_count'          => fn($q) => $this->applyDateFilter($q, $data),
                'medicationCharts as charted_count'             => fn($q) => $this->applyDateFilter($q, $data),
                'givenMedications as served_count'               => fn($q) => $this->applyDateFilter($q, $data, 'time_given'), // <--- IT IS HERE
                'nursingCharts as nursing_charts_count'          => fn($q) => $this->applyDateFilter($q, $data),
                'doneNursingCharts as done_count'               => fn($q) => $this->applyDateFilter($q, $data, 'time_done'), // <--- IT IS HERE
                'nursesReports as nurses_reports_count'          => fn($q) => $this->applyDateFilter($q, $data),
            ]),

            'Lab Tech' => $query->withCount([
                'labResults as lab_results_count' => fn($q) => $this->applyDateFilter($q, $data),
            ]),

            'Pharmacy Tech' => $query->withCount([
                'pharmacyBilled as rx_billed_count'    => fn($q) => $this->applyDateFilter($q, $data),
                'pharmacyDispensed as rx_dispensed_count' => fn($q) => $this->applyDateFilter($q, $data),
            ]),

            'HMO Officer' => $query->withCount([
                'patients as patients_count'           => fn($q) => $this->applyDateFilter($q, $data),
                'visits as visits_count'               => fn($q) => $this->applyDateFilter($q, $data),
                'verifiedVisits as verified_count'     => fn($q) => $this->applyDateFilter($q, $data, 'verified_at'),
                'treatedVisits as treated_count'       => fn($q) => $this->applyDateFilter($q, $data, 'viewed_at'),
                'processedVisits as processed_count'   => fn($q) => $this->applyDateFilter($q, $data, 'hmo_done_at'),
                'closedOpenedVisits as closed_opened_count' => fn($q) => $this->applyDateFilter($q, $data, 'closed_opened_at'),
                'rxHmoBilled as rx_billed_count'       => fn($q) => $this->applyDateFilter($q, $data, 'hmo_bill_date'),
                'rxApproved as rx_approved_count'     => fn($q) => $this->applyDateFilter($q, $data, 'approved_rejected_at'),
                'rxRejected as rx_rejected_count'     => fn($q) => $this->applyDateFilter($q, $data, 'approved_rejected_at'),
                'rxPaid as rx_paid_count'             => fn($q) => $this->applyDateFilter($q, $data, 'paid_at'),
            ]),

            'Bill Officer' => $query->withCount([
                'patients as patients_count'           => fn($q) => $this->applyDateFilter($q, $data),
                'visits as visits_count'               => fn($q) => $this->applyDateFilter($q, $data),
                'closedOpenedVisits as closed_opened_count' => fn($q) => $this->applyDateFilter($q, $data, 'closed_opened_at'),
                'thirdPartyServices as third_party_count' => fn($q) => $this->applyDateFilter($q, $data),
                'payments as payments_count'           => fn($q) => $this->applyDateFilter($q, $data),
            ])->withSum(['payments as payments_total' => fn($q) => $this->applyDateFilter($q, $data)], 'amount_paid'),

            default => $query
        };

        return $query->orderBy('firstname', 'desc')
                     ->paginate($params->length, ['*'], 'page', ($params->start / $params->length) + 1);
    }

    // --- TRANSFORMERS ---

    public function getDoctorsTransformer(): callable
    {
        return fn(User $user) => [
            'id'                 => $user->id,
            'doctor'             => $user->username,
            'dateOfEmployment'   => $user->date_of_employment?->format('d/M/y g:ia'),
            'visitsInitiated'    => $user->visits_count,
            'firstConsultations' => $user->doctor_visits_count,
            'consultations'      => $user->consultations_count,
            'prescriptions'      => $user->prescriptions_count,
            'discountinued'      => $user->discontinued_count,
            'surgeryNotes'       => $user->surgery_notes_count,
            'vitalSigns'         => $user->vital_signs_count,
            'AncVitalSigns'      => $user->anc_vitals_count,
        ];
    }

    public function getNursesTransformer(): callable
    {
        return fn(User $user) =>
        [
            'id'               => $user->id,
            'nurse'            => $user->username,
            'dateOfEmployment' => $user->date_of_employment?->format('d/M/y g:ia'),
            'vitalSigns'       => $user->vital_signs_count,
            'AncVitalSigns'    => $user->anc_vitals_count,
            'discountinued'    => $user->discontinued_count,
            'prescriptions'    => $user->prescriptions_count,
            'deiveryNotes'     => $user->delivery_notes_count,
            'charted'          => $user->charted_count,
            'served'           => $user->served_count, // <--- Accesses results of 'givenMedications' relationship
            'nursingCharts'    => $user->nursing_charts_count,
            'done'             => $user->done_count,
            'nursesReports'    => $user->nurses_reports_count,
        ];
    }

    public function getLabTechsTransformer(): callable
    {
        return fn(User $user) => [
            'id'               => $user->id,
            'labTech'          => $user->username,
            'dateOfEmployment' => $user->date_of_employment?->format('d/M/y g:ia'),
            'results'          => $user->lab_results_count,
        ];
    }

    public function getPharmacyTechsTransformer(): callable
    {
        return fn(User $user) => [
            'id'               => $user->id,
            'pharmacyTech'     => $user->username,
            'dateOfEmployment' => $user->date_of_employment?->format('d/M/y g:ia'),
            'rxBilled'         => $user->rx_billed_count,
            'rxDispensed'      => $user->rx_dispensed_count,
        ];
    }

    public function getHmoOfficerTransformer(): callable
    {
        return fn(User $user) => [
            'id'               => $user->id,
            'hmoOfficer'       => $user->username,
            'dateOfEmployment' => $user->date_of_employment?->format('d/M/y g:ia'),
            'patients'         => $user->patients_count,
            'visitsInitiated'  => $user->visits_count,
            'verified'         => $user->verified_count,
            'treated'          => $user->treated_count,
            'billsProcessed'   => $user->processed_count,
            'closedAndOpened'    => $user->closed_opened_count,
            'rxHmoBilled'      => $user->rx_billed_count,
            'rxApproved'       => $user->rx_approved_count,
            'rxRejected'       => $user->rx_rejected_count,
            'rxPaid'           => $user->rx_paid_count,
        ];
    }

    public function getBillOfficerTransformer(): callable
    {
        return function (User $user) {
                return [
                'id'                 => $user->id,
                'billOfficer'        => $user->username,
                'dateOfEmployment'   => $user->date_of_employment?->format('d/M/y g:ia'),
                'patients'           => $user->patients_count,
                'visitsInitiated'    => $user->visits_count,
                'closedAndOpened'    => $user->closed_opened_count,
                'thirdPartyServices' => $user->third_party_count,
                'payments'           => $user->payments_count,
                'paymentsTotal'      => $user->payments_total ?? 0,
            ];
        };
    }
}