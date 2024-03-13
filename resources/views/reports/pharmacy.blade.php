@extends('layout')


@section('content')
@vite(['resources/js/pharmacyReports.js'])

@include('reports.modals.byResourceModal', ['title' => 'Patients', 'id' => 'byResourceModal'])

<div class="container mt-5">
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-summary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summary" type="button" role="tab" aria-controls="nav-summary"
                    aria-selected="true">Summary</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Summary table -->
            <div class="tab-pane fade show active" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Summary of Pharmacy Resources</h5>
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
                                <th>Qty Billed</th>
                                <th>Qty Dispensed</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection