
<div class="modal fade modal-md" id="sponsorPercentagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Sponsor Percentages Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <x-form-div class="col-xl-12">
                        <x-input-span>Sponsor</x-input-span>
                        <x-form-input name="sponsorName" value="" id="sponsorName" readonly/>
                    </x-form-div>
                </div>
                <div class="resourceCategoriesDiv col-xl-12 px-1">
                    <table id="percentagesTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Resource Category</th>
                                <th>Percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>