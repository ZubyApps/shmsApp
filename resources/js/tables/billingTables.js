import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import {admissionStatusX, displayPaystatus, sponsorAndPayPercent } from "../helpers";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';


const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getWaitingTable = (tableId) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/visits/load/waiting',
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No patient is waiting'
        },
        columns: [
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: row => `<span class="tooltip-test" title="initiated by ${row.initiatedBy}">${row.came}</span>`},
            {data: "waitingFor"},
            {data: "doctor"},
            {data: row => 
                        `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary ${row.vitalSigns ? '' : 'd-none'} tooltip-test" title="View VitalSigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                    </div>
                        `   
            },
            {data: row => 
                        `
                    <div class="dropdown ms-1">
                        <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="remove" data-bs-toggle="dropdown" href="" >
                            <i class="bi bi-file-minus-fill"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a role="button" class="dropdown-item consultationDetailsBtn tooltip-test" title="details" id="consultationDetailsBtn" data-id="${ row.id }">
                                    <i class="bi bi-distribute-vertical text-primary"></i> Details
                                </a>
                            </li>
                            <li>
                                <a role="button" class="dropdown-item closeVisitBtn tooltip-test" title="close visits" id="closeVisitBtn" data-id="${ row.id }">
                                    <i class="bi bi-lock-fill text-primary"></i> Close Visit
                                </a>
                            </li>
                            <li>
                                <a role="button" class="dropdown-item deleteVisitBtn tooltip-test" title="delete visit" id="deleteVisitBtn" data-id="${ row.id }">
                                    <i class="bi bi-x-circle-fill text-primary"></i> Delete Visit
                                </a>
                            </li>
                        </ul>
                    </div>
                        `
                
            },
        ]
    });
}

const getPatientsVisitsByFilterTable = (tableId, filter, urlSuffix, patientId) => {
    const preparedColumns = [
        {data: "came"},
            {data: "patient"},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => admissionStatusX(row)},
            {
                sortable: false,
                data: row => `
                <div class="dropdown">
                    <a class="btn btn-outline-primary tooltip-test text-decoration-none ${row.closed ? 'px-1': ''}" title="${row.closed ? 'record closed by ' + row.closedBy: ''}" data-bs-toggle="dropdown">
                        More${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}
                    </a>
                        <ul class="dropdown-menu">
                        <li>
                            <a class=" btn btn-outline-primary dropdown-item consultationDetailsBtn tooltip-test" title="details"  data-id="${ row.id }" data-conid="${ row.conId }" data-patientId="${ row.patient }" data-patientType="${ row.patientType }">
                                Details
                            </a>
                            <a class="dropdown-item patientsBillBtn btn tooltip-test" title="patient's bill"  data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-staff="${ row.staff }">
                                Bill Summary
                            </a>
                            <a class="dropdown-item closeVisitBtn btn tooltip-test" title="${row.closed ? 'closed': 'close'}"  data-id="${ row.id }">
                            ${row.closed ? '': 'Close'}
                            </a>
                        </li>
                    </ul>
                </div>
                `
            },
    ]
    filter === 'Inpatient' ? preparedColumns.splice(6, 0, {data: row => `<small>${row.ward + '-' + row.bedNo}</small>`},) : ''

    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: `/billing/load/${urlSuffix}`, data: {
            'filterBy': filter,
            'patientId': patientId
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200],
        search:true,
        language: {
            emptyTable: urlSuffix == 'openvisits' ? 'No open visits' : 'No patient record'
        },
        columns: preparedColumns
    });
}

const getbillingTableByVisit = (tableId, visitId, modal, billing) => {
    const billingTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/billing/bill', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        search:true,
        searching: false,
        lengthChange: false,
        language: {
            emptyTable: 'No item has been added'
        },
        rowCallback: (row, data) => {
                row.classList.add('fw-semibold')
            return row
        },
        columns: [
            {
                sortable: true,
                data: "came"
            },
            {
                sortable: false,
                data: "patient"
            },
            {
                sortable: false,
                data: row => row.sponsor +' '+ row.sponsorCategory
            },
            {
                sortable: false,
                data: "doctor"
            },
            {
                sortable: false,
                data: "diagnosis"
            },
            {
                sortable: false,
                data: row => () => {
                    const outstanding = row.sponsorCategory === 'NHIS' ? row.outstandingNhisBalance : row.outstandingBalance
                    return `<span class="btn fw-bold text-${outstanding > 0 ? 'danger' : outstanding === 0 ? 'primary' : 'success'} outstandingsBtn" data-patientid="${row.patientId}">Outstanding: ${outstanding}</span>`
                }
            },
        ]
    });

    function format(data, tableId) {
        const credit        = data.sponsorCategoryClass == 'Credit'
        const NHIS          = data.sponsorCategory == 'NHIS'
        const balance       = data.sponsorCategory == 'NHIS' ? data.nhisBalance : data.balance
        const prescriptions = data.prescriptions
        const payMethods    = data.payMethods
        const notBilled     = data.notBilled
        const patient       = data.patient
        const user          = data.user
        let payMethodOptions = ''
        payMethods.forEach(method => {
            payMethodOptions += `<option value="${method.id}">${method.name}</option>`
        })
        let count = 1
                if (prescriptions.length > 0) {
                    let child = `<table class="table align-middle ">
                                            <thead >
                                                <tr class="fw-semibold fst-italic">
                                                    <td class="text-secondary">S/N</td>
                                                    <td class="text-secondary">Date</td>
                                                    <td class="text-secondary">Added by</td>
                                                    <td class="text-secondary">Item</td>
                                                    ${credit || NHIS ? '<td class="text-secondary">HMO Note</td>' : ''}
                                                    <td class="text-secondary">Unit price</td>
                                                    <td class="text-secondary">Qty</td>
                                                    <td class="text-secondary">Description</td>
                                                    <td class="text-secondary">Bill</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                                prescriptions.forEach(p => {
                                        child += `
                                            <tr>
                                                <td class="text-secondary">${count++}</td>
                                                <td class="text-secondary">${p.prescribed}</td>                
                                                <td class="text-secondary">${p.prescribedBy}</td>
                                                <td class="text-${p.rejected ? 'danger' : 'primary'} fw-semibold tooltip-test ${p.isInvestigation && p.thirdParty == ''  ? 'thirdPartyServiceBtn' : ''}" data-id="${p.prescriptionId}" data-patient="${patient}" data-service="${p.item}" title="${p.thirdParty ? '' : 'initite third party service'}">
                                                    ${p.item +' '+ displayPaystatus(p, credit, NHIS)}
                                                </td>
                                                ${credit || NHIS ? `<td class="text-primary fst-italic">${p.hmoNote ? p.statusBy+'-'+p.hmoNote: p.statusBy}</td>` : ''}                
                                                <td class="">${account.format(p.unitPrice)}</td>
                                                <td class="">${p.quantity}</td>
                                                <td class="${p.quantity ? 'text-secondary' : 'colour-change2 fw-bold'}">${p.description}</td>                
                                                <td>${account.format(p.hmsBill)}</td>
                                            </tr>
                                            `
                                    })
                            child += `</tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? `<td></td>` : ''}
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary fw-semibold">Sub total</td>
                                            <td class="text-secondary">${account.format(data.subTotal)}</td>
                                        </tr>
                                        ${NHIS ?
                                         `  <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-secondary fw-semibold">NHIS Sub total (10%)</td>
                                                <td class="text-secondary">${account.format(data.nhisSubTotal)}</td>
                                            </tr>` :
                                         ''}
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? `<td></td>` : ''}
                                            <td class="p-1">
                                            ${billing ? 
                                                `
                                                <div class="">
                                                    <button class="${user ? 'discountBtn' : ''} btn btn-outline-secondary m-0" data-id="${data.id}">Discount</button>
                                                    <input class="ms-1 form-control discountInput d-none" id="discountInput" type="number" style="width:6rem;" value="${data.discount}">
                                                </div>
                                                ` : ''}
                                            </td>
                                            <td class="text-secondary">${data.discountBy}</td>
                                            <td class="text-secondary">Discount</td>
                                            <td class="text-secondary">${account.format(data.discount)}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? `<td></td>` : ''}
                                            <td></td>
                                            <td class="text-secondary fw-bold">Net total</td>
                                            <td class="text-secondary fw-bold">${account.format(data.netTotal)}</td>
                                        </tr>
                                        ${NHIS ?
                                         `  <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-secondary">NHIS Net total (10%)</td>
                                                <td class="text-secondary">${account.format(data.nhisNetTotal)}</td>
                                            </tr>` :
                                         ''}
                                        <tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? `<td></td>` : ''}
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary">Paid</td>
                                            <td class="text-secondary">${account.format(data.totalPaid)}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? `<td></td>` : ''}
                                            <td class="${notBilled ? 'colour-change2 fw-bold' : ''}">${notBilled ? 'Incomplete Billing' : ''}</td>
                                            <td></td>
                                            <td class="text-${balance > 0 ? 'danger' : balance == 0 ? 'secondary' : 'success'} fw-bold">Balance</td>
                                            <td class="text-${balance > 0 ? 'danger' : balance == 0 ? 'secondary' : 'success'} fw-bold">${account.format(balance)}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                ${billing ? `
                                <div class="d-flex justify-content-end paymentDetailsDiv">
                                    <div class="card border-0" style="width: 18rem;">
                                        <div class="toast align-items-center shadow-none border-0" id="savePaymentToast" role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="toast-body">
                                                <h6 class="text-primary">Successful</h6>
                                            </div>  
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item border-0"> Amount <input class="ms-1 form-control amountInput" id="amount" name="amount" type="number"></li>
                                            <li class="list-group-item border-0">Pay Method
                                            <select class="form-select form-select-md payMethodInput" name="payMethod" id="payMethod">
                                            ${payMethodOptions}
                                            </select>
                                            </li>
                                            <li class="list-group-item border-0">Comment <input class="ms-1 form-control commentInput" id="comment" name="comment"></li>
                                        </ul>
                                        <div class="card-footer">
                                            <button class="payBtn btn btn-outline-primary" data-id="${data.id}" data-patientid="${data.patientId}">Pay</button>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                                `
                    return (child);
                } else {
                   let noChild = `
                   <table class="table align-middle table-sm">
                        <tr>
                            <td align="center" colspan="8" class="text-secondary">
                                No items for this visit
                            </td>
                        </tr>
                    </table>
                    ${billing ? 
                    `
                    <div class="d-flex justify-content-end paymentDetailsDiv">
                                    <div class="card border-0" style="width: 18rem;">
                                        <div class="toast align-items-center shadow-none border-0" id="savePaymentToast" role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="toast-body">
                                                <h6 class="text-primary">Successful</h6>
                                            </div>  
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item border-0"> Amount <input class="ms-1 form-control amountInput" type="number" id="amount" name="amount"></li>
                                            <li class="list-group-item border-0">Pay Method
                                            <select class="form-select form-select-md payMethodInput" name="payMethod" id="payMethod">
                                            ${payMethodOptions}
                                            </select>
                                            </li>
                                            <li class="list-group-item border-0">Comment <input class="ms-1 form-control commentInput" id="comment" name="comment"></li>
                                        </ul>
                                        <div class="card-footer">
                                            <button class="payBtn btn btn-outline-primary" data-id="${data.id}" data-patientid="${data.patientId}">Pay</button>
                                        </div>
                                    </div>
                                </div>
                    ` : ''}
                   `
                   return (noChild)
                }
    }

    billingTable.on('draw', function() {
        const tableId = billingTable.table().container().id.split('_')[0]
        billingTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })

    modal.addEventListener('hidden.bs.modal', function () {
        billingTable.destroy()
    })

    return billingTable
}

const getPaymentTableByVisit = (tableId, visitId, modal) => {
    const paymentTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/billing/payment', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        search: false,
        searching: false,
        lengthChange: false,
        language: {
            emptyTable: 'No payment has been added'
        },
        drawCallback: function () {
            var api = this.api()
            
                $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "receivedBy"},
            {data: "payMethod"},
            {data: "comment"},
            {data: row => account.format(row.amount)},
            {
                sortable: false,
                data: row => () => {
                    if (row.user){
                        return `
                        <div class="d-flex flex-">
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        `  
                    }
                    return ''
                }      
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        paymentTable.destroy()
    })
    
    return paymentTable
}

const getPatientsBill = (tableId, visitId, modal, type) => {
    const billTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/billing/summary', data: {
            'visitId': visitId,
            'type': type,
        }},
        orderMulti: false,
        search: false,
        searching: false,
        lengthChange: false,
        paging: false,
        info: false,
        language: {
            emptyTable: 'No bill'
        },
        drawCallback: function (settings) {
            var api = this.api()
            
                $( 'tr:eq(0) td:eq(3)', api.table().footer() ).html(account.format(api.data()[0].discount));
                $( 'tr:eq(1) td:eq(3)', api.table().footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum() - api.data()[0].discount));
                $( 'tr:eq(1) td:eq(4)', api.table().footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
                $( 'tr:eq(1) td:eq(5)', api.table().footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum() - api.data()[0].discount - api.column( 4, {page:'current'} ).data().sum()));
        },
        columns: [
            {
                data: "service"
            },
            {
                data: "types"
            },
            {
                data: "quantity"
            },
            {
                data: row => account.format(row.totalBill)
            },
            {
                data: row => account.format(row.totalPaid)
            },
            {
                data: row => account.format(row.totalBill - row.totalPaid)
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        billTable.destroy()
    })
    
    return billTable
}

const getExpensesTable = (tableId, accessor, expenseCategoryId, modal, startDate, endDate, date) => {
    const expenseTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/billing/load/expenses', data: {
            'accessor': accessor,
            'expenseCategoryId' : expenseCategoryId,
            'startDate'         : startDate, 
            'endDate'           : endDate,
            'date'              : date,
        }},
        orderMulti: true,
        lengthMenu:[50, 100, 200, 300, 500],
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        language: {
            emptyTable: 'No expense'
        },
        drawCallback: function () {
            var api = this.api()
            
                $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "description"},
            {data: "category"},
            {data: row => account.format(row.amount)},
            {data: "givenTo"},
            {data: "givenBy"},
            {data: "approvedBy"},
            {data: "comment"},
            {
                sortable: false,
                data: row => () => {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary editExpenseBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteExpenseBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        `  
                }      
            },
        ]
    });

    if (accessor == 'byExpenseCategory'){
        modal._element.addEventListener('hidden.bs.modal', function () {
            expenseTable.destroy()
        })
    }
    
    return expenseTable
}

const getBalancingTable = (tableId, accessor, date) => {
    const balancingTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/billing/load/balancing', data: {
            'accessor': accessor,
            'date'    : date,
        }},
        orderMulti: true,
        searching: false,
        lengthChange: false,
        columns: [
            {data: "date"},
            {data: row => account.format(row.totalCash)},
            {data: row => account.format(row.totalExpense)},
            {data: row => account.format(row.totalCash - row.totalExpense)},
        ]
    });

    return balancingTable
}

export {getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit, getPatientsBill, getExpensesTable, getBalancingTable}