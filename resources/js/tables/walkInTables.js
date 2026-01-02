import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
import { admissionStatusX, flagIndicator, flagPatientReason, searchDecider, searchPlaceholderText, sponsorAndPayPercent } from '../helpers';
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getWalkInsTable = (tableId, notLab) => {
    const walkInsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/walkins/load',
        paging: true,
        searchDelay: 500,
        orderMulti: false,
        language: {
            emptyTable: 'No WalkIns'
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
            emptyTable: 'No WalkIns'
        },
        rowCallback: (row, data) => {
                row.classList.add('table-light')
        },
        columns: [
            {data: "createdAt"},
            {data: "name"},
            {data: "age"},
            {data: "sex"},
            {data: "phone"},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    if (row.presCount < 1 || row.payCount < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-primary addPrescriptionBtn tooltip-test" title="add request" data-id="${ row.id }">
                                <i class="bi bi-plus-circle-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-primary addPrescriptionBtn" title="add request" data-id="${ row.id }">
                                <i class="bi bi-plus-circle-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="ms-1 btn btn${row.isLinked ? '-secondary unLinkBtn' : '-outline-primary linkBtn'}" title="Click to ${row.isLinked ? 'unlink' : 'link'}" data-id="${ row.id }" data-name="${row.name}" data-phone="${row.phone}">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    `
                    }
                }}
        ]
    });

    function formatChild(data) {
        const prescriptions = data.prescriptions
        const payments = data.payments
        const payableUser = data.payableUser
        let iteratedPayments = ''
        payments.forEach(payM => {
            iteratedPayments += ` 
                                <tr class="text-secondary">
                                    <td class="text-secondary">${payM.date}</td>
                                    <td class="text-secondary">${payM.receivedBy}</td>
                                    <td class="text-secondary">${payM.payMethod}</td>
                                    <td class="text-secondary">${payM.comment ?? ''}</td>
                                    <td class="text-secondary">${account.format(payM.amount)}</td>
                                    <td>
                                        ${payM.user ?
                                            `<div class="d-flex flex-">
                                                <i class="bi bi-trash3-fill deletePaymentBtn"  title="delete" data-id="${ payM.id}"></i>
                                            </div>
                                            `  
                                            :
                                            `
                                            <div class="d-flex flex-">
                                                <i class="bi bi-x-circle-fill softDeletePaymentBtn" title="delete" data-id="${ payM.id}"></i>
                                            </div>`
                                            }
                                    </td>
                                </tr>
                            `
                        })

                    if (prescriptions.length > 0) {
                        let child =  `   
                                    <table class="table align-middle table-sm table-border">
                                        <span>Request Table</span>
                                                <thead >
                                                    <tr class="fs-italics fw-bold">
                                                        <td class="text-secondary">Date</td>
                                                        <td class="text-secondary">Request By</td>
                                                        <td class="text-secondary">Request</td>
                                                        <td class="text-secondary">Result</td>
                                                        <td class="text-secondary">Result Date</td>
                                                        <td class="text-secondary">Result By</td>
                                                        <td class="text-secondary">Bill</td>
                                                        <td class="text-secondary">Actions</td>
                                                    </tr>
                                                </thead>
                                            <tbody class="fs-italics">`
                                            prescriptions.forEach(p => {
                                                child += `
                                                <tr>
                                                    <td class="text-secondary">${p.requested}</td>
                                                    <td class="text-secondary">${p.requestedBy ?? ''}</td>
                                                    <td class="text-primary fw-semibold">${p.request ?? ''}</td>
                                                    <td class="text-secondary">
                                                        <p>${p.result ?? ''}</p>
                                                    </td>
                                                    <td class="text-secondary">${p.resultDate}</td>
                                                    <td class="text-secondary">${p.resultBy}</td>
                                                    <td class="text-secondary">${account.format(p.bill)}</td>
                                                    <td class="text-secondary">
                                                        <div class="dropdown">
                                                            <i class="btn btn-outline-primary bi bi-gear" role="button" data-bs-toggle="dropdown"></i>

                                                            <ul class="dropdown-menu">
                                                                <li class="${p.resultDate ? 'd-none' : ''}">
                                                                    <a class="btn btn-outline-primary dropdown-item addResultBtn" id="addResultBtn" data-request="${p.request}" data-name="${ data.name }" title="add result" data-id="${ p.id}">
                                                                        <i class="bi bi-plus-square"></i> Add Result
                                                                    </a>
                                                                </li>
                                                                <li  class="${!p.resultDate ? 'd-none' : ''}">
                                                                    <a class="btn btn-outline-primary dropdown-item updateResultBtn" id="updateResultBtn" data-investigation="${p.request}" data-name="${ data.name }" title="update result" data-id="${ p.id}">
                                                                        <i class="bi bi-pencil-fill"></i> Update Result
                                                                    </a>
                                                                </li>
                                                                <li  class="${!p.resultDate ? 'd-none' : ''}">
                                                                    <a class="btn btn-outline-primary dropdown-item printThisBtn" id="printThisBtn" data-investigation="${p.request}" data-name="${ data.name }" data-resultdate="${ p.resultDate }" title="print this" data-id="${ p.id}" data-resultby="${ p.resultBy }">
                                                                        <i class="bi bi-download"></i> Print This
                                                                    </a>
                                                                </li>
                                                                <li  class="${!p.resultDate ? 'd-none' : ''}">
                                                                    <a class="btn btn-outline-primary dropdown-item printAllBtn" id="printAllBtn" data-investigation="${p.request}" data-name="${ data.name }" data-resultdate="${ p.resultDate } title="print all" data-id="${ p.id}" data-resultby="${ p.resultBy }">
                                                                        <i class="bi bi-download"></i> Print All
                                                                    </a>
                                                                </li>
                                                                <li class="${!p.resultDate ? 'd-none' : ''}">
                                                                    <a class="btn dropdown-item deleteResultBtn" data-table="${tableId}" title="delete" data-id="${ p.id}" >
                                                                        <i class="bi bi-trash3-fill"></i> Delete Result
                                                                    </a>
                                                                </li>
                                                                <li class="${p.resultDate ? 'd-none' : ''}">
                                                                    <a class="btn dropdown-item deleteRequestBtn" data-table="${tableId}" title="delete" data-id="${ p.id}" >
                                                                        <i class="bi bi-trash3-fill"></i> Delete Request
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr> 
                                               `
                                            })
                                        child += ` 
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="fw-semibold">Total</td>
                                                        <td class="fw-semibold">${account.format(data.billSum)}</td>
                                                        <td></td>
                                                    </tr>  
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            ${payableUser ? `
                                                                <button class=" btn btn-outline-primary payWalkInBtn tooltip-test" title="pay" data-id="${ data.id }">
                                                                    <i class="bi bi-p-circle-fill"></i>
                                                                </button>
                                                                ` : 'Paid'}
                                                        </td>
                                                        <td class="fw-semibold text-primary">${account.format(data.paidSum)}</td>
                                                        <td>${payableUser ? `
                                                                <button class=" btn btn-outline-primary posWalkinBillBtn tooltip-test" title="pay" data-id="${ data.id }" data-name ="${data.name}" data-staff="${data.staff}">
                                                                    <i class="bi bi-printer"></i>
                                                                </button>
                                                                ` : ''}
                                                        </td>
                                                    </tr>  
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Balance</td>
                                                        <td class="fw-semibold text-${(data.billSum - data.paidSum) > 0 ? 'danger' : 'success'}">${account.format(data.billSum - data.paidSum)}</td>
                                                        <td></td>
                                                    </tr>  
                                                </tbody>
                                        </table>
                                        ${payableUser ? `
                                            <table class="table">
                                                <span>Payment Table</span>
                                                <tbody>
                                                    <tr class="fw-semibold">
                                                        <td class="text-secondary">Date</th>
                                                        <td class="text-secondary">Received By</td>
                                                        <td class="text-secondary">Pay Method</td>
                                                        <td class="text-secondary">Comment</td>
                                                        <td class="text-secondary">Amount</td>
                                                        <td class="text-secondary">Action</td>
                                                    </tr>
                                                        ${iteratedPayments}
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>${account.format(data.paidSum)}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            
                                            ` : ''}
                                        `
                        return child                             
                    } 
                    
                    // else {
                    //    return  `
                    //                 <table class="table align-middle table-sm">
                    //                         <tr>
                    //                             <td align="center" colspan="8" class="text-secondary">
                    //                                 No result
                    //                             </td>
                    //                         </tr>
                    //                     </table>
                    //             `
                    // }
                }
    
        walkInsTable.on('draw', function() {
            const tableId = walkInsTable.table().container().id.split('_')[0]
            walkInsTable.rows().every(function () {
                let tr = $(this.node())
                let row = this.row(tr);
                this.child(formatChild(row.data(), tableId))?.show()
            })
        })

    return walkInsTable
}

const getWalkinsBillPos = (tableId, walkInId, modal, type) => {
    const posBillTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/walkins/summary', data: {
            'walkInId': walkInId,
            'type': type,
        }},
        orderMulti: false,
        search: false,
        searching: false,
        lengthChange: false,
        paging: false,
        info: false,
        scrollX: false,
        language: {
            emptyTable: 'No bill'
        },
        drawCallback: function (settings) {
            var api = this.api()

                const discount = api.data()[0].discount
                const totalPaid = api.data().reduce((sum, item) => sum + Number(item.totalPaid), 0)
                $( 'tr:eq(0) td:eq(2)', api.table().footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
                $( 'tr:eq(2) td:eq(2)', api.table().footer() ).html(account.format(totalPaid));
                $( 'tr:eq(3) td:eq(2)', api.table().footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum() - totalPaid));
                
                $('#'+tableId+' th, #'+tableId + ' td').css({
                    'font-size': '12px', // Set font size for thermal printing
                    'line-height': '1.1',
                    'word-wrap': 'break-word',
                    'white-space': 'normal',
                });
        },
        columnDefs: [
                    { width: '30%', targets: 0 }, // Item column: Wider for wrapping
                    { width: '10%', targets: 1 }, // Qty: Narrow
                    { width: '20%', targets: 2 }, // Price: Narrow
                ],
        columns: [
            {
                data: "service"
            },
            {
                data: "quantity"
            },
            {
                data: row => account.format(row.totalBill)
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        posBillTable.destroy()
    })
    
    return posBillTable
}

const getLinkToVisitsTable = (tableId) => {
    const preparedColumns = [
        {data: "came"},
        {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row => admissionStatusX(row)},
        {
            sortable: false,
            data: row => `
            <div class="d-flex flex-">
                <button class="btn btn-outline-primary tooltip-test linkWalkinToVisitBtn ${row.closed ? 'px-1': ''}" title="Link visit" data-id="${ row.id }" data-visittype="${ row.visitType }"  data-closed="${row.closed}">Link Visit ${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}</button>
            </div>
            `
        },
    ]
    // filter === 'Inpatient' ? preparedColumns.splice(6, 0, {data: row => wardState(row)},) : ''

    const linktoVisitsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: `/patients/load/linktovisits`, data: {}},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200],
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: 'Search for Visits',
            searchPlaceholder: searchPlaceholderText
        },
        columns: preparedColumns
    });

    linktoVisitsTable.on('draw.init', searchDecider(linktoVisitsTable, tableId, 2))

    return linktoVisitsTable
}
export {getWalkInsTable, getWalkinsBillPos, getLinkToVisitsTable}