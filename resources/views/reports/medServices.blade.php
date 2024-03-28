@extends('layout')


@section('content')
@vite(['resources/js/medReports.js'])

@include('reports.modals.byResourceModal', ['title' => 'Patients', 'id' => 'byResourceModal'])
@include('reports.modals.visitsByDischargeModal', ['title' => "Patient's Visits", 'id' => 'visitsByDischargeModal'])

<div class="container mt-5">
    @include('reports.reportGrid')
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-summary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summary" type="button" role="tab" aria-controls="nav-summary"
                    aria-selected="true">Summary</button>
                <button class="nav-link"  id="nav-newBirths-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-newBirths" type="button" role="tab" aria-controls="nav-newBirths"
                    aria-selected="true">New Births</button>
                <button class="nav-link"  id="nav-referred-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-referred" type="button" role="tab" aria-controls="nav-referred"
                    aria-selected="false">Referred</button>
                <button class="nav-link"  id="nav-deceased-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-deceased" type="button" role="tab" aria-controls="nav-deceased"
                    aria-selected="false">Deceased</button>
                <button class="nav-link"   id="nav-dischargeSummary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-dischargeSummary" type="button" role="tab" aria-controls="nav-dischargeSummary"
                    aria-selected="false">Discharge Summary</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Medical Services Suammry table -->
            <div class="tab-pane fade show active" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Summary of Medical Services</h5>
                    <x-form-div class="col-xl-8 py-3 datesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="expenseMonth" id="medServiceMonth" />
                        <button class="input-group-text searchMedServiceByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="summaryTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SubCategory</th>
                                <th>Times Prescribed</th>
                                <th>Qty Prescribed</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- New Births Summary 2 table -->
            <div class="tab-pane fade" id="nav-newBirths" role="tabpanel" aria-labelledby="nav-newBirths-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">New Births</h5>
                    <x-form-div class="col-xl-8 py-3 newBirthsDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchNewBirthsWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="newBirthsMonth" id="newBirthsMonth" />
                        <button class="input-group-text searchNewBirthsByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="newBirthsTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time of delivery</th>
                                <th>Mode of delivery</th>
                                <th>Mother</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Note By</th>
                                <th>Sex</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- frequency table -->
            <div class="tab-pane fade" id="nav-referred" role="tabpanel" aria-labelledby="nav-referred-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Referred Patients</h5>
                    <x-form-div class="col-xl-8 py-3 referredDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchReferredWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="newBirthsMonth" id="referredMonth" />
                        <button class="input-group-text searchReferredByMonthBtn">Search</button>
                    </x-form-div>
                    <table id="referredTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th> <i class="bi bi-telephone-outbound-fill text-primary"></i></th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>HMS Bill</th>
                                <th>Paid</th>
                                <th>Diff</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Visit Details table -->
            <div class="tab-pane fade" id="nav-deceased" role="tabpanel" aria-labelledby="nav-deceased-tab" tabindex="0">
                <h5 class="card-title py-4">Deceased Patients</h5>
                <x-form-div class="col-xl-8 py-3 deceasedDatesDiv">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchDeceasedWithDatesBtn">Search</button>
                    <x-input-span class="">OR</x-input-span>
                    <x-input-span class="">Month/Year</x-input-span>
                    <x-form-input type="month" name="newBirthsMonth" id="deceasedMonth" />
                    <button class="input-group-text searchDeceasedByMonthBtn">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table id="deceasedTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th> <i class="bi bi-telephone-outbound-fill text-primary"></i></th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>HMS Bill</th>
                                <th>Paid</th>
                                <th>Diff</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- discharge summaries tables -->
            <div class="tab-pane fade" id="nav-dischargeSummary" role="tabpanel" aria-labelledby="nav-dischargeSummary-tab" tabindex="0">
                <h5 class="card-title py-4">Discharge Summary</h5>
                <x-form-div class="col-xl-8 py-3 dischargeSummaryDatesDiv">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchDischargeSummaryWithDatesBtn">Search</button>
                    <x-input-span class="">OR</x-input-span>
                    <x-input-span class="">Month/Year</x-input-span>
                    <x-form-input type="month" name="dischargeSummaryMonth" id="dischargeSummaryMonth" />
                    <button class="input-group-text searchDischargeSummaryByMonthBtn">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table  id="dischargeSummaryTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Reason</th>
                                <th>Sponsors</th>
                                <th>Patients</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection