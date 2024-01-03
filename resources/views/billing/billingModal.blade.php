<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="mb-2 form-control">
                        <x-form-label>Patient's Details</x-form-label>
                        <X-form-div class="my-4">
                            <table id="billingTable" class="table align-middle">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </X-form-div>
                    </div>
                    <div class="mb-2 form-control">
                        <x-form-label>Payment Details</x-form-label>
                        <X-form-div class="mt-2">
                            <table id="paymentTable" class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Received By</th>
                                        <th>Pay Method</th>
                                        <th>Comment</th>
                                        <th>Amount</th>
                                        <th>Action</th>
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
