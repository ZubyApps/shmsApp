<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="patientInfoDiv form-control mb-2">
                        <x-form-span>Patient Details</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="patientLabel">Patient</x-input-span>
                                <x-form-input name="patientId" id="patientId" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
                                <x-form-input type="search" class="sponsorName" name="sponsorName" id="sponsorName" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Staff ID/No.</x-input-span>
                                <x-form-input name="staffId" class="staffId" id="staffId"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Phone Number</x-input-span>
                                <x-form-input type="tel" name="phoneNumber" id="phoneNumber" readonly />
                            </x-form-div>
                        </div>
                    </div>
                    <div class="mt-4 form-control" id="sponsorDetailsDiv">
                        <x-form-span>Fill Information</x-form-span>
                        <div class="row">
                            <x-form-div>
                                <x-input-span id="sponsorCategoryLabel">Sponsor Category<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="sponsorCategory" id="newSponsorCategory">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category )
                                    <option @if (Str::lower($category->name) === "family") {!! 'class="familyOption"' !!} @endif value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </x-form-div>
                            <x-form-div class="sponsorNameDiv">
                                <x-input-span id="sponsorNameLabel">Sponsor<x-required-span /></x-input-span>
                                <x-form-input type="search" name="sponsor" class="sponsorName categorySponsor" id="newPatientSponsor" placeholder="Search..." list="newSponsorList"/>
                                <datalist name="sponsor" type="text" class="decoration-none bg-white sponsorList" id="newSponsorList"></datalist>
                            </x-form-div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="saveNewSponsorBtn" class="btn btn-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
