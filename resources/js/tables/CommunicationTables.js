import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
import { showToast } from '../toasts/globalNotificationToasts';
import http from '../http';
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const account = new Intl.NumberFormat('en-US', 
    {
        currencySign: 'accounting', 
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
})

const getListOfSentSmsTable = (tableId) => {
    const preparedColumns = [
        {data: "createdAt"},
        {data: "recipient"},
        {data: "network"},
        {data: "contact"},
        {data: "messageType"},
        {data: "message"},
        {data: "units"},
        {data: "status"},
        {
            sortable: false,
            data: row => function () {
                    return `
                    <div class="d-flex flex-">
                        <button type="submit" class="ms-1 btn btn-outline-primary deleteSmsBtn tooltip-test" title="delete" data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                `
            }}
    ]

    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/communication-services/load/sms', data: {
        }},
        orderMulti: true,
        search:true,
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        language: {
            emptyTable: 'No SMS'
        },
        columns: preparedColumns
    });
}

const getWalletFundingTable = (tableId, notLab) => {
    const walletFundingTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/wallet-funding/load/walletfundings',
        paging: true,
        searchDelay: 500,
        orderMulti: false,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        language: {
            emptyTable: 'No wallet fundings'
        },
        drawCallback: function () {
                    var api = this.api()
                    
                        $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
                        $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
                },
        columns: [
            {data: "createdAt"},
            {data: "amount"},
            {data: "units"},
            {data: "payMethod"},
            {data: "createdBy"},
            {data: row => function() {
                        return `
                            <div class="dropdown">
                                <a class="btn btn-${row.status === 'pending' ? 'warning' : row.status === 'failed' ? 'danger' : 'primary' } tooltip-test text-decoration-none" title="" data-bs-toggle="dropdown">
                                    ${row.statusLabel}
                                </a>
                                    <ul class="${ row.status === 'paid' || !row.admin ? 'd-none' : 'dropdown-menu'}">
                                        <li>
                                            <a class="dropdown-item btn btn-outline-primary updatePaymentStatusBtn" title="details" data-id="${ row.id }">
                                                ${row.status === 'paid' ? 'pending' : 'paid'}
                                            </a>
                                            <a class="dropdown-item updatePaymentStatusBtn btn btn-danger" title=""  data-id="${ row.id }">
                                                failed
                                            </a>
                                        </li>
                                    </ul>
                            </div>
            `
                
                    } 
                    
                },
            {
                sortable: false,
                data: row => function () {
                        return `
                        <div class="d-flex flex-">
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteFundBtn tooltip-test" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                }}
        ]
    });

    return walletFundingTable
}

const getCurrentBalance = (el, show) => {
    http.get(`/communication-services/current-balance`)
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                const balance = response.data
                el.innerHTML = `
                <span class="fw-semibold btn btn-light rounded" id="unitsSpan">Units: 
                    <span class="text-${balance < 100.00 ? 'danger' : 'primary' } ms-1 balanceSpan ">${ account.format(balance, 2) } </span>                
                </span>
                `
                if (show){
                    showToast('Balance reloaded')
                }
            }
        })
        .catch((error) => {
            console.log(error)
        })
}
export {getListOfSentSmsTable, getWalletFundingTable, getCurrentBalance}