@extends('layout')


@section('content')
@vite(['resources/js/accountReports.js'])

@include('reports.modals.byPayMethodModal', ['title' => 'Paymets By Paymethod', 'id' => 'byPayMethodModal'])
@include('reports.modals.byExpenseCategoryModal', ['title' => 'Paymets By Paymethod', 'id' => 'byExpenseCategoryModal'])
@include('billing.expenseModal', ['title' => "New Expense", 'isUpdate' => false, 'id' => 'newExpenseModal'])
@include('billing.expenseModal', ['title' => "Update Expense", 'isUpdate' => true, 'id' => 'updateExpenseModal'])

<div class="container mt-5">
    @include('reports.reportGrid')
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-payMethodSummary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-payMethodSummary" type="button" role="tab" aria-controls="nav-payMethodSummary"
                    aria-selected="true">Pay Methods Summary</button>
                <button class="nav-link"  id="nav-capitationPayments-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-capitationPayments" type="button" role="tab" aria-controls="nav-capitationPayments"
                    aria-selected="true">Capitation Payments</button>
                <button class="nav-link" id="nav-expenses-tab" data-bs-toggle="tab" data-bs-target="#nav-expenses"
                    type="button" role="tab" aria-controls="nav-expenses" aria-selected="false">Expenses</button>
                <button class="nav-link"  id="nav-expenseSummary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-expenseSummary" type="button" role="tab" aria-controls="nav-expenseSummary"
                    aria-selected="false">Expense Summary</button>
                <button class="nav-link"  id="nav-visitSummary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-visitSummary" type="button" role="tab" aria-controls="nav-visitSummary"
                    aria-selected="false">Visit Summary</button>
                {{-- <button class="nav-link"   id="nav-summaries-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summaries" type="button" role="tab" aria-controls="nav-summaries"
                    aria-selected="false">Summaries</button> --}}
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- Pay methods summary table -->
            <div class="tab-pane fade show active" id="nav-payMethodSummary" role="tabpanel" aria-labelledby="nav-payMethodSummary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Pay Methods Summary</h5>
                    <x-form-div class="col-xl-8 py-3 payMethodDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchPayMethodByDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="payMethodMonth" id="payMethodMonth" />
                        <button class="input-group-text searchPayMethodByMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="payMethodSummaryTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Pay Method</th>
                                <th>Paymenys</th>
                                <th>Total AMount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Capitation Payments table -->
            <div class="tab-pane fade" id="nav-capitationPayments" role="tabpanel" aria-labelledby="nav-capitationPayments-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Capitation Payments Table</h5>
                    <x-form-div class="col-xl-8 py-3 capitationDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchByCapitationDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="capitationMonth" id="capitationMonth" />
                        <button class="input-group-text searchByCapitationMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="capitationPaymentsTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor</th>
                                <th>Month Paid for</th>
                                <th>Lives</th>
                                <th>Amount</th>
                                <th>Bank</th>
                                <th>Comment</th>
                                <th>Entered By</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Expenses table -->
            <div class="tab-pane fade" id="nav-expenses" role="tabpanel" aria-labelledby="nav-expenses-tab"
            tabindex="0">
            <div class="py-4 ">
                <div class="text-start py-3">
                    <button type="button" id="newExpenseBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Expense
                    </button>
                </div>
                <table id="expensesTable" class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Desription</th>
                            <th>Amount</th>
                            <th>Given to</th>
                            <th>Given By</th>
                            <th>Approved By</th>
                            <th>Comment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="">
                            <td class="fw-semibold"></td>
                            <td class="fw-semibold">Total</td>
                            <td class="fw-semibold"></td>
                            <td class="fw-semibold"></td>
                            <td class="fw-semibold"></td>
                            <td class="fw-semibold"></td>
                            <td class="fw-semibold"></td>
                            <td class="fw-semibold"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
            <!-- Category Expense Summary table -->
            <div class="tab-pane fade" id="nav-expenseSummary" role="tabpanel" aria-labelledby="nav-expenseSummary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Expenses Summary</h5>
                    <x-form-div class="col-xl-8 py-3 expenseSummaryDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchExpenseSummaryByDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="expenseMonth" id="expenseMonth" />
                        <button class="input-group-text searchExpenseSummaryByMonthBtn">Search</button>
                    </x-form-div>
                    <table id="expenseSummaryTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Expense Category</th>
                                <th>Total Count</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Visit Details table -->
            <div class="tab-pane fade" id="nav-visitSummary" role="tabpanel" aria-labelledby="nav-visitSummary-tab" tabindex="0">
                <h5 class="card-title py-4">Visits Summary </h5>
                <x-form-div class="col-xl-8 py-3 visistSummaryDiv">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchVisitsByDatesBtn">Search</button>
                    <x-input-span class="">OR</x-input-span>
                    <x-input-span class="">Month/Year</x-input-span>
                    <x-form-input type="month" name="visitSummaryMonth" id="visitSummaryMonth" />
                    <button class="input-group-text searchVisitsByMonthBtn">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table  id="visitSummaryTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor Category</th>
                                <th>Sponsors</th>
                                <th>Patients</th>
                                <th>Visits</th>
                                <th>HMS Bill</th>
                                <th>HMO Bill</th>
                                <th>NHIS Bill</th>
                                <th>Paid</th>
                                <th>Capitation</th>
                                <th>(Paid+Captation) - HMS</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- summaries tables -->
            {{-- <div class="tab-pane fade" id="nav-summaries" role="tabpanel" aria-labelledby="nav-summaries-tab" tabindex="0">
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Sex</h5>
                                <table  id="sexAggregateTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Total</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Age and Sex</h5>
                                <table  id="ageAggregateTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>0-3m</th>
                                            <th>3-12m</th>
                                            <th>1-5y</th>
                                            <th>5-13y</th>
                                            <th>13-18y</th>
                                            <th>18-48y</th>
                                            <th>48-63y</th>
                                            <th>63</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Totals</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                      </div>
                </div>
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">New Registrations by Sponsor This Month</h5>
                                <table  id="totalPatientsTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sponsor</th>
                                            <th>Count</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Total</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="card">
                            <div class="card-body">
                            <h5 class="card-title">Visits Summary by Sponsor This Month</h5>
                            <table  id="visitsSummaryTable" class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Sponsor</th>
                                        <th>Outpatients</th>
                                        <th>Inpatients</th>
                                        <th>Observations</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td class="text-center">Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                          </div>
                        </div>
                      </div>
                </div>
            </div> --}}
        </div>
    </div>
    
</div>


@endsection