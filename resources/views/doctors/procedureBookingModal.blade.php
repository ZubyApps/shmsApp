<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Details</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span>Procedure</x-input-span>
                            <x-form-input type="" id="procedure"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span class="">Book Date</x-input-span>
                            <x-form-input type="datetime-local" id="bookedDate" name="bookedDate"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Comment</x-input-span>
                            <x-form-input type="" id="comment" name="comment"/>
                        </x-form-div>                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="saveBookedProcedureBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
