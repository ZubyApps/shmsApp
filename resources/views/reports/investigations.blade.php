@extends('layout')


@section('content')
@vite(['resources/js/investigationReports.js'])

@include('reports.modals.byResourceModal', ['title' => 'Patients', 'id' => 'byResourceModal'])

<div class="container mt-5">
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-summary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summary" type="button" role="tab" aria-controls="nav-summary"
                    aria-selected="true">Summary</button>
                {{-- <button class="nav-link"  id="nav-distribution2-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-distribution2" type="button" role="tab" aria-controls="nav-distribution2"
                    aria-selected="true">Distribution 2</button>
                <button class="nav-link"  id="nav-frequency-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-frequency" type="button" role="tab" aria-controls="nav-frequency"
                    aria-selected="false">Frequency</button>
                <button class="nav-link"  id="nav-registrations-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-registrations" type="button" role="tab" aria-controls="nav-registrations"
                    aria-selected="false">Registrations</button> --}}
                {{-- <button class="nav-link"   id="nav-summaries-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summaries" type="button" role="tab" aria-controls="nav-summaries"
                    aria-selected="false">Summaries</button> --}}
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Distribution 1 table -->
            <div class="tab-pane fade show active" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Summary of Investigations</h5>
                    <x-form-div class="col-xl-6 py-3 datesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchWithDatesBtn">Serach</button>
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
            <!-- Distribution 2 table -->
            {{-- <div class="tab-pane fade" id="nav-distribution2" role="tabpanel" aria-labelledby="nav-distribution2-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Patient Distribution by Sponsor</h5>
                    <table  id="distribution2Table" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor</th>
                                <th>Female</th>
                                <th>Male</th>
                                <th>Total</th>
                                <th>Category</th>
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
            <div class="tab-pane fade" id="nav-frequency" role="tabpanel" aria-labelledby="nav-frequency-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Patients by highest Paid/Visit</h5>
                    <table id="frequencyTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>age</th>
                                <th> <i class="bi bi-telephone-outbound-fill text-primary"></i></th>
                                <th>Sponsor</th>
                                <th>Category</th>
                                <th>Visits</th>
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
            <div class="tab-pane fade" id="nav-registrations" role="tabpanel" aria-labelledby="nav-registrations-tab" tabindex="0">
                <h5 class="card-title py-4">Registration Summary by Dates <small>(Default: This Month)</small></h5>
                <x-form-div class="col-xl-6 py-3 datesDiv">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchRegWithDatesBtn">Serach</button>
                </x-form-div>
                <div class="py-2 ">
                    <table  id="registerationsTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor</th>
                                <th>Category</th>
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
            </div> --}}
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