
<div class="modal fade modal-md" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <x-form-div class="col-xl-12">
                        <x-input-span>Full name</x-input-span>
                        <x-form-input name="fullName" id="fullName" readonly/>
                    </x-form-div>
                    <x-form-div class="col-xl-12">
                        <x-input-span>Designation</x-input-span>
                        <select class="form-select form-select-md" name="designation" id="designation">
                            <option value="">Select</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Nurse">Nurse</option>
                            <option value="LabTech">LabTech</option>
                            <option value="Pharmacy Tech">Pharmacy Tech</option>
                            <option value="Bill Officer">Bill Officer</option>
                            <option value="HMO Officer">HMO Officer</option>
                            <option value="Admin">Admin</option>
                            <option value="IT Officer">IT Officer</option>
                            <option value="Maid">Maid</option>
                            <option value="Security">Security</option>
                        </select>
                    </x-form-div>
                    <x-form-div class="col-xl-12">
                        <x-input-span>Access Level</x-input-span>
                        <select class="form-select form-select-md" name="accessLevel" id="accessLevel">
                            <option value="">Select</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </x-form-div>
                </div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="designateBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Designate
                </button>
            </div>
        </div>
    </div>
</div>