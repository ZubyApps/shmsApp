<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 form-control bulkRequestDiv">
                        <x-form-span>Add to Theatre Stock</x-form-span>
                        <div class="row">
                            <div class="valuesDiv">
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="itemLabel">Resource<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md" name="resource" id="resource">
                                    </select>
                                </x-form-div>        
                                <x-form-div class="col-xl-12 qty" id="qty">
                                    <x-input-span id="quantityLabel">Quantity<x-required-span /></x-input-span>
                                    <x-form-input type="number" name="quantity" id="quantity"
                                        placeholder="quantity"/>
                                </x-form-div>
                                {{-- <x-form-div class="col-xl-12">
                                    <x-input-span id="noteLabel">Note</x-input-span>
                                    <x-form-input type="text" name="note" id="note"/>
                                </x-form-div> --}}
                            </div>
                            {{-- <x-form-div class="col-xl-12 qty" id="qty">
                                <x-form-input hidden name="department" id="department" value="{{ $dept }}"/>       
                            </x-form-div>  --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="resolveTheatreBtn" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Resolve
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
