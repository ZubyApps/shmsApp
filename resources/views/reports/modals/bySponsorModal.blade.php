<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input name="sposnor" value="" id="sponsor" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Category</x-input-span>
                                    <x-form-input name="category" value="" id="category" readonly/>
                                </x-form-div>
                                @if ($isByMonth)
                                    <x-form-div class="col-xl-6">
                                        <x-input-span>Dates</x-input-span>
                                        <x-input-span class="">From</x-input-span>
                                        <x-form-input type="date" name="from" id="from" readonly/>
                                        <x-input-span class="">To</x-input-span>
                                        <x-form-input type="date" name="to" id="to" readonly/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span class="">Month/Year</x-input-span>
                                        <x-form-input type="month" name="patientMonth" id="patientMonth" />
                                    </x-form-div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>All Patients By Sponsor</x-form-span>
                            <div class="row overflow-auto my-3">
                                <fieldset id="patientsBySponsorFieldset">
                                    <table id="{{ $isByMonth ? 'byMonthBySponsorTable' :  'bySponsorTable'}}" class="table align-middle table-sm bySponsorTable mt-2">
                                        <thead>
                                            <tr>
                                                <th>Patient</th>
                                                <th>Phone</th>
                                                <th>Sex</th>
                                                <th>Age</th>
                                                <th>Total Visits</th>
                                                <th>HMS Bill</th>
                                                <th>HMO Bill</th>
                                                <th>NHIS Bill</th>
                                                <th>Total Paid</th>
                                                <th>Outstanding</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </fieldset>
                            </div>  
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
