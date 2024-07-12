import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatusX, displayPaystatus, sponsorAndPayPercent } from "../helpers";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getlistOfServicesTable = (tableId) => {
    const preparedColumns = [
        {data: "date"},
        {data: "initiatedBy"},
        {data: "thirdParty"},
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

    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/thirdpartyservices/load/list', data: {
        }},
        orderMulti: true,
        search:true,
        searchDelay: 1500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        language: {
            emptyTable: 'No Third Party Services'
        },
        drawCallback: function () {
            var api = this.api()
            
                $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: preparedColumns
    });
}

const getThirdPartiesTable = (tableId, notLab) => {
    const thirdPartiesTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/thirdparties/load/thirdparties',
        paging: true,
        searchDelay: 1500,
        orderMulti: false,
        language: {
            emptyTable: 'No Third Party'
        },
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        language: {
            emptyTable: 'No Third Party Services'
        },
        rowCallback: (row, data) => {
            if (data.delisted) {
                row.classList.add('table-danger')
            }
            return row
        },
        columns: [
            {data: "fullName"},
            {data: "shortName"},
            {data: "phone"},
            {data: "address"},
            {data: "email"},
            {data: "comment"},
            {data: "createdAt"},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-${row.delisted ? 'danger' : 'primary'} delistBtn tooltip-test" title="delist third party" data-id="${ row.id }">
                                ${row.delisted ? '<i class="bi bi-x-square-fill tooltip-test" title="delisted"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="listed"></i>'}
                            </button>
                            <button class="ms-1 btn btn-outline-${row.delisted ? 'danger' : 'primary'} updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-${row.delisted ? 'danger' : 'primary'} deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                        <button class=" btn btn-outline-${row.delisted ? 'danger' : 'primary'} delistBtn tooltip-test" title="delist third party" data-id="${ row.id }">
                            ${row.delisted ? '<i class="bi bi-x-square-fill tooltip-test" title="delisted"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="listed"></i>'}
                        </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    }
                }}
        ]
    });

    return thirdPartiesTable
}
export {getlistOfServicesTable, getThirdPartiesTable}