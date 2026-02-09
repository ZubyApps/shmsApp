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
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Patient</x-input-span>
                                    <x-form-input name="patient" value="" id="patient" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input name="sponsor" value="" id="sponsor" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-4 staffIdDiv">
                                    <x-input-span>Staff ID/No.</x-input-span>
                                    <x-form-input name="staffId" class="staffId" id="staffId" readonly />
                                </x-form-div>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Age</x-input-span>
                                    <x-form-input name="age" class="age" id="age" readonly />
                                </x-form-div>
                                <x-form-div class="">
                                    <x-input-span>Sex</x-input-span>
                                    <x-form-input name="sex" class="" id="sex" readonly />
                                </x-form-div>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Phone Number</x-input-span>
                                    <x-form-input type="tel" name="phone" id="phone" value="" readonly />
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control vitalsDiv">
                            <x-form-span>All Prescriptions for this Visit</x-form-span>
                            <div class="row overflow-auto my-3">
                                <fieldset id="makeBillFieldset">
                                    <table id="visitPrescriptionsTable" class="table table-hover align-middle table-sm visitPrescriptionsTable">
                                        <thead>
                                            <tr>
                                                <th>By</th>
                                                <th>Prescribed</th>
                                                <th>Item</th>
                                                <th>Diagnosis</th>
                                                <th>Prescription</th>
                                                <th>Note</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th class="text-center">HMS Bill</th>
                                                <th class="text-center">HMO Bill</th>
                                                <th>Bill By</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr class="">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="">Total</td>
                                                <td class="text-primary text-center"></td>
                                                <td class="text-primary text-center"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="">Paid HMS</td>
                                                <td class="text-success text-center"></td>
                                                <td class="text-success  text-center"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="">Balance</td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
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
                    <button type="button" id="markAsSentBtn" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        Mark as Sent
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
