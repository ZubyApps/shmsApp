import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getResourceValueSummaryTable = (tableId, startDate, endDate) => {
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
            {data: row => account.format(row.subCategoryCount)},
            {data: row => account.format(row.resourceCount)},
            {data: row => account.format(row.stockLevel)},
            {data: row => account.format(row.purchacedValue)},
            {data: row => account.format(row.sellValue)},
        ]
    })

    return summaryTable
}

const getUsedResourcesSummaryTable = (tableId, startDate, endDate, date) => {
    const usedSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/resources/usedsummary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            }
        ],
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
            {data: row => `<span class="btn text-decoration-underline showPrescriptionsBtn tooltip-test" title="show prescriptions" data-id="${row.id}" data-category="${row.rCategory}">${row.rCategory}</span>`},
            {data: row => account.format(row.resourceCount)},
            {data: row => account.format(row.prescriptionsCount)},
            {data: row => account.format(row.expectedCost)},
            {
                visible: false,
                data: row => account.format(row.dispensedCost)
            },
            {data: row => account.format(row.expectedIncome)},
            {
                visible: false,
                data: row => account.format(row.dispensedIncome)
            },
            {data: row => account.format(row.actualIncome)},
            {data:  row => account.format(row.actualIncome - row.expectedCost)},
            {data:  row => account.format(row.actualIncome - row.expectedIncome)},
        ]
    })

    return usedSummaryTable
}

const getByResourceCategoryTable = (tableId, resourceCategoryId, modal, startDate, endDate, date) => {
    const patientsByResourceTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/resources/bycategoryresource`, data: {
            'resourceCategoryId': resourceCategoryId,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
            $( api.column(11).footer() ).html(account.format(api.column( 11, {page:'current'} ).data().sum()));
            $( api.column(12).footer() ).html(account.format(api.column( 12, {page:'current'} ).data().sum()));
            $( api.column(13).footer() ).html(account.format(api.column( 13, {page:'current'} ).data().sum()));
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
            {data: "resource"},
            {data: "resourceSubcategory"},
            {data: row => account.format(row.hmsBill)},
            {data: row => account.format(row.paid)},
            {data: row => account.format(row.capitation)},
            {data:row => account.format(row.paid + row.capitation)},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        modal._element.querySelector('#resourcesMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
        patientsByResourceTable.destroy()
    })



    return patientsByResourceTable
}

const getExpirationStockTable = (tableId, filter) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/reports/resources/expiratonstock', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No Resource'
        },
        columns: [
            {data: "name"},
            {data: row => () => {
                if (row.stockLevel <= row.reOrderLevel) {
                    return `<span class="text-danger fw-semibold">${row.stockLevel +' '+ row.description}</span>`
                    }
                return row.stockLevel +' '+ row.description
                } },
            {data: row => row.reOrderLevel +' '+ row.description},
            {data: "purchasePrice"},
            {data: "sellingPrice"},
            {data: row => row.stockLevel * row.purchasePrice},
            {data: row => row.stockLevel * row.sellingPrice},
            {data: row => () => {
                if (row.flag) {
                    return `<span class="text-danger fw-semibold">${row.expiring}</span>`
                    }
                return row.expiring
                } 
            },
            {data: "prescriptionFrequency"},
            {data: "dispenseFrequency"},
        ]
    });
}

export {getResourceValueSummaryTable, getUsedResourcesSummaryTable, getByResourceCategoryTable, getExpirationStockTable}