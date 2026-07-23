<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            <x-form-div class="col-xl-12">
                                    <x-input-span>Sending Category</x-input-span>
                                    <select class="form-select form-select-md" name="sendingCategory" id="sendingCategory">
                                        <option value="">Select</option>
                                        <option value="single">Single receiver</option>
                                        <option value="multiple">Multiple receivers</option>
                                    </select>
                            </x-form-div>
                            <div class="mb-2 form-control singleReceiverDiv d-none">
                                @include('communicationservices.includes.singleReceiver')
                            </div>
                            <div class="mb-2 form-control multipleReceiversDiv d-none">
                                @include('communicationservices.includes.multipleReceivers')
                            </div>
                        </div>
                        <div class="mb-2 form-control" id="medicalReportDetailsDiv">
                            <div class="row">
                                <x-form-span class="fs-5">SMS Details</x-form-span>                        
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="textDetailsLabel">Text<x-required-span /></x-input-span>
                                    <textarea class="form-control" type="text" name="smsDetails" id="smsDetails" rows="5" cols="5"></textarea>
                                </x-form-div>
                                {{-- <x-form-div class="col-xl-12 {{ $title == 'New Report' ? 'd-none' : '' }}">
                                    <x-input-span>Written By</x-input-span>
                                    <x-form-input name="writtenBy" id="writtenBy" readonly/>
                                    <x-input-span>Written At</x-input-span>
                                    <x-form-input name="writtenAt" id="writtenAt" readonly/>
                                </x-form-div>                       --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="sendSmsBtn" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
