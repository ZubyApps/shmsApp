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
                                    <x-input-span>PayMethod</x-input-span>
                                    <x-form-input name="paymethod" value="" id="paymethod" readonly/>
                                </x-form-div>
                                @if ($isManagement)
                                    <x-form-div class="col-xl-6">
                                        <x-input-span>Dates</x-input-span>
                                        <x-input-span class="">From</x-input-span>
                                        <x-form-input type="date" name="from" id="from" readonly/>
                                        <x-input-span class="">To</x-input-span>
                                        <x-form-input type="date" name="to" id="to" readonly/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span class="">Month/Year</x-input-span>
                                        <x-form-input type="month" name="payMethodMonth" id="payMethodMonth" readonly/>
                                    </x-form-div>
                                @else
                                    <x-form-div class="col-xl-6">
                                        <x-input-span>Date</x-input-span>
                                        <x-form-input type="date" name="showBalanceDate" id="showBalanceDate" readonly/>
                                    </x-form-div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>Payments Made with this Paymethod</x-form-span>
                            <div class="row overflow-auto my-3">
                                <table id="expensesByPayMethodTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Desription</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Given to</th>
                                    <th>Given By</th>
                                    <th>Approved By</th>
                                    <th>Paymethod</th>
                                    <th>Comment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="">
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold">Total</td>
                                    <td class="fw-semibold"></td>
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
