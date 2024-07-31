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
                            <x-input-span class="">Month/Year to Send For</x-input-span>
                            <x-form-input type="text" id="monthYear" readonly/>
                        </x-form-div>
                    </div>
                    <x-form-label>Fill the details</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span class="">Month/Year Sent For</x-input-span>
                            <x-form-input type="month" id="monthYear" name="monthSentFor"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Date Sent<x-required-span /></x-input-span>
                            <x-form-input type="datetime-local" name="dateSent"/>
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
                <button type="button" id="saveReminderBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Register
                </button>
            </div>
        </div>
    </div>
</div>
