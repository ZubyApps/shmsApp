import $, { data } from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatusX, displayPaystatus, sponsorAndPayPercent } from '../helpers';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';


const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getPayMethodsSummmaryTable = (tableId, startDate, endDate, date) => {
    const summaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/accounts/paymethodsummary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showPaymentsBtn tooltip-test" title="show payments" data-id="${row.id}" data-paymethod="${row.pMethod}">${row.pMethod}</span>`},
            {data: "paymentCount"},
            {data: row => account.format(row.amountPaid)},
        ]
    })

    return summaryTable
}

const getByPayMethodsTable = (tableId, payMethodId, modal, startDate, endDate, date) => {
    const byPayMethodTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/bypaymethod`, data: {
            'payMethodId': payMethodId,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[50, 100, 150, 200, 500],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "patient"},
            {data: "sponsor"},
            {data: "category"},
            {data: "diagnosis"},
            {data: "doctor"},
            {data: "totalHmsBill"},
            {data: "totalHmoBill"},
            {data: "totalNhisBill"},
            {data: "amountPaid"},
            {data: "by"},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        modal._element.querySelector('#payMethodMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
        byPayMethodTable.destroy()
    })

    return byPayMethodTable
}

const getTPSByThirdPartyTable = (tableId, thirdPartyId, modal, startDate, endDate, date) => {
    const TPSByThirdPartyTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/tpsbythirdparty`, data: {
            'thirdPartyId': thirdPartyId,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "initiatedBy"},
            // {data: "thirdParty"},
            {data: row => function () {
                const credit = row.sponsorCategoryClass == 'Credit'
                const NHIS = row.sponsorCategory == 'NHIS'
                return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
                }
            },
            {data: "patient"},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => admissionStatusX(row)},
            {data: row => account.format(row.hmsBill)},
            {
                sortable: false,
                data: row => function () {
                        return `
                        <div class="d-flex flex-">
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteThirPartyServiceBtn tooltip-test ${row.user ? '' : 'd-none'}" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
            }}
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        modal._element.querySelector('#TPSByMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
        TPSByThirdPartyTable.destroy()
    })

    return TPSByThirdPartyTable
}

const getCapitationPaymentsTable = (tableId, startDate, endDate, date) => {
    const capitationPaymentsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/accounts/capitation', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
            }
        },
        orderMulti: true,
        search:true,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "sponsor"},
            {data: "monthPaidFor"},
            {data: row => account.format(row.lives)},
            {data: row => account.format(row.amountPaid)},
            {data: "bank"},
            {data: "comment"},
            {data: "enteredBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                        return `
                        <div class="d-flex flex-">
                            <button type="submit" class="ms-1 btn btn-outline-primary deletePaymentBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        `  
                }      
            },
        ]
    })

    return capitationPaymentsTable
}

const getExpenseSummaryTable = (tableId, startDate, endDate, date) => {
    const expenseSummaryTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/reports/accounts/expensesummary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        lengthMenu:[20, 40, 80, 120, 200],
        language: {
            emptyTable: 'No expense'
        },
        "sAjaxDataProp": "data.data",
        drawCallback: function () {
            var api = this.api()
            
                $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
                $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showExpensesBtn tooltip-test" title="show expenses" data-id="${row.id}" data-expensecategory="${row.eCategory}">${row.eCategory}</span>`},
            {data: row => account.format(+row.expenseCount)},
            {data: row => account.format(+row.totalExpense)},
        ]
    });
    
    return expenseSummaryTable
}

const getTPSSummaryTable = (tableId, startDate, endDate, date) => {
    const TPSSummaryTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/reports/accounts/tpsssummary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        lengthMenu:[20, 40, 80, 120, 200],
        language: {
            emptyTable: 'No expense'
        },
        "sAjaxDataProp": "data.data",
        drawCallback: function () {
            var api = this.api()
            
                $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
                $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
                $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showThirdPartyServicesBtn tooltip-test" title="show third party services" data-id="${row.id}" data-thirdparty="${row.thirdPartyL}">${row.thirdParty}</span>`},
            {data: row => account.format(+row.patientsCount)},
            {data: row => account.format(+row.servicesCount)},
            {data: row => account.format(+row.totalHmsBill)},
        ]
    });
    
    return TPSSummaryTable
}

const getVisitSummaryTable1 = (tableId, startDate, endDate, date) => {
    const visitSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/visitsummary1`, data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        search:true,
        "sAjaxDataProp": "data.data",
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "category"},
            {data: row => account.format(row.sponsorCount)},
            {data: row => account.format(row.patientsCount)},
            {data: row => account.format(row.visitCount)},
            {data: row => account.format(row.totalHmsBill)},
            {data: row => account.format(row.totalHmoBill)},
            {data: row => account.format(row.totalNhisBill)},
            {data: row => account.format(row.totalPaid)},
            {data: row => account.format(row.totalCapitation)},
            {data: row => account.format((+row.totalPaid + +row.totalCapitation) - +row.totalHmsBill)},
        ]
    })

    return visitSummaryTable
}
const getVisitSummaryTable2 = (tableId, startDate, endDate, date) => {
    const visitSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/visitsummary2`, data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        fixedHeader: true,
        lengthMenu:[50, 100, 150, 200, 500],
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        search:true,
        "sAjaxDataProp": "data.data",
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showVisitsBtn tooltip-test" title="show visits" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}" >${row.sponsor}${row.resolved == 0 ? '  <i class="bi bi-check-circle-fill text-primary"></i>' : ''}</span>`},
            {data: "category"},
            {data: row => account.format(row.patientsCount)},
            {data: row => account.format(row.visitCount)},
            {data: row => account.format(row.totalHmsBill)},
            {data: row => account.format(row.totalHmoBill)},
            {data: row => account.format(row.totalNhisBill)},
            {data: row => account.format(row.totalPaid)},
            {data: row => account.format(row.discount)},
            {data: row => account.format(row.totalCapitation)},
            {data: row => account.format((+row.totalPaid + +row.totalCapitation + +row.discount) - +row.totalHmsBill)},
        ]
    })

    return visitSummaryTable
}

const getVisitsBySponsorTable = (tableId, sponsorId, modal, startDate, endDate, date, state) => {
    const visitsBySponsorTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/byvisitbysponsor`, data: {
            'sponsorId': sponsorId,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
            'state'     : state
        }},
        fixedHeader: true,
        orderMulti: true,
        search:true,
        lengthMenu:[50, 100, 150, 200, 500],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "patient"},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: "totalHmsBill"},
            {data: "totalHmoBill"},
            {data: "totalNhisBill"},
            {data: "amountPaid"},
            {data: "discount"},
            {data: row => row.category == 'NHIS' ? account.format((row.amountPaid + row.discount) - row.totalNhisBill) : row.category == 'HMO' ? account.format((row.amountPaid + row.discount) - row.totalHmoBill): account.format((row.amountPaid + row.discount) - row.totalHmsBill)},
            {data: row => function () {
                return `
                <div class="d-flex flex-">
                    <span class="btn reviewSpan" data-id="${row.id}" data-table="${tableId}">${row.reviewed ? row.reviewed: 'Review'}</span>
                    <textarea class="ms-1 form-control reviewInput d-none text-secondary" value="" name="reviewed" id="reviewed">${row.reviewed ?? ''}</textarea>
                </div>
            `
                }
            },
            {data: row => function () {
                return `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn  markAsResolvedBtn" data-id="${row.id}" data-table="${tableId}" data-state="${row.resolved}">
                        ${row.resolved ? '<i class="bi bi-check-circle-fill text-primary"></i>' : 'Resolve'}
                    </button>
                </div>
            `
                }
            },
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        visitsBySponsorTable.destroy()
        modal._element.querySelector('#visitMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
    })

    return visitsBySponsorTable
}

const getYearlyIncomeAndExpenseTable = (tableId, year) => {
    const yearlyIncomeAndExpenseTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/reports/accounts/yearlysummary', data: {
            'year'    : year,
        }},
        orderMulti: true,
        searching: false,
        lengthChange: false,
        info: false,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        drawCallback: function () {
            var api = this.api()
            
                $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
                $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
                $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
                $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
                $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
                $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "month_name"},
            {data: row => account.format(row.bill)},
            {data: row => account.format(row.paid)},
            {data: row => account.format(row.expense)},
            {data: row => account.format(row.bill - row.expense)},
            {data: row => account.format(row.paid - row.expense)},
            {data: row => account.format((row.bill - row.expense) - (row.paid - row.expense))},
        ]
    });
    
    return yearlyIncomeAndExpenseTable
}

export {getPayMethodsSummmaryTable, getCapitationPaymentsTable, getTPSSummaryTable, getByPayMethodsTable, getTPSByThirdPartyTable, getExpenseSummaryTable, getVisitSummaryTable1, getVisitSummaryTable2, getVisitsBySponsorTable, getYearlyIncomeAndExpenseTable}
