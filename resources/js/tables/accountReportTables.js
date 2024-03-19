import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';


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
            {data: row => `<span class="btn text-decoration-underline showPaymentsBtn tooltip-test" title="show payments" data-id="${row.id}" data-paymethod=${row.pMethod}>${row.pMethod}</span>`},
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
        lengthMenu:[20, 40, 80, 120, 200],
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

const getCapitationPaymentsTable = (tableId, startDate, endDate, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

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

const getVisitSummaryTable = (tableId, startDate, endDate, date) => {
    const visitSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/visitsummary`, data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        search:true,
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

const getVisitsBySponsorCategoryTable = (tableId, payMethodId, modal, startDate, endDate, date) => {
    const byPayMethodTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/accounts/bysponsorcategory`, data: {
            'payMethodId': payMethodId,
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
            {data: "patient"},
            {data: "sponsor"},
            {data: "category"},
            {data: "diagnosis"},
            {data: "doctor"},
            {data: "totalHmsBill"},
            {data: "totalHmoBill"},
            {data: "totalNhisBill"},
            {data: "amountPaid"},
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

export {getPayMethodsSummmaryTable, getCapitationPaymentsTable, getByPayMethodsTable, getExpenseSummaryTable, getVisitSummaryTable, getVisitsBySponsorCategoryTable}