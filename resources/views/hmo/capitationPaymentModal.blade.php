<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span>Sponsor</x-input-span>
                            <x-form-input value="" id="sponsor" readonly/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span class="">Month/Year to Pay For</x-input-span>
                            <x-form-input type="text" id="monthYear" />
                        </x-form-div>
                    </div>
                    <x-form-label>Record Capitaton Payment</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span>Month Paid For<x-required-span /></x-input-span>
                            <x-form-input type="month" name="monthPaidFor"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Number of Lives<x-required-span /></x-input-span>
                            <x-form-input type="number" name="numberOfLives" id="numberOfLives" />
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Amount Paid<x-required-span /></x-input-span>
                            <x-form-input type="number" name="amountPaid" id="amountPaid"/>
                        </x-form-div>
                        <x-form-div  class="col-xl-12">
                            <x-input-span>Bank Paid To</x-input-span>
                            <x-form-input type="text" name="bank" id="bank"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Comment</x-input-span>
                            <x-form-input name="comment" id="comment" />
                        </x-form-div>
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="saveCapitationPaymentBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
