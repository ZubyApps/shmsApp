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
                            <x-input-span>Patient</x-input-span>
                            <x-form-input value="" id="patient" readonly/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span class="">Sponsor</x-input-span>
                            <x-form-input type="text" id="sponsor" readonly/>
                        </x-form-div>
                    </div>
                    <x-form-label>Fill the details</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span>Date Set<x-required-span /></x-input-span>
                            <x-form-input type="datetime-local" name="dateSet"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span class="">Max Days</x-input-span>
                            <x-form-input type="number" id="maxDays" name="maxDays"/>
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
                <button type="button" id="saveBillReminderBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Register
                </button>
            </div>
        </div>
    </div>
</div>
