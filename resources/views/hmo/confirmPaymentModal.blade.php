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
                            <x-input-span>{{ $identity }}</x-input-span>
                            <x-form-input value="" id="{{ Str::lower($identity) }}" readonly/>
                        </x-form-div>
                        <x-form-div class="col-xl-12 {{ $identity == 'Patient' ? 'd-none' : '' }}">
                            <x-input-span class="">Month/Year</x-input-span>
                            <x-form-input type="text" id="monthYear" readonly/>
                        </x-form-div>
                    </div>
                    <x-form-label>Fill Details</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span class="">Confirmed Pay Date</x-input-span>
                            <x-form-input type="date" id="confirmedPayDate" name="confirmedPayDate"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Amount Confirmed<x-required-span /></x-input-span>
                            <x-form-input type="number" id="amountConfirmed" name="amountConfirmed"/>
                        </x-form-div>                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="savePaymentBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
