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
                                    <x-input-span>Sponsor Category</x-input-span>
                                    <x-form-input name="sponsorCategory" value="" id="sponsorCategory" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Visits</x-input-span>
                                    <x-input-span class="">From</x-input-span>
                                    <x-form-input type="date" name="from" id="from" readonly/>
                                    <x-input-span class="">To</x-input-span>
                                    <x-form-input type="date" name="to" id="to" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span class="">Month/Year</x-input-span>
                                    <x-form-input type="month" name="visitMonth" id="visitMonth" readonly/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>All Visit and Prescriptions under this Sponsor</x-form-span>
                            <div class="row overflow-auto my-3">
                                <fieldset id="reconciliationFieldset">
                                    <table id="reconciliationTable" class="table align-middle table-sm reconciliationTable mt-2">
                                        <thead>
                                            <tr>
                                                <th>Came</th>
                                                <th>Patient</th>
                                                <th>Doctor</th>
                                                <th>Diagnosis</th>
                                                <th>Total Bill Sent</th>
                                                <th>Total Hms Bill</th>
                                                <th>Total Nhis Bill</th>
                                                <th>Total Capitation</th>
                                                <th>Total Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
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
