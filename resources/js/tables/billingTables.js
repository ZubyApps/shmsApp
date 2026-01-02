import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import {admissionStatusX, deferredCondition, displayPaystatus, flagIndicator, flagPatientReason, flagSponsorReason, preSearch, searchDecider, searchMin, searchPlaceholderText, selectReminderOptions, sponsorAndPayPercent, visitType, wardState } from "../helpers";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';


const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getWaitingTable = (tableId) => {
    const waitingTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  '/visits/load/waiting',
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No patient is waiting',
            searchPlaceholder: searchPlaceholderText

        },
        columns: [
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "sex"},
            {data: "age"},
            {data: row => `<div><span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span></div>${row.visitType == 'ANC' ? visitType(row, null, 50) : ''}`},
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
            {data: row => () => {
                const show = row.prescriptions > 0 || row.payments > 0 ? false : true
                return  `
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
                                <a role="button" class="dropdown-item deleteVisitBtn tooltip-test ${show ? '' : 'd-none'}" title="delete visit" id="deleteVisitBtn" data-id="${ row.id }">
                                    <i class="bi bi-x-circle-fill text-primary"></i> Delete Visit
                                </a>
                            </li>
                        </ul>
                    </div>
                        `

            }
                
            },
        ]
    });

    waitingTable.on('draw.init', searchMin(waitingTable, tableId, 2))

    return waitingTable;
}

const getPatientsVisitsByFilterTable = (tableId, filter, urlSuffix, patientId, sponsorId, cardNo, sponsorCat) => {
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
            <div class="dropdown">
                <a class="btn btn-outline-primary tooltip-test text-decoration-none ${row.closed ? 'px-1': ''}" title="${row.closed ? 'record closed by ' + row.closedBy: ''}" data-bs-toggle="dropdown">
                    More${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}
                </a>
                    <ul class="dropdown-menu">
                    <li>
                        <a class=" btn btn-outline-primary dropdown-item consultationDetailsBtn tooltip-test" title="details"  data-id="${ row.id }" data-conid="${ row.conId }" data-patientId="${ row.patient }" data-visitType="${ row.visitType }">
                            Details
                        </a>
                        <a class="dropdown-item patientsBillBtn btn tooltip-test" title="patient's bill"  data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-staff="${ row.staff }">
                            Bill Summary
                        </a>
                        <a class="dropdown-item btn btn-outline-primary medicalReportBtn" data-id="${ row.id }" data-patient="${ row.patient }" data-patientid="${ row.patientId }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-age="${ row.age }" data-sex="${ row.sex }">Report/Refer/Result</a>
                        <a class="dropdown-item ${row.closed ? 'openVisitBtn' : 'closeVisitBtn'} btn tooltip-test" title="${row.closed ? 'open?': 'close?'}"  data-id="${ row.id }" id="${row.closed ? 'openVisitBtn' : 'closeVisitBtn'}">
                            ${row.closed ? 'Open? <i class="bi bi-unlock-fill"></i>': 'Close? <i class="bi bi-lock-fill"></i>'}
                        </a>
                    </li>
                </ul>
            </div>
            `
        },
    ]
    filter === 'Inpatient' ? preparedColumns.splice(6, 0, {data: row => wardState(row)},) : ''

    const patientsVisitisByFilterTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: `/billing/load/${urlSuffix}`, data: {
            'filterBy': filter,
            'patientId': patientId,
            'sponsorId': sponsorId,
            'cardNo': cardNo,
            'sponsorCat': sponsorCat,
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200],
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: urlSuffix == 'openvisits' ? 'No open visits' : 'No patient record',
            searchPlaceholder: searchPlaceholderText
        },
        columns: preparedColumns
    });

    patientsVisitisByFilterTable.on('draw.init', searchDecider(patientsVisitisByFilterTable, tableId, 2))

    return patientsVisitisByFilterTable
}

const getbillingTableByVisit = (tableId, visitId, modal, billing) => {
    const buttonColour = (value) => {return value > 0 ? 'danger' : value === 0 ? 'primary' : 'success'}
    
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
                data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`
            },
            {
                sortable: false,
                data: row => `<span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor +' '+ row.sponsorCategory}</span>`
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
                    const outstanding = row.sponsorCategory === 'NHIS' ? row.outstandingNhisBalance : row.outstandingPatientBalance
                    return `<span class="btn fw-bold text-${buttonColour(outstanding)} outstandingsBtn" data-patientid="${row.patientId}" data-sponsorcat="${row.sponsorCategory}">Patient's Outstanding: ${outstanding}</span>`
                }
            },
            {
                sortable: false,
                data: row => () => {
                    const outstandingSponsor = row.outstandingSponsorBalance
                    const outstandingCardNo = row.outstandingCardNoBalance
                    const allSponsorCategories = ['Family', 'Retainership', 'NHIS', 'Individual']
                    const sponsorsCategories = ['NHIS', 'Individual']
                    if (allSponsorCategories.includes(row.sponsorCategory)){
                        return `<span class="btn fw-bold text-${buttonColour(outstandingSponsor)} sponsorOutstandingsBtn ${sponsorsCategories.includes(row.sponsorCategory) ? 'd-none' : ''}" data-sponsorid="${row.sponsorId}">${row.sponsor + ' ' + row.sponsorCategory}'s Outstanding: ${outstandingSponsor}</span>
                                <span class="btn fw-bold text-${buttonColour(outstandingCardNo)} cardNoOutstandingsBtn ${row.cardNo.includes('ANC') ? 'd-none' : ''}" data-cardno="${row.cardNo}" data-sponsorcat="${row.sponsorCategory}">${row.cardNo}...'s Outstanding: ${outstandingCardNo}</span>
                        `
                    }
                    return ''
                }
            },
        ]
    });

    function format(data) {
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
                                                <td class="">
                                                    <div class="d-flex">
                                                        <span class="${p.isDischarge ? 'changeDischargeBillSpan fw-bold text-primary' : ''} tooltip-test" title="${p.isDischarge ? 'Change Discharge Bill' : ''}" data-id="${p.prescriptionId}">${p.quantity}</span>
                                                        <input class="ms-1 form-control billInput d-none" id="billInput" style="width:6rem;" value="${p.quantity ?? ''}">
                                                    </div>
                                                </td>
                                                <td class="${p.quantity ? 'text-secondary' : 'colour-change2 fw-bold'}">${p.description}</td>                
                                                <td>${account.format(NHIS ? p.nhisBill : p.hmsBill)}</td>
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
                                            ${credit || NHIS ? '<td></td>' : ''}
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary fw-semibold">${NHIS ? 'NHIS Sub total (10%)' : 'Sub total'}</td>
                                            <td class="text-secondary">${account.format(NHIS ? data.nhisSubTotal : data.subTotal)}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? '<td></td>' : ''}
                                            <td></td>
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
                                            ${credit || NHIS ? '<td></td>' : ''}
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary fw-bold">${NHIS ? 'NHIS Net total (10%)' : 'Net total'}</td>
                                            <td class="text-secondary fw-bold">${account.format(NHIS ? data.nhisNetTotal : data.netTotal)}</td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? '<td></td>' : ''}
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary">Paid</td>
                                            <td class="text-secondary">${account.format(data.totalPaid)}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            ${credit || NHIS ? '<td></td>' : ''}
                                            <td></td>
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
                                <button type="button" class="btn btn-${data.reminder ? 'outline-primary' : 'primary registerBillReminderBtn'}" data-id="${data.id}" data-patient="${patient}" data-sponsor="${data.sponsor +' - '+ data.sponsorCategory}">
                                ${data.reminder ? 'Reminder Set' : 'Set Bill Reminder'}
                                </button>
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
        // const tableId = billingTable.table().container().id.split('_')[0]
        billingTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data())).show()
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
                            <button type="button" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        `  
                    }
                    return `
                        <div class="d-flex flex-">
                            <button type="button" class="ms-1 btn btn-outline-primary softDeleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    `
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
                data: row => account.format(row.sponsorCat == 'NHIS' ? row.totalNhisBill : row.totalBill)
            },
            {
                data: row => account.format(row.totalPaid)
            },
            {
                data: row => account.format((row.sponsorCat == 'NHIS' ? row.totalNhisBill : row.totalBill) - row.totalPaid)
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        billTable.destroy()
    })
    
    return billTable
}

const getExpensesTable = (tableId, accessor, expenseCategoryId, modal, startDate, endDate, date, payMethodId) => {
    const expenseTable =  new DataTable(tableId, {
        serverSide: true,
        ajax: {url: '/billing/load/expenses', data: {
            'accessor': accessor,
            'expenseCategoryId' : expenseCategoryId,
            'payMethodId'       : payMethodId,
            'startDate'         : startDate, 
            'endDate'           : endDate,
            'date'              : date,
        }},
        orderMulti: true,
        lengthMenu:[50, 100, 200, 300, 500],
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
            emptyTable: 'No expense',
            searchPlaceholder: searchPlaceholderText
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
            {data: "payMethod"},
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

    if (accessor == 'byExpenseCategory' || accessor == 'byPayMethod'){
        modal._element.addEventListener('hidden.bs.modal', function () {
            expenseTable.destroy()
        })
    }
    expenseTable.on('draw.init', searchMin(expenseTable, tableId, 2))

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
            // {data: row => account.format(row.totalCash)},
            {data: row => `<span class="btn text-decoration-underline showCashPaymentsBtn" title="show cash payments" data-id="${row.id}">${account.format(row.totalCash)}</span>`},
            // {data: row => `<span class="btn text-decoration-underline showExpensesBtn" title="show expenses" data-id="${row.id}">${account.format(row.totalExpense)}</span>`},
            {data: row => account.format(row.totalExpense)},
            {data: row => account.format(row.totalCash - row.totalExpense)},
        ]
    });

    return balancingTable
}

const getBillReminderTable = (tableId, startDate, endDate, date) => {
    const billReimndersTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reminders/load/cash', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
            }
        },
        orderMulti: true,
        search:true,
        searchDelay: 500,
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            },
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
        ],
        lengthMenu:[20, 40, 80, 120, 200],
        columns: [
            {data: "patient"},
            {
                visible: false,
                data: "setFrom"
            },
            {data: row => `<span class="${row.daysAgo > row.maxDays && row.paid == 'Pending' ? 'fw-bold colour-change3' : ''} ${row.paid != 'Pending' ? 'd-none' : ''}">${row.daysAgo}</span>`},
            {data: "maxDays"},
            {data: "daysToPay"},
            {data: row => `<span class="${row.secondReminder ? '' : 'deleteFirstReminderBtn'}" data-id="${ row.id}">${row.firstReminder}</span>`},
            {
                visible: false,
                data: "firstDate"
            },
            {data: row => `<span class="${row.finalReminder ? '' : 'deleteSecondReminderBtn'}" data-id="${ row.id}">${row.secondReminder}</span>`},
            {
                visible: false,
                data: "secondDate"
            },
            {data: row => `<span class="deleteFinalReminderBtn" data-id="${ row.id}">${row.finalReminder}</span>`},
            {
                visible: false,
                data: "finalDate"
            },
            {data: "remind"},
            {data: row => () => {
                return `<span class="fw-bold ${row.paid == 'Pending' ? '' : 'deletePaidBtn'} ${row.paid == 'Pending' ? row.daysAgo > row.maxDays ? 'colour-change3' :'text-warning' : 'text-primary'}" data-id="${row.id}">${row.paid}</span> ${row.paid == 'Pending' ? 
                   `<i class=" bi-dash-circle-fill text-secondary"></i>` : `<i class="ms-1 text-primary bi bi-p-circle-fill tooltip-test" title="paid"></i>`}`
            }},
            {
                visible: false,
                data: "createdAt"
            },
            {
                visible: false,
                data: "setBy"
            },
            {
                visible: false,
                data: "comment"
            },
            {
                visible: false,
                sortable: false,
                data: row => () => {
                        return `
                        <div class="d-flex flex- ${row.paid != 'Pending' ? 'd-none' : ''}">
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBillReminderBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        `  
                }      
            },
        ]
    })

    return billReimndersTable
}

const getDueCashRemindersTable = (tableId) => {
    const dueHmoRemindersTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/reminders/load/cash/due', data: {
        }},
        orderMulti: true,
        search:true,
        searchDelay: 500,
        lengthMenu:[50, 100, 150, 200],
        language: {
            emptyTable: 'No reminders due'
        },
        drawCallback: function (settings) {
            var api = this.api() 
        },
        columns: [
            {data: "patient"},
            {data: "phone"},
            {data: "daysAgo"},
            {data: "maxDays"},
            {data: "firstReminder", 
                render: (data, type, row) => {
                    if (deferredCondition(data)){
                        return data
                    }
                    return  selectReminderOptions(row, 'firstReminderSelect')
                }
            },
            {data: "secondReminder", 
                render: (data, type, row) => {
                    if (deferredCondition(data)){
                        return data
                    }
                    return  selectReminderOptions(row, 'secondReminderSelect')
                }
            },
            {data: "finalReminder", 
                render: (data, type, row) => {
                    if (deferredCondition(data)){
                        return data
                    }
                    return selectReminderOptions(row, 'finalReminderSelect')
                }
            },
            {data: "paid", 
                render: (data, type, row) => {
                    if (data != 'Pending'){
                        return data
                    }
                    return  `<button class="btn btn-primary confirmedPaidBtn" data-id="${row.id}" data-patient="${row.patient}">Pay</button>`  
                    // `<input class="ms-1 form-control confirmedPaidInput text-secondary" type="date" style="width:8rem;" data-id="${row.id}">`
                }
            },
            {data: "comment"},
            {data: "setBy"},
        ]
    });

    return dueHmoRemindersTable
}

export {getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit, getPatientsBill, getExpensesTable, getBalancingTable, getBillReminderTable, getDueCashRemindersTable}