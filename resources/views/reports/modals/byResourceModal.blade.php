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
                                    <x-input-span>Resource</x-input-span>
                                    <x-form-input name="resource" value="" id="resource" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sub Category</x-input-span>
                                    <x-form-input name="subcategory" value="" id="subcategory" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Dates</x-input-span>
                                    <x-input-span class="">From</x-input-span>
                                    <x-form-input type="date" name="from" id="from" readonly/>
                                    <x-input-span class="">To</x-input-span>
                                    <x-form-input type="date" name="to" id="to" readonly/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>Patients prescribed for</x-form-span>
                            <div class="row overflow-auto my-3">
                                <fieldset id="patientsBySponsorFieldset">
                                    <table id="byResourceTable" class="table align-middle table-sm byResourceTable mt-2">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Patient</th>
                                                <th>Sex</th>
                                                <th>Age</th>
                                                <th>Sponsor</th>
                                                <th>Category</th>
                                                <th>Diagnosis</th>
                                                <th>Doctor</th>
                                                <th>HmsBill</th>
                                                <th>HmoBill</th>
                                                <th>Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="fw-bold">
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
