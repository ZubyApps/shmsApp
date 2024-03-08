import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, admissionStatusX, detailsBtn, displayPaystatus, sponsorAndPayPercent } from "../helpers";

const getPatientsVisitByFilterTable = (tableId, filter) => {
    const preparedColumns = [
        {data: "came"},
        {data: "patient"},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row =>  `
                    <div class="d-flex flex-">
                        <button class=" btn btn-${row.countPrescribed > row.countBilled ? 'primary' : 'outline-primary'} billingDispenseBtn tooltip-test" title="Billing/Dispense" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                        ${row.countPrescribed} - ${row.countBilled} - ${row.countDispensed}
                        </button>
                    </div>`                
        },
        {data: row => admissionStatusX(row)},
        {
            sortable: false,
            data: row => detailsBtn(row) 
        },
    ]
    
    filter === 'Inpatient' ? preparedColumns.splice(7, 0, {data: row => `<small>${row.ward + '-' + row.bedNo}</small>`},) : ''

    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/pharmacy/load/consulted',  data: {
            'filterBy': filter 
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No patient record'
        },
        columns: preparedColumns
    });
}

const getPrescriptionsByConsultation = (tableId, visitId, modal) => {
    const consultationItemsTable =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/pharmacy/load/consultation/prescriptions', data: {
            'visitId': visitId,
        }},
        paging: true,
        orderMulti: false,
        language: {
            emptyTable: 'No Consultations'
        },
        columns: [
            {data: "consultBy"},
            {data: row =>  `<span class="text-primary fw-semibold">${row.diagnosis}</span>`},
            {data: "consulted"},
        ]
    });

    function format(data, tableId) {
        // `d` is the original data object for the row
        const credit = data.sponsorCategoryClass == 'Credit'
        const NHIS = data.sponsorCategory == 'NHIS'
        const prescriptions = data.prescriptions
        const closed = +data.closed
        let count = 1
                if (prescriptions.length > 0) {
                    let totalBill = 0
                    let child = `<table class="table align-middle ">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td class="text-secondary">S/N</td>
                                                    <td class="text-secondary">Prescribed</td>
                                                    <td class="text-secondary">Doctor</td>
                                                    <td class="text-secondary">Item</td>
                                                    <td class="text-secondary">Prescription</td>
                                                    <td class="text-secondary">Note</td>
                                                    ${credit ? '<td class="text-secondary">HMO Approval</td>' : ''}
                                                    <td class="text-secondary">Bill Qty</td>
                                                    <td class="text-secondary">Comment</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                                prescriptions.forEach(p => {
                                        totalBill += +p.bill
                                        child += `
                                            <tr>
                                                <td class="text-secondary">${count++}</td>
                                                <td class="text-secondary">${p.prescribed}</td>                
                                                <td class="text-secondary">${p.prescribedBy}</td>                
                                                <td class="text-${p.rejected ? 'danger' : 'primary'} fw-semibold">${p.item +' '+ displayPaystatus(p, credit, NHIS)}</td>                
                                                <td class="text-secondary">${p.prescription}</td>                
                                                <td class="text-secondary">${p.note}</td>
                                                ${credit ? `<td class="text-primary fst-italic">${p.hmoNote ? p.statusBy+'-'+p.hmoNote: p.statusBy}</td>` : ''}
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary">
                                                        <span class="${p.qtyDispensed || closed ? '': 'billQtySpan'} btn btn-${p.qtyBilled ? 'white text-secondary' : 'outline-primary'}" data-id="${p.id}" data-stock="${p.stock}">${p.qtyBilled ? p.qtyBilled+' '+p.unit : 'Bill'}</span>
                                                        <input class="ms-1 form-control billQtyInput d-none text-secondary" type="number" style="width:6rem;" id="billQtyInput" value="${p.qtyBilled ?? ''}" name="quantity" id="quantity">
                                                    </div>
                                                </td>
                                                <td class="text-secondary"></td>
                                            </tr>
                                            <tr class="${p.qtyBilled ? '' : 'd-none'}">
                                                <td class="text-secondary">Price: ${p.bill ? p.price : ''}</td>
                                                <td class="text-secondary fw-semibold">Bill: ${p.bill} (paid: ${p.amountPaid})</td>
                                                <td class="text-secondary">Bill by: ${p.hmsBillBy}</td>
                                                <td class="text-secondary">Time: ${p.billed}</td>
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary">
                                                        <span class="${closed ? '' : p.qtyBilled ? 'dispenseQtySpan' : ''} btn btn-${p.dispensed ? 'white text-secondary' : 'outline-primary'}" data-id="${p.id}" data-qtybilled="${p.qtyBilled}">${p.qtyDispensed ? 'Dispensed: '+p.qtyDispensed : 'Dispense'}</span>
                                                        <input class="ms-1 form-control dispenseQtyInput d-none text-secondary" type="number" style="width:6rem;" value="${p.qtyDispensed ?? ''}" name="quantity" id="quantity">
                                                    </div>
                                                </td>
                                                <td class="text-secondary">By: ${p.dispensedBy}</td>
                                                <td class="text-secondary">Time: ${p.dispensed}</td>
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary ${p.dispensed ? '' : 'd-none'}">
                                                        <span class="dispenseCommentSpan btn btn-${p.dispenseComment ? 'white text-secondary' : 'outline-primary'}" data-id="${p.id}">${p.dispenseComment ? p.dispenseComment : 'Comment'}</span>
                                                        <input class="ms-1 form-control dispenseCommentInput d-none text-secondary" type="text" style="width:4rem;" value="${p.dispenseComment ?? ''}">
                                                    </div>
                                                </td>
                                            </tr>
                                            `
                                    })
                            child += `</tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-secondary fw-bold">Total: ${totalBill}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>`
                    return (child);
                } else {
                   let noChild = `
                   <table class="table align-middle table-sm">
                        <tr>
                            <td align="center" colspan="8" class="text-secondary">
                                No medication or consumable
                            </td>
                        </tr>
                    </table>
                   `
                   return (noChild)
                }
    }

    modal._element.addEventListener('hidden.bs.modal', function () {
        consultationItemsTable.destroy()
    })

    consultationItemsTable.on('draw', function() {
        const tableId = consultationItemsTable.table().container().id.split('_')[0]
        consultationItemsTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })
    
    return consultationItemsTable
}

const getExpirationStockTable = (tableId, filter) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/pharmacy/load/expiratonstock', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No Medications'
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
            {data: "sellingPrice"},
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

const getBulkRequestTable = (tableId, urlSuffix) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax: `/bulkrequests/load/${urlSuffix}`,
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No bulk requests'
        },
        columns: [
            {data: "date"},
            {data: "item"},
            {data: "quantity"},
            {data: "dept"},
            {data: "requestedBy"},
            {data: "note"},
            {
                data: 'qtyApproved',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="${ row.qtyDispensed ? '' : 'approveRequestBtn'} btn ${data ? 'btn-white' : 'btn-outline-primary'}" data-id="${row.id}">${ data ?? (urlSuffix !== 'pharmacy' ? 'Pending' : 'Approve')}</span>
                    <input class="ms-1 form-control qtyApprovedInput d-none" id="qtyApprovedInput" value="${data ?? ''}">
                </div>
                `}
            },
            {data: "approvedBy"},
            {
                data: 'qtyDispensed',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="btn ${ row.qtyApproved && !data ? 'dispenseQtyBtn' : ''} ${data ? 'btn-white' : 'btn-outline-primary'}" data-id="${row.id}" data-qtyapproved="${row.qtyApproved}">${data ?? (urlSuffix !== 'pharmacy' ? 'Pending' : 'Dispense')}</span>
                    <input class="ms-1 form-control qtyDispensedInput d-none" id="qtyDispensedInput" value="${data ?? ''}">
                </div>
                `}
            },
            {data: "dispensed"},
            {data: "dispensedBy"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary ${row.dispensed || urlSuffix !== 'pharmacy' ? 'd-none' : 'deleteRequestBtn'} tooltip-test" title="delete" data-id="${ row.id}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                `      
            },
        ]
    });
}

export {getPatientsVisitByFilterTable, getPrescriptionsByConsultation, getExpirationStockTable, getBulkRequestTable}