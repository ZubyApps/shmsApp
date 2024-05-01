<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            <x-form-span class="fs-5">Shift Reports</x-form-span>
                            <div class="text-start py-4">
                                <button type="button" id="newPharmacyReportBtn" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    New Shift Report
                                </button>
                            </div>
                            <div class="row overflow-auto my-3">
                                <table id="{{ $dept }}ShiftReportTable" class="table table-sm {{ $dept }}ShiftReportTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Shift</th>
                                            <th>Written By</th>
                                            <th>Viewed</th>
                                            <th>Viewed By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
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
</div>
