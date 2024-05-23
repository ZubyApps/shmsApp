<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\MedicationChart;
use App\Models\Prescription;
use App\Models\ShiftPerformance;
use Carbon\CarbonImmutable;

Class ShiftPerformanceService
{
    public function __construct(
        private readonly ShiftPerformance $shiftPerformance,
        private readonly Prescription $prescription,
        private readonly MedicationChart $medicationChart
        )
    {
        
    }

    public function create($department, $shift): ShiftPerformance
    {
       $shiftPerformance = $this->shiftPerformance->create([
            'department'   => $department,
            'shift'        => $shift,
        ]);

        return $shiftPerformance;
    }

    public function update($data)
    {
        $shiftPerformance = $this->shiftPerformance->where('department', $$data->department)->where('is_closed', false)->latest();

        $shiftPerformance->update([
                'chart_rate'                => $this->chartRate($shiftPerformance),
                'first_med_res'             => '',
                'first_vitals_res'          => '',
                'medication_time'           => '',
                'inpatient_vitals_count'    => '',
                'outpatient_vitals_count'   => '',
            ]);

            $shiftPerformance->update([
                'performance'  => $this->getPerformance($shiftPerformance),
            ]);

        return $shiftPerformance->performance;
    }

    public function chartRate(ShiftPerformance $shiftPerformance)
    {
        $date = CarbonImmutable::now();
        $morningShift   = ['start' => $date->date.' 08:00:01', 'end' => $date->date.' 14:00:00'];
        $afternoonShift = ['start' => $date->date.' 14:00:01', 'end' => $date->date.' 20:00:00'];
        $nightShift     = ['start' => $date->date.' 20:00:01', 'end' => $date->date.' 08:00:00'];

        if ($shiftPerformance->shift == 'Morning Shift'){
            $totalPrescriptions         = $this->prescription->whereBetween('created_at', [$morningShift['start'], $morningShift['end']])->count();
            $totalPrescriptionsCharted  = $this->prescription->prescriptionsChartedPerShift($morningShift, 'medicationCharts');

            return $totalPrescriptions ? ($totalPrescriptionsCharted/$totalPrescriptions) * 100 : 0; 
        }

        if ($shiftPerformance->shift == 'Afternoon Shift'){
            $totalPrescriptions         = $this->prescription->whereBetween('created_at', [$afternoonShift['start'], $afternoonShift['end']])->count();
            $totalPrescriptionsCharted  = $this->prescription->prescriptionsChartedPerShift($afternoonShift, 'medicationCharts');

            return $totalPrescriptions ? ($totalPrescriptionsCharted/$totalPrescriptions) * 100 : 0; 
        }

        if ($shiftPerformance->shift == 'Night Shift'){
            $totalPrescriptions         = $this->prescription->whereBetween('created_at', [$nightShift['start'], $nightShift['end']])->count();
            $totalPrescriptionsCharted  = $this->prescription->prescriptionsChartedPerShift($nightShift, 'medicationCharts');

            return $totalPrescriptions ? ($totalPrescriptionsCharted/$totalPrescriptions) * 100 : 0; 
        }
    }

    public function getPerformance($shiftPerformance)
    {
        $convertChartRate               = ($shiftPerformance->chartRate/100) * 20;
        $convertFirstMedRes             = ($shiftPerformance->first_med_res/100) * 20;
        $convertFirstVitalsRes          = ($shiftPerformance->first_vitals_res/100) * 20;
        $convertMedicationTime          = ($shiftPerformance->medication_time/100) * 20;
        $convertInpatientsVitalsCount   = ($shiftPerformance->inpatient_vitals_count/100) * 20;
        $convertOutPatientsVitalsCount  = ($shiftPerformance->outpatient_vitals_count/100) * 20;

        $preformance = ($convertChartRate + $convertFirstMedRes + $convertFirstVitalsRes + $convertMedicationTime + $convertInpatientsVitalsCount + $convertOutPatientsVitalsCount)/120 * 100;
        return $preformance;
    }

    public function getShiftPerformance($department)
    {
        return $this->shiftPerformance
                    ->where('department', $department)
                    ->where('is_closed', false)
                    ->get()->first()?->performance;

    }
}