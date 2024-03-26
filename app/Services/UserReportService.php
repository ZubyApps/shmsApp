<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationChart;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class UserReportService
{
    public function __construct(
        private readonly Resource $resource, 
        private readonly HelperService $helperService,
        private readonly User $user,
        )
    {
    }

    public function staffActivitiesByDesignation(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'firstname';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->user
                        ->whereRelation('designation', 'designation', '=', $data->designation)
                        ->where('firstname', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middlename', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('lastname', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereRelation('designation', 'access_level', '<', 5)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->user
                    ->whereRelation('designation', 'designation', '=', $data->designation)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getDoctorsTransformer($data): callable
    {
        return  function (User $user) use($data) {
            $current = new CarbonImmutable();

            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return [
                        'id'                    => $user->id,
                        'doctor'                => $user->username,
                        'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'visitsInitiated'       => $user->visits()->whereMonth('created_at', $date->month)->count(),
                        'firstConsultations'    => Visit::where('doctor_id', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'consultations'         => $user->consultations()->whereMonth('created_at', $date->month)->count(),
                        'prescriptions'         => $user->prescriptions()->whereMonth('created_at', $date->month)->count(),
                        'discountinued'         => Prescription::where('discontinued_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'surgeryNotes'          => $user->surgeryNotes()->whereMonth('created_at', $date->month)->count(),
                        'vitalSigns'            => $user->vitalSigns()->whereMonth('created_at', $date->month)->count(),
                        'AncVitalSigns'         => $user->ancVitalSigns()->whereMonth('created_at', $date->month)->count(),
                    ];
            }

            if ($data->startDate && $data->endDate){
                return [
                    'id'                    => $user->id,
                    'doctor'                => $user->username,
                    'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                    'visitsInitiated'       => $user->visits()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'firstConsultations'    => Visit::where('doctor_id', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'consultations'         => $user->consultations()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'prescriptions'         => $user->prescriptions()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'discountinued'         => Prescription::where('discontinued_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'surgeryNotes'          => $user->surgeryNotes()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'vitalSigns'            => $user->vitalSigns()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    'AncVitalSigns'         => $user->ancVitalSigns()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                ];
            }

            return [
                'id'                    => $user->id,
                'doctor'                => $user->username,
                'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                'visitsInitiated'       => $user->visits()->whereMonth('created_at', $current->month)->count(),
                'firstConsultations'    => Visit::where('doctor_id', $user->id)->whereMonth('created_at', $current->month)->count(),
                'consultations'         => $user->consultations()->whereMonth('created_at', $current->month)->count(),
                'prescriptions'         => $user->prescriptions()->whereMonth('created_at', $current->month)->count(),
                'discountinued'         => Prescription::where('discontinued_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'surgeryNotes'          => $user->surgeryNotes()->whereMonth('created_at', $current->month)->count(),
                'vitalSigns'            => $user->vitalSigns()->whereMonth('created_at', $current->month)->count(),
                'AncVitalSigns'         => $user->ancVitalSigns()->whereMonth('created_at', $current->month)->count(),
            ];
        };
    }

    public function getNursesTransformer($data): callable
    {
        return  function (User $user) use($data) {
            $current = new CarbonImmutable();
        
            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return [
                        'id'                    => $user->id,
                        'nurse'                 => $user->username,
                        'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'vitalSigns'            => $user->vitalSigns()->whereMonth('created_at', $date->month)->count(),
                        'AncVitalSigns'         => $user->ancVitalSigns()->whereMonth('created_at', $date->month)->count(),
                        'discountinued'         => Prescription::where('discontinued_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'deiveryNotes'          => $user->deliveryNotes()->whereMonth('created_at', $date->month)->count(),
                        'charted'               => $user->medicationCharts()->whereMonth('created_at', $date->month)->count(),
                        'served'                => MedicationChart::where('given_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'nursingCharts'         => $user->nursingCharts()->whereMonth('created_at', $date->month)->count(),
                        'nursesReports'         => $user->nursesReports()->whereMonth('created_at', $date->month)->count(),
                    ];
            }

            if ($data->startDate && $data->endDate){
                return [
                        'id'                    => $user->id,
                        'nurse'                 => $user->username,
                        'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'vitalSigns'            => $user->vitalSigns()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'AncVitalSigns'         => $user->ancVitalSigns()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'discountinued'         => Prescription::where('discontinued_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'deiveryNotes'          => $user->deliveryNotes()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'charted'               => $user->medicationCharts()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'served'                => MedicationChart::where('given_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'nursingCharts'         => $user->nursingCharts()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'nursesReports'         => $user->nursesReports()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    ];
            }

            return [
                'id'                    => $user->id,
                'nurse'                 => $user->username,
                'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                'vitalSigns'            => $user->vitalSigns()->whereMonth('created_at', $current->month)->count(),
                'AncVitalSigns'         => $user->ancVitalSigns()->whereMonth('created_at', $current->month)->count(),
                'discountinued'         => Prescription::where('discontinued_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'deiveryNotes'          => $user->deliveryNotes()->whereMonth('created_at', $current->month)->count(),
                'charted'               => $user->medicationCharts()->whereMonth('created_at', $current->month)->count(),
                'served'                => MedicationChart::where('given_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'nursingCharts'         => $user->nursingCharts()->whereMonth('created_at', $current->month)->count(),
                'nursesReports'         => $user->nursesReports()->whereMonth('created_at', $current->month)->count(),
            ];
        };
    }

    public function getLabTechsTransformer($data): callable
    {
        return  function (User $user) use($data) {
            $current = new CarbonImmutable();
        
            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return [
                        'id'                => $user->id,
                        'labTech'           => $user->username,
                        'dateOfEmployment'  => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'results'           => Prescription::where('result_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                    ];
            }

            if ($data->startDate && $data->endDate){
                return [
                        'id'                    => $user->id,
                        'labTech'                 => $user->username,
                        'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'results'         => Prescription::where('result_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    ];
            }

            return [
                'id'                    => $user->id,
                'labTech'               => $user->username,
                'dateOfEmployment'      => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                'results'               => Prescription::where('result_by', $user->id)->whereMonth('created_at', $current->month)->count(),
            ];
        };
    }

    public function getPharmacyTechsTransformer($data): callable
    {
        return  function (User $user) use($data) {
            $current = new CarbonImmutable();
        
            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return [
                        'id'                => $user->id,
                        'pharmacyTech'      => $user->username,
                        'dateOfEmployment'  => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'rxBilled'          => Prescription::where('hms_bill_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'rxDispensed'       => Prescription::where('dispensed_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                    ];
            }

            if ($data->startDate && $data->endDate){
                return [
                        'id'              => $user->id,
                        'pharmacyTech'    => $user->username,
                        'dateOfEmployment'=> (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'rxBilled'        => Prescription::where('hms_bill_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'rxDispensed'     => Prescription::where('dispensed_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    ];
            }

            return [
                'id'               => $user->id,
                'pharmacyTech'     => $user->username,
                'dateOfEmployment' => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                'rxBilled'         => Prescription::where('hms_bill_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'rxDispensed'      => Prescription::where('dispensed_by', $user->id)->whereMonth('created_at', $current->month)->count(),
            ];
        };
    }

    public function getHmoOfficerTransformer($data): callable
    {
        return  function (User $user) use($data) {
            $current = new CarbonImmutable();
        
            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return [
                        'id'                => $user->id,
                        'hmoOfficer'        => $user->username,
                        'dateOfEmployment'  => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'visitsInitiated'   => $user->visits()->whereMonth('created_at', $date->month)->count(),
                        'verified'          => Visit::where('verified_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'closedAndOpened'   => Visit::where('closed_opened_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'billsProcessed'    => Visit::where('hmo_done_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'rxHmoBilled'       => Prescription::where('hmo_bill_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'rxApproved'        => Prescription::where('approved_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'rxRejected'        => Prescription::where('rejected_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'rxPaid'            => Prescription::where('paid_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                    ];
            }

            if ($data->startDate && $data->endDate){
                return [
                        'id'              => $user->id,
                        'hmoOfficer'      => $user->username,
                        'dateOfEmployment'=> (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'visitsInitiated' => $user->visits()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'verified'        => Visit::where('verified_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'closedAndOpened' => Visit::where('closed_opened_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'billsProcessed'  => Visit::where('hmo_done_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'rxHmoBilled'     => Prescription::where('hmo_bill_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'rxApproved'      => Prescription::where('approved_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'rxRejected'      => Prescription::where('rejected_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'rxPaid'          => Prescription::where('paid_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                    ];
            }

            return [
                'id'                => $user->id,
                'hmoOfficer'        => $user->username,
                'dateOfEmployment'  => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                'verified'          => Visit::where('verified_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'visitsInitiated'   => $user->visits()->whereMonth('created_at', $current->month)->count(),
                'closedAndOpened'   => Visit::where('closed_opened_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'billsProcessed'    => Visit::where('hmo_done_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'rxHmoBilled'       => Prescription::where('hmo_bill_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'rxApproved'        => Prescription::where('approved_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'rxRejected'        => Prescription::where('rejected_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'rxPaid'            => Prescription::where('paid_by', $user->id)->whereMonth('created_at', $current->month)->count(),
            ];
        };
    }

    public function getBillOfficerTransformer($data): callable
    {
        return  function (User $user) use($data) {
            $current = new CarbonImmutable();
        
            if ($data->date){
                $date = new CarbonImmutable($data->date);
                return [
                        'id'                => $user->id,
                        'billOfficer'       => $user->username,
                        'dateOfEmployment'  => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'visitsInitiated'   => $user->visits()->whereMonth('created_at', $date->month)->count(),
                        'closedAndOpened'   => Visit::where('closed_opened_by', $user->id)->whereMonth('created_at', $date->month)->count(),
                        'thirdPartyServices' => $user->thirdPartyServies()->whereMonth('created_at', $date->month)->count(),
                        'payments'          => $user->payments()->whereMonth('created_at', $date->month)->count(),
                        'paymentsTotal'     => $user->payments()->whereMonth('created_at', $date->month)->sum('amount_paid'),
                    ];
            }

            if ($data->startDate && $data->endDate){
                return [
                        'id'              => $user->id,
                        'billOfficer'      => $user->username,
                        'dateOfEmployment'=> (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                        'visitsInitiated' => $user->visits()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'closedAndOpened' => Visit::where('closed_opened_by', $user->id)->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'thirdPartyServices' => $user->thirdPartyServies()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'payments'        => $user->payments()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->count(),
                        'paymentsTotal'   => $user->payments()->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])->sum('amount_paid'),
                    ];
            }

            return [
                'id'                => $user->id,
                'billOfficer'        => $user->username,
                'dateOfEmployment'  => (new Carbon($user->date_of_employment))->format('d/M/y g:ia'),
                'visitsInitiated'   => $user->visits()->whereMonth('created_at', $current->month)->count(),
                'closedAndOpened'   => Visit::where('closed_opened_by', $user->id)->whereMonth('created_at', $current->month)->count(),
                'thirdPartyServices' => $user->thirdPartyServies()->whereMonth('created_at', $current->month)->count(),
                'payments'          => $user->payments()->whereMonth('created_at', $current->month)->count(),
                'paymentsTotal'     => $user->payments()->whereMonth('created_at', $current->month)->sum('amount_paid'),
            ];
        };
    }
}
