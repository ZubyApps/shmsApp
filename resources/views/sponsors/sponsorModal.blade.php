@vite(['resources/js/modals/sponsorModal.js'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Sponsor Information</x-form-label>
                    <div class="row">
                        <x-form-div class="my-2 col-xl-6">
                            <x-input-span>Category<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" id="sponsorCategory1" name="category">
                                <option value="">Select Category</option> 
                                @foreach ($categories as $category )
                                    @if (Str::lower($category->name) == "individual" && $category->sponsors()->count() > 0)
                                    <option disabled value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name }}</option>
                                    @else
                                    <option value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </x-form-div>
                    </div>

                    <!-- first row -->
                    <div class="{{ !$isUpdate ? 'd-none' : '' }} allSponsorInputsDiv form-control">
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Name<x-required-span /></x-input-span>
                                <x-form-input name="name" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Phone No.<x-required-span /></x-input-span>
                                <x-form-input name="phone" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Email</x-input-span>
                                <x-form-input type="email" name="email" />
                            </x-form-div>
                            <x-form-div class="registrationBillDiv1 col-xl-6">
                                <x-input-span>Registration Bill</x-input-span>
                                <select class="form-select form-select-md familyRegistrationBill" name="{{ $isUpdate ? 'registerationBill' : '' }}">
                                    <option class="familyRegistrationBillOption" value="5000">5000</option>
                                    <option value="1500">2000 (Upgrade)</option>
                                    <option value="old">Old</option>
                                    <option class="{{ !$isUpdate ? 'd-none' : ''  }}" value="">N/A</option>  
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Max Pay Days</x-input-span>
                                <x-form-input type="number" name="maxPayDays" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Flag Sponsor</x-input-span>
                                <select class="form-select form-select-md" name="flagSponsor">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>  
                                </select>
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
                <button type="button" id="{{ $isUpdate ? 'saveSponsorBtn' : 'createSponsorBtn' }}" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
