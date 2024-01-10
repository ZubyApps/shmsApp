import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { detailsBtn, displayPaystatus, sponsorAndPayPercent } from "../helpers";

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
            {data: "came"},
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
                    <div class="d-flex flex-">
                        <button class="ms-1 btn btn-outline-primary removeBtn tooltip-test" title="remove" data-id="${ row.id }">
                                <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                        `
                
            },
        ]
    });
}

const getPatientsVisitsByFilterTable = (tableId, filter) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/billing/load/consulted', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No patient record'
        },
        columns: [
            {data: "came"},
            {data: "patient"},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                <button class="btn btn-outline-primary billingDetailsBtn" data-id="${ row.id }" data-patient="${ row.patient }" data-diagnosis="${ row.diagnosis }" data-doctor="${ row.doctor }" data-came="${ row.cameFormatted }" data-sponsor="${ row.sponsor }" data-sponsorcat="${ row.sponsorCategory }">Details</button>
                </div>
                `      
            },
        ]
    });
}

const getbillingTableByVisit = (tableId, visitId, modal) => {
    const billingTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/billing/load/bill', data: {
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
                sortable: false,
                data: "came"
            },
            {
                sortable: false,
                data: "patient"
            },
            {
                sortable: false,
                data: "sponsor"
            },
            {
                sortable: false,
                data: "doctor"
            },
            {
                sortable: false,
                data: "diagnosis"
            },
        ]
    });

    function format(data, tableId) {
        // const HMO = data.sponsorCategory == 'HMO'
        const credit = data.sponsorCategoryClass == 'Credit'
        const NHIS = data.sponsorCategory == 'NHIS'
        const balance = data.sponsorCategory == 'NHIS' ? data.nhisBalance : data.balance
        const prescriptions = data.prescriptions
        let count = 1
                if (prescriptions.length > 0) {
                    let child = `<table class="table align-middle ">
                                            <thead >
                                                <tr class="fw-semibold fst-italic">
                                                    <td class="text-secondary">S/N</td>
                                                    <td class="text-secondary">Prescribed</td>
                                                    <td class="text-secondary">Added by</td>
                                                    <td class="text-secondary">Item</td>
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
                                                <td class="text-${p.rejected ? 'danger' : 'primary'} fw-semibold">
                                                ${p.item +' '+ displayPaystatus(p, credit)}
                                                </td>                
                                                <td class="">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(p.unitPrice)}</td>
                                                <td class="">${p.quantity}</td>
                                                <td class="text-secondary">${p.description}</td>                
                                                <td>${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(p.bill)}</td>
                                            </tr>
                                            `
                                    })
                            child += `</tbody>
                                    <tfoot>
                                        <tr>
                                            <td>     </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary">Sub total</td>
                                            <td class="text-secondary">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(data.subTotal)}</td>
                                        </tr>
                                        ${data.sponsorCategory === 'NHIS' ?
                                         `  <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-secondary">NHIS Sub total (10%)</td>
                                                <td class="text-secondary">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(data.nhisSubTotal)}</td>
                                            </tr>` :
                                         ''}
                                        <tr>
                                            <td>     </td>
                                            <td class=""></td>
                                            <td></td>
                                            <td></td>
                                            <td class="p-1">
                                                <div class="">
                                                    <button class="discountBtn btn btn-outline-secondary m-0" data-id="${data.id}">Discount</button>
                                                    <input class="ms-1 form-control discountInput d-none" id="discountInput" type="number" value="${data.discount}">
                                                </div>
                                            </td>
                                            <td class="text-secondary">${data.discountBy}</td>
                                            <td class="text-secondary">Discount</td>
                                            <td class="text-secondary">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(data.discount)}</td>
                                        </tr>
                                        <tr>
                                            <td>     </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary fw-bold">Net total</td>
                                            <td class="text-secondary fw-bold">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(data.netTotal)}</td>
                                        </tr>
                                        ${data.sponsorCategory === 'NHIS' ?
                                         `  <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-secondary">NHIS Net total (10%)</td>
                                                <td class="text-secondary">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(data.nhisNetTotal)}</td>
                                            </tr>` :
                                         ''}
                                        <tr>
                                        <tr>
                                            <td>     </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-secondary">Paid</td>
                                            <td class="text-secondary">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(data.totalPaid)}</td>
                                        </tr>
                                        <tr>
                                            <td>     </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-${balance ? 'danger' : 'secondary'} fw-bold">Balance</td>
                                            <td class="text-${balance ? 'danger' : 'secondary'} fw-bold">${new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(balance)}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="d-flex justify-content-end paymentDetailsDiv">
                                    <div class="card border-0" style="width: 18rem;">
                                        <div class="toast align-items-center shadow-none border-0" id="savePaymentToast" role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="toast-body">
                                                <h6 class="text-primary">Successful</h6>
                                            </div>  
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item border-0"> Amount <input class="ms-1 form-control amountInput" id="amount" name="amount"></li>
                                            <li class="list-group-item border-0">Pay Method
                                            <select class="form-select form-select-md payMethodInput" name="payMethod" id="payMethod">
                                                <option value="Cash">Cash</option>
                                                <option value="UBA">UBA Pos</option>
                                                <option value="Union Pos">Union Pos</option>
                                                <option value="Ecobank">Ecobank</option>
                                                <option value="Surety">Surety</option>
                                            </select>
                                            </li>
                                            <li class="list-group-item border-0">Comment <input class="ms-1 form-control commentInput" id="comment" name="comment"></li>
                                        </ul>
                                        <div class="card-footer">
                                            <button class="payBtn btn btn-outline-primary" data-id="${data.id}" data-patientid="${data.patientId}">Pay</button>
                                        </div>
                                    </div>
                                </div>
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
        ajax:  {url: '/billing/load/payment', data: {
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
            
                $( api.column(4).footer() ).html( new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(
                    api.column( 4, {page:'current'} ).data().sum())
                );
        },
        columns: [
            {
                data: "date"
            },
            {
                data: "receivedBy"
            },
            {
                data: "payMethod"
            },
            {
                data: "comment"
            },
            {
                data: row => new Intl.NumberFormat('en-US', {currencySign: 'accounting'}).format(row.amount)
            },
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                `      
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        paymentTable.destroy()
    })
    
    return billingTable
}

export {getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit}