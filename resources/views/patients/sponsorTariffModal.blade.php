
<div class="modal fade modal-md" id="sponsorTariffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Sponsor Tariff Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <x-form-div class="col-xl-12">
                        <x-input-span>Sponsor</x-input-span>
                        <x-form-input name="sponsorName" value="" id="sponsorName" readonly/>
                    </x-form-div>
                </div>
                <x-form-div class="col-xl-12">
                    <x-input-span id="resourceLabel">Medical Resource<x-required-span /></x-input-span>
                    <input class="form-control resource" type="search" autocomplete="off" name="resource" id="resource" data-input="" placeholder="search" list="resourceList"/>
                    <datalist name="resource" type="text" class="decoration-none" id="resourceList"></datalist>
                </x-form-div>
                <x-form-div class="col-xl-12">
                    <x-input-span id="doctorLabel">Selling Price</x-input-span>
                    <x-form-input type="number" name="sellingPrice" value="" id="sellingPrice" />
                </x-form-div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="saveSellPriceBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Create
                </button>
            </div>
        </div>
    </div>
</div>