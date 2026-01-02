
<div class="modal fade modal-md" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center paymentDetailsDiv">
                    <div class="card border-0  w-100">
                        <div class="toast align-items-center shadow-none border-0" id="savePaymentToast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-body">
                                <h6 class="text-primary">Successful</h6>
                            </div>  
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0"> Amount <input class="ms-1 form-control amountInput" type="number" id="amount" name="amount"></li>
                            <li class="list-group-item border-0">Pay Method
                            <select class="form-select form-select-md" id="payMethod" name="payMethod">
                                <option value="">Select Category</option>   
                                @foreach ($payMethods as $payMethod )
                                    <option value="{{ $payMethod->id}}" name="{{ $payMethod->name }}">{{ $payMethod->name }}</option>
                                @endforeach
                            </select>
                            </li>
                            <li class="list-group-item border-0">Comment <input class="ms-1 form-control commentInput" id="comment" name="comment"></li>
                        </ul>
                        <div class="card-footer">
                            <button class="payBtn btn btn-primary">Pay</button>
                        </div>
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