@extends('layout')


@section('content')
@vite(['resources/js/usersReports.js'])

@include('reports.modals.byResourceModal', ['title' => 'Patients', 'id' => 'byResourceModal'])

<div class="container mt-5">
    @include('reports.reportGrid')
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-doctorsActivity-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-doctorsActivity" type="button" role="tab" aria-controls="nav-doctorsActivity"
                    aria-selected="true">Doctors</button>
                <button class="nav-link"  id="nav-nursesActivity-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-nursesActivity" type="button" role="tab" aria-controls="nav-nursesActivity"
                    aria-selected="true">Nurses</button>
                <button class="nav-link"  id="nav-labTechActivity-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-labTechActivity" type="button" role="tab" aria-controls="nav-labTechActivity"
                    aria-selected="true">Lab Techs</button>
                <button class="nav-link"  id="nav-pharmacyTechsActivity-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-pharmacyTechsActivity" type="button" role="tab" aria-controls="nav-pharmacyTechsActivity"
                    aria-selected="true">Pharmacy Techs</button>
                <button class="nav-link"  id="nav-hmoOfficersActivity-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-hmoOfficersActivity" type="button" role="tab" aria-controls="nav-hmoOfficersActivity"
                    aria-selected="true">HMO Officers</button>
                <button class="nav-link"  id="nav-billOfficersActivity-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-billOfficersActivity" type="button" role="tab" aria-controls="nav-billOfficersActivity"
                    aria-selected="true">Bill Officers</button>
                <button class="nav-link"  id="nav-nursesShiftPerfomance-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-nursesShiftPerfomance" type="button" role="tab" aria-controls="nav-nursesShiftPerfomance"
                    aria-selected="true">Nurses Perfomance</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Doctors table -->
            <div class="tab-pane fade show active" id="nav-doctorsActivity" role="tabpanel" aria-labelledby="nav-doctorsActivity-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Doctors Activity</h5>
                    <x-form-div class="col-xl-8 py-3 doctorsDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchDoctorsWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="doctorsActivityMonth" id="doctorsActivityMonth" />
                        <button class="input-group-text searchDoctorsByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="doctorsActivityTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Employed</th>
                                <th>Visits Initiated</th>
                                <th>Initial Visits</th>
                                <th>Consultations</th>
                                <th>Rx </th>
                                <th>Rx D/C</th>
                                <th>Surgeries</th>
                                <th>Vitalsigns</th>
                                <th>Anc Vitalsigns</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Nurses table -->
            <div class="tab-pane fade" id="nav-nursesActivity" role="tabpanel" aria-labelledby="nav-nursesActivity-tab" tabindex="0">
                <div class="">
                    <h5 class="card-title py-4">Nurses Activity</h5>
                    <x-form-div class="col-xl-8 py-3 nursesDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchNursesWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="nursesActivityMonth" id="nursesActivityMonth" />
                        <button class="input-group-text searchNursesByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="nursesActivityTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nurse</th>
                                <th>Employed</th>
                                <th>Vitalsigns</th>
                                <th>ANC Vitalsigns</th>
                                <th>Rx</th>
                                <th>Rx D/C</th>
                                <th>Delivery</th>
                                <th>Med Charted</th>
                                <th>Med Served</th>
                                <th>Other Charts</th>
                                <th>Reports</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Lab Tech table -->
            <div class="tab-pane fade" id="nav-labTechActivity" role="tabpanel" aria-labelledby="nav-labTechActivity-tab" tabindex="0">
                <div class="">
                    <h5 class="card-title py-4">Lab Techs Activity</h5>
                    <x-form-div class="col-xl-8 py-3 labTechDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchLabTechWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="labtTechActivityMonth" id="labTechActivityMonth" />
                        <button class="input-group-text searchLabTechByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="labTechActivityTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Lab Staff/Technician</th>
                                <th>Employed</th>
                                <th>Results Recorded</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Pharmacy table -->
            <div class="tab-pane fade" id="nav-pharmacyTechsActivity" role="tabpanel" aria-labelledby="nav-pharmacyTechsActivity-tab" tabindex="0">
                <div class="">
                    <h5 class="card-title py-4">Pharmacy Techs Activity</h5>
                    <x-form-div class="col-xl-8 py-3 pharmacyTechsDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchPharmacyTechsWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="pharmacyTechsActivityMonth" id="pharmacyTechsActivityMonth" />
                        <button class="input-group-text searchPharmacyTechsByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="pharmacyTechsActivityTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pharmacy Staff/Tech</th>
                                <th>Employed</th>
                                <th>Rx Billed</th>
                                <th>Rx Dispensed</th>
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
            <!-- HMO table -->
            <div class="tab-pane fade" id="nav-hmoOfficersActivity" role="tabpanel" aria-labelledby="nav-hmoOfficersActivity-tab" tabindex="0">
                <div class="">
                    <h5 class="card-title py-4">HMO Officers Activity</h5>
                    <x-form-div class="col-xl-8 py-3 hmoOfficersDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchHmoOfficersWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="hmoOfficersActivityMonth" id="hmoOfficersActivityMonth" />
                        <button class="input-group-text searchHmoOfficersByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="hmoOfficersActivityTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>HMO Officer</th>
                                <th>Employed</th>
                                <th>Patients</th>
                                <th>Visits Initiated</th>
                                <th>Visits Verified</th>
                                <th>Visits Treated</th>
                                <th>Visits closed</th>
                                <th>Bills Processed</th>
                                <th>Hmo Bills</th>
                                <th>Rx Approved</th>
                                <th>Rx Rejected</th>
                                <th>Rx Paid</th>
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
            <!-- Billing table -->
            <div class="tab-pane fade" id="nav-billOfficersActivity" role="tabpanel" aria-labelledby="nav-billOfficersActivity-tab" tabindex="0">
                <div class="">
                    <h5 class="card-title py-4">Bill Officers Activity</h5>
                    <x-form-div class="col-xl-8 py-3 billOfficersDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchBillOfficersWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="billOffersActivityMonth" id="billOffersActivityMonth" />
                        <button class="input-group-text searchBillOfficersByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="billOfficersActivityTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Bill Officer</th>
                                <th>Employed</th>
                                <th>Patients</th>
                                <th>Visits Initiated</th>
                                <th>Visits closed</th>
                                <th>Third Party Services</th>
                                <th>Payments</th>
                                <th>Total Payments</th>
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
            <!-- Nurses Shift Perfomance table -->
            <div class="tab-pane fade" id="nav-nursesShiftPerfomance" role="tabpanel" aria-labelledby="nav-nursesShiftPerfomance-tab" tabindex="0">
                <div class="">
                    <h5 class="card-title py-4">Nurses Shift Perfomance</h5>
                    {{-- <x-form-div class="col-xl-8 py-3 billOfficersDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchBillOfficersWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="billOffersActivityMonth" id="billOffersActivityMonth" />
                        <button class="input-group-text searchBillOfficersByMonthBtn">Search</button>
                    </x-form-div> --}}
                    <table  id="nursesShiftPerfomanceTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Shift</th>
                                <th>Duration</th>
                                <th>Chart Rate</th>
                                <th>Given Rate</th>
                                <th>1st Med Avg</th>
                                <th>1st Vitals Avg</th>
                                <th>Med Time</th>
                                <th>Intpatient Vitals Count</th>
                                <th>Outpatient Vitals Count</th>
                                <th>Nurses</th>
                                <th>Performance</th>
                                <th>Bonus</th>
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
        </div>
    </div>
</div>


@endsection