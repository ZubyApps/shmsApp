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
                                {{-- <option value="Self">Self</option> 
                                <option value="Family">Family</option> 
                                <option value="HMO">HMO</option> 
                                <option value="NHIS">NHIS</option> 
                                <option value="Retainership">Retainership</option>  --}}
                                @foreach ($categories as $category )
                                    @if (Str::lower($category->name) == "self" && $category->sponsors()->count() > 0)
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
                                <x-input-span>Phone No.</x-input-span>
                                <x-form-input name="phone" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Email</x-input-span>
                                <x-form-input name="email" />
                            </x-form-div>
                            <x-form-div class="registrationBillDiv1 col-xl-6">
                                <x-input-span>Registration Bill</x-input-span>
                                <select class="form-select form-select-md familyRegistrationBill" name="{{ $isUpdate ? 'registerationBill' : '' }}">
                                    <option class="familyRegistrationBillOption" value="3500">3500</option>
                                    <option value="1500">1500 (Upgrade)</option>
                                    <option value="old">Old</option>
                                    <option class="{{ !$isUpdate ? 'd-none' : ''  }}" value="">N/A</option>
                                    
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
