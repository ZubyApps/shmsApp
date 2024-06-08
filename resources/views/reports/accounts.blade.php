@extends('layout')


@section('content')
@vite(['resources/js/accountReports.js'])

@include('reports.modals.byPayMethodModal', ['title' => 'Payments By Pay Method', 'id' => 'byPayMethodModal'])
@include('reports.modals.visitsBySponsorModal', ['title' => 'Visits By Sponsor', 'id' => 'visitsBySponsorModal'])
@include('reports.modals.byExpenseCategoryModal', ['title' => 'Expenses By Category', 'id' => 'byExpenseCategoryModal'])
@include('reports.modals.TPSByThirdPartyModal', ['title' => 'Third Party Services By Third Party', 'id' => 'TPSByThirdPartyModal'])
@include('billing.expenseModal', ['title' => "New Expense", 'isUpdate' => false, 'isManagement' => true, 'id' => 'newExpenseModal'])
@include('billing.expenseModal', ['title' => "Update Expense", 'isUpdate' => true, 'isManagement' => true, 'id' => 'updateExpenseModal'])

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
                <button class="nav-link"  id="nav-TPSSummary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-TPSSummary" type="button" role="tab" aria-controls="nav-TPSSummary"
                    aria-selected="true">Third Party Summary</button>
                <button class="nav-link" id="nav-expenses-tab" data-bs-toggle="tab" data-bs-target="#nav-expenses"
                    type="button" role="tab" aria-controls="nav-expenses" aria-selected="false">Expenses</button>
                <button class="nav-link"  id="nav-expenseSummary-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-expenseSummary" type="button" role="tab" aria-controls="nav-expenseSummary"
                    aria-selected="false">Expense Summary</button>
                <button class="nav-link"  id="nav-visitSummary1-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-visitSummary1" type="button" role="tab" aria-controls="nav-visitSummary1"
                    aria-selected="false">Visit Summary1</button>
                <button class="nav-link"  id="nav-visitSummary2-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-visitSummary2" type="button" role="tab" aria-controls="nav-visitSummary2"
                    aria-selected="false">Visit Summary2</button>
                <button class="nav-link" id="nav-yearlyIncomeAndExpense-tab" data-bs-toggle="tab" 
                    data-bs-target="#nav-yearlyIncomeAndExpense" type="button" role="tab" aria-controls="nav-yearlyIncomeAndExpense" 
                    aria-selected="false">Yearly Summary</button>
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
                                <th>Payments</th>
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
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Third Party Services Summary Table -->
            <div class="tab-pane fade" id="nav-TPSSummary" role="tabpanel" aria-labelledby="nav-TPSSummary-tab" tabindex="0">
                <div class="py-2">
                    <h5 class="card-title py-4">Third Party Services Summary</h5>
                    <x-form-div class="col-xl-8 py-3 TPSSummaryDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchTPSSummaryByDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="TPSSummaryMonth" id="TPSSummaryMonth" />
                        <button class="input-group-text searchTPPSSummaryMonthBtn">Search</button>
                    </x-form-div>
                    <table  id="TPSSummaryTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Third Party Org</th>
                                <th>Patients</th>
                                <th>Services</th>
                                <th>Total Hms Bill</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder">
                            <tr>
                                <td class="text-center">Total</td>
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
                            <th>Desription</th>
                            <th>Category</th>
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
            <!-- Visit Summary 1 table -->
            <div class="tab-pane fade" id="nav-visitSummary1" role="tabpanel" aria-labelledby="nav-visitSummary1-tab" tabindex="0">
                <h5 class="card-title py-4">Visits Summary </h5>
                <x-form-div class="col-xl-8 py-3 visistSummaryDiv1">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchVisitsByDatesBtn1">Search</button>
                    <x-input-span class="">OR</x-input-span>
                    <x-input-span class="">Month/Year</x-input-span>
                    <x-form-input type="month" name="visitSummaryMonth1" id="visitSummaryMonth1" />
                    <button class="input-group-text searchVisitsByMonthBtn1">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table  id="visitSummaryTable1" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor Category</th>
                                <th>Sponsors</th>
                                <th>Patients</th>
                                <th>Visits</th>
                                <th>VisitsC</th>
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
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Visit Summary 2 table -->
            <div class="tab-pane fade" id="nav-visitSummary2" role="tabpanel" aria-labelledby="nav-visitSummary2-tab" tabindex="0">
                <h5 class="card-title py-4">Visits Summary </h5>
                <x-form-div class="col-xl-8 py-3 visistSummaryDiv2">
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchVisitsByDatesBtn2">Search</button>
                    <x-input-span class="">OR</x-input-span>
                    <x-input-span class="">Month/Year</x-input-span>
                    <x-form-input type="month" name="visitSummaryMonth2" id="visitSummaryMonth2" />
                    <button class="input-group-text searchVisitsByMonthBtn2">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table  id="visitSummaryTable2" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor</th>
                                <th>Category</th>
                                <th>Patients</th>
                                <th>Visits</th>
                                <th>HMS Bill</th>
                                <th>HMO Bill</th>
                                <th>NHIS Bill</th>
                                <th>Paid</th>
                                <th>Discount</th>
                                <th>Capitation</th>
                                <th>(Paid+Captation) - HMS</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td class="fw-bold"></td>
                                <td class="fw-bold"></td>
                                <td class="fw-bold"></td>
                                <td class="fw-bold"></td>
                                <td class="fw-bold"></td>
                                <td class="fw-bold"></td>
                                <td class="text-danger fw-bold"></td>
                                <td class="fw-bold"></td>
                                <td class="text-danger fw-bold"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
             <!-- Yearly income and expenses table -->
            <div class="tab-pane fade" id="nav-yearlyIncomeAndExpense" role="tabpanel" aria-labelledby="nav-yearlyIncomeAndExpense-tab" tabindex="0">
                <div class="py-4 ">
                    <x-form-div class="col-lg-3 py-3 yearlyIncomeAndExpenseDiv">
                        <x-input-span class="">Year</x-input-span>
                        <x-form-input type="number" name="incomeAndExpenseyear" id="incomeAndExpenseyear" min="1900" max="{{ date('Y') }}" value="{{ date('Y') }}" />
                        <button class="input-group-text searchIncomeAndExpenseByYearBtn">Get</button>
                    </x-form-div>
                    <table id="yearlyIncomeAndExpenseTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Bill</th>
                                <th>Total Paid</th>
                                <th>Total Expense</th>
                                <th>Expected Net</th>
                                <th>Actual Net</th>
                                <th>Net Difference</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="text-primary">
                                <td class="fw-bold">Total</td>
                                <td class="fw-bold "></td>
                                <td class="fw-bold "></td>
                                <td class="fw-bold "></td>
                                <td class="fw-bold "></td>
                                <td class="fw-bold "></td>
                                <td class="fw-bold "></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection