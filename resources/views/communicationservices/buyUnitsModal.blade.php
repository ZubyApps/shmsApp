<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="mb-2 form-control">
                        <x-form-label>Buy Units</x-form-label>
                        <div class="form-control">
                            <div class="row">
                                <div class="col-xl-6 buyDiv">
                                    <div class="form-control">
                                        <x-form-div class="col-xl-12">
                                            <x-input-span>Amount (NGN)</x-input-span>
                                            <x-form-input name="amount" type="number" id="amount" placeholder="Enter amount"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span>Units</x-input-span>
                                            <x-form-input name="units"  id="units" readonly/>
                                        </x-form-div>
                                        <div class="p-3 mb-3 bg-body-tertiary rounded"> 
                                            <h5 class="fst-italic">Note</h5> 
                                            <p class="mb-0">For your application to continue delivering patient notifications, you have to have "units" in your wallet. Units are currently priced at ₦2 per unit. At this time, wallet funding is handled manually; please transfer to the designated account and provide proof of payment via call or chat to the number provided. Pls note that SMSes won't be sent between 7:00PM - 8:00AM due to NCC restrictions</p> 
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            <button type="button" id="buyBtn"
                                                class="btn btn-primary">
                                                Buy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-control">
                                        <div class="d-flex flex-column flex-md-row p-3 gap-3 py-md-2 m-0 align-items-center justify-content-center">
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                                    <i class="bi bi-person-fill-check text-primary"></i>
                                                    <div class="d-flex gap-2 w-100 justify-content-between">
                                                        <div>
                                                        <h6 class="mb-0">Account Name</h6>
                                                        <p class="mb-0 opacity-75">Nzube Okoye</p>
                                                        </div>
                                                        <small class="opacity-50 text-nowrap">Company: ZubyApps</small>
                                                    </div>
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                                    <i class="bi bi-menu-app-fill text-primary"></i>
                                                    <div class="d-flex gap-2 w-100 justify-content-between">
                                                        <div>
                                                        <h6 class="mb-0">Account Number</h6>
                                                        <p class="mb-0 opacity-75">2084023878</p>
                                                        </div>
                                                        <small class="opacity-50 text-nowrap">10 digits</small>
                                                    </div>
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                                    <i class="bi bi-bank text-primary"></i>
                                                    <div class="d-flex gap-2 w-100 justify-content-between">
                                                        <div>
                                                        <h6 class="mb-0">Account Bank</h6>
                                                        <p class="mb-0 opacity-75">KUDA Microfinance Bank</p>
                                                        </div>
                                                        <small class="opacity-50 text-nowrap">KUDA</small>
                                                    </div>
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                                    <i class="bi bi-bank text-primary"></i>
                                                    <div class="d-flex gap-2 w-100 justify-content-between">
                                                        <div>
                                                        <h6 class="mb-0">Send proof of payment</h6>
                                                        <p class="mb-0 opacity-75">08035999029</p>
                                                        <p class="mb-0 opacity-75">08103830241</p>
                                                        <p class="mb-0 opacity-75">zubyokoye@gmail.com</p>
                                                        </div>
                                                        <small class="opacity-50 text-nowrap">WhatsApp, Telegram, Gmail</small>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 form-control overflow-auto">
                        <X-form-div class="py-4">
                            <table id="walletFundingTable" class="table table-hover table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Units</th>
                                        <th>Pay Method</th>
                                        <th>By</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr class="">
                                    <td class="fw-semibold">Total</td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                </tr>
                            </tfoot>
                            </table>
                        </X-form-div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
