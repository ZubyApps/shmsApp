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
                                    <x-form-input name="sponsor" value="" id="sponsor" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sponsor Category</x-input-span>
                                    <x-form-input name="sponsorCategory" value="" id="sponsorCategory" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Dates</x-input-span>
                                    <x-input-span class="">From</x-input-span>
                                    <x-form-input type="date" name="from" id="from" readonly/>
                                    <x-input-span class="">To</x-input-span>
                                    <x-form-input type="date" name="to" id="to" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span class="">Month/Year</x-input-span>
                                    <x-form-input type="month" name="visitMonth" id="visitMonth" />
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>Visits Under this Sponsor</x-form-span>
                            <div class="row overflow-auto my-3">
                                <table id="visitsBySponsorTable" class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th class="resetSorting">Seen</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Current Diagnosis</th>
                                            <th>HMS Bill</th>
                                            <th>HMO Bill</th>
                                            <th>NHIS Bill</th>
                                            <th>Paid</th>
                                            <th>Diff</th>
                                            <th class="sortByreviewed">Reviewed</th>
                                            <th class="sortByresolved">Resolved</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr class="">
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold">Total</td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                            <td class="fw-semibold"></td>
                                        </tr>
                                    </tfoot>
                                </table>
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
