<?php
$cardNumber = 'SH23/0960';
$patientName = 'Mohammed Hassan Suleiman';
?>
<div class="modal fade modal-md" id="initiatePatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Initiate Patient Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <x-form-div class="col-xl-12">
                    <x-input-span>Initiate</x-input-span>
                    <x-form-input name="patientIds" class="" value=""/>
                    <x-input-span>visit?</x-input-span>
                </x-form-div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    No
                </button>
                <button type="button" class="btn bg-primary text-white confirmVisitBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Yes
                </button>
            </div>
        </div>
    </div>
</div>