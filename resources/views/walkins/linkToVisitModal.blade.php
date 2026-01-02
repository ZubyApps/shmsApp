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
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Name</x-input-span>
                                    <x-form-input name="name" value="" id="name"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Phone</x-input-span>
                                    <x-form-input name="phone" value="" id="phone"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6 d-none">
                                    <x-input-span>WalkinId</x-input-span>
                                    <x-form-input name="walkinId" value="" id="walkinId"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>Link Walkin Tests to a Visit</x-form-span>
                            <div class="row overflow-auto my-3">
                                <table id="linkToVisitTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Seen</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Current Diagnosis</th>
                                            <th>Sponsor</th>
                                            <th>Status</th>
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
                    {{-- <button type="button" class="btn btn-primary" id="linkVisitBtn">
                        <i class="bi bi-link-45deg"></i>
                        Link Visit
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
