import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getDeceasedTable = (tableId, notLab) => {
    const deceasedTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/mortuaryservices/load',
        paging: true,
        searchDelay: 500,
        orderMulti: false,
        language: {
            emptyTable: 'No Deceased'
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
            emptyTable: 'No Deceased'
        },
        rowCallback: (row, data) => {
                row.classList.add('table-light')
        },
        columns: [
            {data: "createdAt"},
            {data: row => `<span class="text-danger fw-semibold">${row.deceasedName}</span>`},
            {data: "sex"},
            {data: "depositor"},
            {data: "depositorPhone"},
            {data: "depositorRship"},
            {data: "dateOfDeposit"},
            {data: "daysSpent"},
            {data: row => function () {
                return `
                    <div class="d-flex">
                        ${ !row.dateCollected ? `
                            <span class="btn btn-outline-primary dateCollectedSpan">Fill Date</span>
                            `
                        : `<div class="dateCollectedSpan" title="${row.dateCollectedBy}">${row.dateCollected}</div>`}
                        <input class="ms-1 form-control dateCollectedInput d-none" type="dateTime-local" style="width:8xrem;" value="${row.dateCollectedRaw ?? ''}" name="dateCollected" id="dateCollected" data-id="${row.id}">
                    </div>
                `
            }},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    if (!row.presCount || !row.payCount) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-primary addBillBtn tooltip-test" title="add request" data-id="${ row.id }">
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
                        <button class=" btn btn-primary addBillBtn tooltip-test" title="add request" data-id="${ row.id }">
                            <i class="bi bi-plus-circle-fill"></i>
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
                                    <table class="table ">
                                        <span>Bill Table</span>
                                                <thead >
                                                    <tr class="fs-italics fw-bold">
                                                        <td class="text-secondary">Date</td>
                                                        <td class="text-secondary">Billed By</td>
                                                        <td class="text-secondary">Item</td>
                                                        <td class="text-secondary">Qty</td>
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
                                                    <td class="text-primary fw-semibold">${p.quantity ?? ''}</td>
                                                    <td class="text-secondary">${account.format(p.bill)}</td>
                                                    <td class="text-secondary">
                                                        <button type="submit" class="ms-1 btn btn-outline-primary deleteBillBtn tooltip-test" title="delete bill" data-id="${ p.id }">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </td>
                                                </tr> 
                                               `
                                            })
                                        child += ` 
                                                    <tr>
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
                                                        <td>
                                                            ${payableUser ? `
                                                                <button class=" btn btn-outline-primary payBillBtn tooltip-test" title="pay" data-id="${ data.id }">
                                                                    <i class="bi bi-p-circle-fill"></i>
                                                                </button>
                                                                ` : 'Paid'}
                                                        </td>
                                                        <td class="fw-semibold text-primary">${account.format(data.paidSum)}</td>
                                                        <td></td>
                                                    </tr>  
                                                    <tr>
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
    
        deceasedTable.on('draw', function() {
            const tableId = deceasedTable.table().container().id.split('_')[0]
            deceasedTable.rows().every(function () {
                let tr = $(this.node())
                let row = this.row(tr);
                this.child(formatChild(row.data(), tableId))?.show()
            })
        })

    return deceasedTable
}
export {getDeceasedTable}