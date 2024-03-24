@extends('layout')


@section('content')
@vite(['resources/js/medReports.js'])

@include('reports.modals.byResourceModal', ['title' => 'Patients', 'id' => 'byResourceModal'])

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
                <button class="nav-link"  id="nav-referrals-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-referrals" type="button" role="tab" aria-controls="nav-referrals"
                    aria-selected="false">Referrals</button>
                <button class="nav-link"  id="nav-deceased-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-deceased" type="button" role="tab" aria-controls="nav-deceased"
                    aria-selected="false">Deceased</button>
                {{-- <button class="nav-link"   id="nav-summaries-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summaries" type="button" role="tab" aria-controls="nav-summaries"
                    aria-selected="false">Summaries</button> --}}
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
                    <table  id="newBirthsTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mode of delivery</th>
                                <th>Female</th>
                                <th>Male</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- frequency table -->
            <div class="tab-pane fade" id="nav-referrals" role="tabpanel" aria-labelledby="nav-referrals-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Referred Patients</h5>
                    <table id="referralsTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th> <i class="bi bi-telephone-outbound-fill text-primary"></i></th>
                                <th>Sponsor</th>
                                <th>Diagnosis</th>
                                <th>HMS Bill</th>
                                <th>HMO Bill</th>
                                <th>NHIS Bill</th>
                                <th>Total Paid</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- Visit Details table -->
            <div class="tab-pane fade" id="nav-deceased" role="tabpanel" aria-labelledby="nav-deceased-tab" tabindex="0">
                <h5 class="card-title py-4">Deceased Patients</h5>
                <x-form-div class="col-xl-6 py-3 datesDiv">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchRegWithDatesBtn">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table  id="deceasedTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th>Sponsor</th>
                                <th>Diagnosis</th>
                                <th>HMS Bill</th>
                                <th>HMO Bill</th>
                                <th>NHIS Bill</th>
                                <th>Total Paid</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- summaries tables -->
            {{-- <div class="tab-pane fade" id="nav-summaries" role="tabpanel" aria-labelledby="nav-summaries-tab" tabindex="0">
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Sex</h5>
                                <table  id="sexAggregateTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Total</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Age and Sex</h5>
                                <table  id="ageAggregateTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>0-3m</th>
                                            <th>3-12m</th>
                                            <th>1-5y</th>
                                            <th>5-13y</th>
                                            <th>13-18y</th>
                                            <th>18-48y</th>
                                            <th>48-63y</th>
                                            <th>63</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Totals</td>
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
                        </div>
                      </div>
                </div>
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">New Registrations by Sponsor This Month</h5>
                                <table  id="totalPatientsTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sponsor</th>
                                            <th>Count</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Total</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="card">
                            <div class="card-body">
                            <h5 class="card-title">Visits Summary by Sponsor This Month</h5>
                            <table  id="visitsSummaryTable" class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Sponsor</th>
                                        <th>Outpatients</th>
                                        <th>Inpatients</th>
                                        <th>Observations</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td class="text-center">Total</td>
                                        <td></td>
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
            </div> --}}
        </div>
    </div>
    
</div>


@endsection