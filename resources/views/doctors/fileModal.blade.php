<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Document Details</x-form-label>
                    <div class="allSponsorInputsDiv">
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span>Name File<x-required-span /></x-input-span>
                                <x-form-input name="filename" placeholder="eg: x-ray, scan result, lab result etc.." id="filename"/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-form-input type="file" name="patientsFile" id="patientsFile" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span>Third Party <i><small>(if applicable)</small></i></x-input-span>
                                <select class="form-select form-select-md" id="thirdParty" name="thirdParty">
                                    <option value="">Select</option>   
                                    @foreach ($thirdParties as $thirdParty )
                                        <option value="{{ $thirdParty->id}}" name="{{ $thirdParty->short_name }}">{{ $thirdParty->short_name }}</option>
                                    @endforeach
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span>Comment</x-input-span>
                                <x-form-textarea name="comment" id="comment"></x-form-textarea>
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
                <button type="button" id="uploadFileBtn" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Create
                </button>
            </div>
        </div>
    </div>
</div>
