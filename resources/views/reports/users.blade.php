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
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Summary table -->
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
                                <th>Initiated Visits</th>
                                <th>Initial Visits</th>
                                <th>Consultations</th>
                                <th>Prescriptions</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection