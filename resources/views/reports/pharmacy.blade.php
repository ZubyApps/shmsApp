@extends('layout')


@section('content')
@vite(['resources/js/pharmacyReports.js'])

@include('reports.modals.byResourcePharmacyModal', ['title' => 'Patients', 'id' => 'byResourcePharmacyModal'])

<div class="container mt-5">
    @include('reports.reportGrid')
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-summary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summary" type="button" role="tab" aria-controls="nav-summary"
                    aria-selected="true">Summary</button>

                <button class="nav-link"  id="nav-missing-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-missing" type="button" role="tab" aria-controls="nav-missing"
                    aria-selected="true">Missing</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Summary table -->
            <div class="tab-pane fade show active" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Summary of Pharmacy Resources</h5>
                    <x-form-div class="col-xl-8 py-3 datesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="pharmacyMonth" id="pharmacyMonth" />
                        <button class="input-group-text searchMedicationByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="summaryTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SubCategory</th>
                                <th>Times Prescribed</th>
                                <th>Qty Billed</th>
                                <th>Qty Dispensed</th>
                                <th>Bulk Dispensed</th>
                                <th>Total Dispensed</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Missing table -->
            <div class="tab-pane fade" id="nav-missing" role="tabpanel" aria-labelledby="nav-missing-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Summary of Pharmacy Resources</h5>
                    <x-form-div class="col-xl-8 py-3 missingDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchMissingWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="missingMonth" id="missingMonth" />
                        <button class="input-group-text searchMissingByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="missingTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SubCategory</th>
                                <th>Add Count</th>
                                <th>Total Qty</th>
                                <th>Total Final Qty</th>
                                <th>Missing</th>
                                <th>Missing Cost Value</th>
                                <th>Missing Sell Value</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection