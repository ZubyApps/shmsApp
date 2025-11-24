<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Record Expense</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-12">
                            <x-input-span>Description<x-required-span /></x-input-span>
                            <x-form-input type="text" name="description" id="description" />
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Category<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" id="expenseCategory" name="expenseCategory">
                                <option value="">Select Category</option>   
                                @foreach ($categories as $category )
                                    <option value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Amount<x-required-span /></x-input-span>
                            <x-form-input type="number" name="amount" id="amount"/>
                        </x-form-div>
                        <x-form-div  class="col-xl-12">
                            <x-input-span>Given To</x-input-span>
                            <x-form-input type="text" name="givenTo" id="givenTo"/>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Approved By</x-input-span>
                            <select class="form-select form-select-md" name="approvedBy" id="">
                                <option value="">Select</option>   
                                @foreach ($users as $user )
                                    <option value="{{ $user->id}}" name="{{ $user->username }}">{{ $user->username}}</option>
                                @endforeach
                            </select>
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>PayMethod<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" id="payMethod" name="payMethod">
                                <option value="">Select Category</option>   
                                @foreach ($payMethods as $payMethod )
                                    <option value="{{ $payMethod->id}}" name="{{ $payMethod->name }}">{{ $payMethod->name }}</option>
                                @endforeach
                            </select>
                        </x-form-div>
                        <x-form-div  class="col-xl-12">
                            <x-input-span>Comment </x-input-span>
                            <x-form-input type="text" name="comment" id="comment"/>
                        </x-form-div>
                        @if ($isManagement)
                            <x-form-div  class="col-xl-12">
                                <x-input-span>Back Date</x-input-span>
                                <x-form-input type="datetime-local" name="backdate" id="backdate"/>
                            </x-form-div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'updateExpenseBtn' : 'saveExpenseBtn' }}" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Save' }}
                </button>
            </div>
        </div>
    </div>
</div>
