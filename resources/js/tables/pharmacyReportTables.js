import $ from 'jquery';
import DataTable from 'datatables.net-bs5';

const getPharmacySummaryTable = (tableId, startDate, endDate, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const summaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/pharmacy/summary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[50, 150, 200, 300, 500],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showPatientsBtn tooltip-test" title="show patients" data-id="${row.id}" data-resource="${row.name}" data-subcategory="${row.subCategory}">${row.name}</span>`},
            {data: "subCategory"},
            {data: "prescriptions"},
            {data: "qtyBilled"},
            {data: "qtyDispensed"},
            {data: "bulkDispensed"},
            {data: row => row.qtyDispensed + row.bulkDispensed},
        ]
    })

    return summaryTable
}

const getByResourceTable = (tableId, resourceId, modal, startDate, endDate, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const patientsByResourceTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/pharmacy/byresource`, data: {
            'resourceId': resourceId,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[50, 100, 150, 200, 300],
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
            {data: "qtyBilled"},
            {data: "qtyDispensed"},
            {data: "Hmsbill"},
            {data: "Hmobill"},
            {data: "paid"},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        modal._element.querySelector('#resourceMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
        patientsByResourceTable.destroy()
    })

    return patientsByResourceTable
}

const getMissingPharmacySummaryTable = (tableId, startDate, endDate, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const summaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/pharmacy/missing', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[50, 150, 200, 300, 500],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "name"},
            {data: "category"},
            {data: "addedResourceCount"},
            {data: "quantity"},
            {data: "finalQuantity"},
            {data: "diff"},
            {data: "diffPurchase"},
            {data: "diffSelling"},
        ]
    })

    return summaryTable
}

export {getPharmacySummaryTable, getByResourceTable, getMissingPharmacySummaryTable}