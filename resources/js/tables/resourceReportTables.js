import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

const getResourceValueSummaryTable = (tableId, startDate, endDate) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const summaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/resources/summary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "rCategory"},
            {data: "subCategoryCount"},
            {data: "resourceCount"},
            {data: "stockLevel"},
            {data: row => account.format(row.purchacedValue)},
            {data: row => account.format(row.sellValue)},
        ]
    })

    return summaryTable
}

const getUsedResourcesSummaryTable = (tableId, startDate, endDate) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const usedSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/resources/usedsummary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
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
            // $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "rCategory"},
            // {data: "subCategoryCount"},
            {data: "resourceCount"},
            {data: "prescriptionsCount"},
            {data: "expectedCost"},
            {data: "dispensedCost"},
            {data: "expectedIncome"},
            {data: "dispensedIncome"},
            {data: "actualIncome"},
            {data:  row => row.actualIncome - row.expectedIncome},
        ]
    })

    return usedSummaryTable
}

const getByResourceTable = (tableId, resourceId, modal, startDate, endDate) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const patientsByResourceTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/investigations/byresource`, data: {
            'resourceId': resourceId,
            'startDate' : startDate, 
            'endDate'   : endDate,
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "category"},
            {data: "diagnosis"},
            {data: "doctor"},
            {data: "Hmsbill"},
            {data: "Hmobill"},
            {data: "paid"},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        patientsByResourceTable.destroy()
    })

    return patientsByResourceTable
}

export {getResourceValueSummaryTable, getUsedResourcesSummaryTable, getByResourceTable}