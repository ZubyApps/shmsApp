import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatusX, detailsBtn, displayPaystatus, flagIndicator, flagPatientReason, getExportOptions, preSearch, searchDecider, searchMin, searchPlaceholderText, sponsorAndPayPercent, wardState } from "../helpers";
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getPatientsVisitByFilterTable = (tableId, filter) => {
    const preparedColumns = [
        {data: "came"},
        {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row =>  `
                    <div class="d-flex flex-">
                        <button class=" btn btn-${row.countPrescribed > row.countBilled ? 'primary' : 'outline-primary'} billingDispenseBtn tooltip-test" title="Billing/Dispense" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${ row.sponsorCategory }">
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
    
    filter === 'Inpatient' ? preparedColumns.splice(7, 0, {data: row => wardState(row)},) : ''

    const allPatientsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/pharmacy/load/consulted',  data: {
            'filterBy': filter 
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200],
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: 'No patient record',
            searchPlaceholder: searchPlaceholderText
        },
        columns: preparedColumns
    });

    allPatientsTable.on('draw.init', searchMin(allPatientsTable, tableId, 2, filter))

    return allPatientsTable
}

const getPrescriptionsByConsultation = (tableId, visitId, modal) => {
    const consultationItemsTable =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/pharmacy/load/consultation/prescriptions', data: {
            'visitId': visitId,
        }},
        paging: true,
        searchDelay: 500,
        orderMulti: false,
        language: {
            emptyTable: 'No Consultations'
        },
        rowCallback: (row, data) => {
                row.classList.add('table-light')
            return row
        },
        columns: [
            {data: "consultBy"},
            {data: row =>  `<span class="text-primary fw-semibold">${row.diagnosis}</span>`},
            {data: "consulted"},
        ]
    });

    function format(data) {
        const credit = data.sponsorCategoryClass == 'Credit'
        const sponsorCat = data.sponsorCategory
        const NHIS = sponsorCat == 'NHIS'
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
                                                    ${credit || NHIS ? '<td class="text-secondary">HMO Approval</td>' : ''}
                                                    <td class="text-secondary">Bill Qty</td>
                                                    <td class="text-secondary"></td>
                                                    <td class="text-secondary">Comment</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                                prescriptions.forEach(p => {
                                        const flag = p.flag.includes(sponsorCat) && (!p.approved && !p.rejected) ? true : false;
                                        totalBill += NHIS && p.approved ? +p.nhisBill : +p.hmsBill
                                        child += `
                                            <tr>
                                                <td class="text-secondary">${count++}</td>
                                                <td class="text-secondary">${p.prescribed}</td>                
                                                <td class="text-secondary">${p.prescribedBy}</td>                
                                                <td class="text-${p.rejected ? 'danger' : 'primary'} fw-semibold">${`<span class="${p.blink ? 'colour-change2' : ''}">${p.item}</span>` +' '+ displayPaystatus(p, credit, NHIS)}</td>                
                                                <td class="text-secondary">${p.prescription}</td>                
                                                <td class="text-secondary">${p.note}</td>
                                                ${credit || NHIS ? `<td class="text-primary fst-italic">${p.hmoNote ? p.statusBy+'-'+p.hmoNote: p.statusBy}</td>` : ''}
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary">
                                                        <span class="${p.qtyDispensed || closed || flag ? '': 'billQtySpan'} btn btn-${p.qtyBilled ? 'white text-secondary' : flag ? 'danger' : 'outline-primary'} tooltip-test" title="${flag ? 'Flagged' : ''}" data-id="${p.id}" data-stock="${p.stock}">${p.qtyBilled ? p.qtyBilled+' '+p.unit : flag ? 'Flagged' : 'Bill'}</span>
                                                        <input class="ms-1 form-control billQtyInput d-none text-secondary" type="number" style="width:6rem;" id="billQtyInput" value="${p.qtyBilled == 0 ? '' : p.qtyBilled}" name="quantity" id="quantity">
                                                        <span class="${closed ? '' : 'holdSpan'} btn btn-${p.reason ? 'danger' : 'outline-primary'} ms-1 ${p.qtyBilled ? 'd-none' : ''}" data-id="${p.id}">${p.reason ? p.reason : 'Hold'}</span>
                                                
                                                        <select class ="form-select form-select-md holdSpanSelect d-none ms-1">
                                                            <option value="">Select Reason</option>
                                                            <option value="Not Paid">Not Paid</option>
                                                            <option value="No Cannulation">No Cannulation</option>
                                                            <option value="Patient Absent">Patient Absent</option>
                                                            <option value="Not Available">Not Available</option>
                                                            <option value="Doctor's Orders">Doctor's Orders</option>
                                                            <option value="Patient Declined">Patient Declined</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="text-secondary"></td>
                                                <td class="text-secondary"></td>
                                            </tr>
                                            <tr class="${p.qtyBilled ? '' : 'd-none'}">
                                                <td class="text-secondary">Price: ${p.hmsBill ? p.price : ''}</td>
                                                <td class="text-secondary fw-semibold">${NHIS && p.approved ? 'NHIS Bill: ' + p.nhisBill : 'Bill ' + p.hmsBill} (paid: ${p.amountPaid})</td>
                                                <td class="text-secondary">Bill by: ${p.hmsBillBy}</td>
                                                <td class="text-secondary">Time: ${p.billed}</td>
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary">
                                                        <span class="${closed ? '' : p.qtyBilled ? 'dispenseQtySpan' : ''} btn btn-${p.qtyDispensed ? 'white text-secondary' : 'outline-primary'}" data-id="${p.id}" data-qtybilled="${p.qtyBilled}" data-stock="${p.stock}">${p.qtyDispensed ? 'Dispensed: '+p.qtyDispensed : 'Dispense'}</span>
                                                        <input class="ms-1 form-control dispenseQtyInput d-none text-secondary" type="number" style="width:6rem;" value="${p.qtyDispensed == 0 ? '' : p.qtyDispensed}" name="quantity" id="quantity">
                                                    </div>
                                                </td>
                                                <td class="text-secondary">By: ${p.dispensedBy}</td>
                                                <td class="text-secondary">Time: ${p.dispensed}</td>
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary">
                                                        <span class="${closed ? '' : 'holdSpan'} btn btn-${p.reason ? 'danger' : 'outline-primary'}" data-id="${p.id}">${p.reason ? p.reason : 'Hold'}</span>
                                                
                                                        <select class ="form-select form-select-md holdSpanSelect d-none">
                                                            <option value="">Select Reason</option>
                                                            <option value="Not Paid">Not Paid</option>
                                                            <option value="No Cannulation">No Cannulation</option>
                                                            <option value="Patient Absent">Patient Absent</option>
                                                            <option value="Not Available">Not Available</option>
                                                            <option value="Doctor's Orders">Doctor's Orders</option>
                                                            <option value="Patient Declined">Patient Declined</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary ${p.qtyBilled ? '' : 'd-none'}">
                                                        <span class="dispenseCommentSpan btn ${p.qtyDispensed && p.qtyBilled !== p.qtyDispensed && !p.dispenseComment ? 'colour-change' : p.dispenseComment ? 'btn-white text-secondary' : 'btn-outline-primary'}" data-id="${p.id}">${p.dispenseComment ? p.dispenseComment : 'Comment'}</span>
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
        // const tableId = consultationItemsTable.table().container().id.split('_')[0]
        consultationItemsTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data())).show()
        })
    })
    
    return consultationItemsTable
}

const getExpirationStockTable = (tableId, filter) => {
    const expirationStockTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax: {url: '/pharmacy/load/expiratonstock', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        search:true,
        searchDelay: 500,
        lengthMenu:[100, 150, 200, 250, 300],
        dom: 'l<"my-5 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            },
            {extend: 'copy', className: 'btn-primary', exportOptions: getExportOptions()},
            {extend: 'csv', className: 'btn-primary', exportOptions: getExportOptions()},
            {extend: 'excel', className: 'btn-primary', exportOptions: getExportOptions()},
            {extend: 'pdfHtml5', className: 'btn-primary', exportOptions: getExportOptions()},
            {extend: 'print', className: 'btn-primary', exportOptions: getExportOptions()},
             ],
        language: {
            emptyTable: 'No Items on this table'
        },
        columns: [
            {data: "name"},
            {data: "location"},
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
            {data: "quantity"},
        ]
    });

    // expirationStockTable.on('click', 'tr', function (e) {
    //     let tr = e.target.closest('tr');
    //     let row = expirationStockTable.row(tr);
    //     row.row(tr).nodes().to$().toggleClass('d-none');
    // });

    return expirationStockTable
}

const getBulkRequestTable = (tableId, urlSuffix) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax: `/bulkrequests/load/${urlSuffix}`,
        orderMulti: true,
        search:true,
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        language: {
            emptyTable: `No ${urlSuffix == 'theatre' ? 'theatre' : 'bulk'} requests`
        },
        columns: [
            {data: "date"},
            {data: "item"},
            {data: "quantity"},
            {data: "dept"},
            {data: "requestedBy"},
            {data: "note"},
            {
                data: 'qtyDispensed',
                render: (data, type, row) => {
                    return ` 
                    <div class="d-flex justify-content-center">
                        <span class="btn ${ row.qtyApproved ? '' : row.marked ? 'theatreQtyBtn' : 'dispenseQtyBtn'}  ${data ? 'btn-white' : 'btn-outline-primary'}" data-id="${row.id}" data-stock="${row.stock}" data-qty="${row.quantity}" data-item="${row.item}">${data ?? (urlSuffix !== 'pharmacy' ? 'Pending' : 'Dispense')}</span>
                        <input type="number" class="ms-1 form-control qtyDispensedInput d-none" id="qtyDispensedInput" value="${data ?? ''}">
                    </div>
                `}
            },
            {data: "dispensed"},
            {data: "dispensedBy"},
            {
                data: 'qtyApproved',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="${ row.qtyDispensed ? 'approveRequestBtn' : ''} btn ${data ? 'btn-white' : 'btn-outline-primary'}" data-id="${row.id}">${ data ?? (urlSuffix !== 'pharmacy' ? 'Pending' : 'Confirm')}</span>
                    <input type="number" class="ms-1 form-control qtyApprovedInput d-none" id="qtyApprovedInput" value="${data ?? row.qtyDispensed ?? ''}">
                </div>
                `}
            },
            {data: "approvedBy"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary ${!row.access || row.dispensed ? 'd-none' : 'deleteRequestBtn'} tooltip-test" title="delete" data-id="${ row.id}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                `      
            },
        ]
    });
}

const getTheatreRequestTable = (tableId, urlSuffix) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax: `/bulkrequests/load/${urlSuffix}`,
        orderMulti: true,
        search:true,
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        language: {
            emptyTable: 'No theatre requests'
        },
        columns: [
            {data: "date"},
            {data: "item"},
            {data: "quantity"},
            {data: "dept"},
            {data: "requestedBy"},
            {data: "note"},
            {
                data: 'qtyDispensed',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="btn ${ row.qtyApproved ? '' : 'dispenseQtyBtn'}  ${data ? 'btn-white' : 'btn-outline-primary'}" data-id="${row.id}" data-stock="${row.stock}">${data ?? (urlSuffix !== 'pharmacy' ? 'Pending' : 'Dispense')}</span>
                    <input class="ms-1 form-control qtyDispensedInput d-none" id="qtyDispensedInput" value="${data ?? ''}">
                </div>
                `}
            },
            // {data: }
            {data: "dispensed"},
            {data: "dispensedBy"},
            {
                data: 'qtyApproved',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="${ row.qtyDispensed ? 'approveRequestBtn' : ''} btn ${data ? 'btn-white' : 'btn-outline-primary'}" data-id="${row.id}">${ data ?? (urlSuffix !== 'pharmacy' ? 'Pending' : 'Confirm')}</span>
                    <input class="ms-1 form-control qtyApprovedInput d-none" id="qtyApprovedInput" value="${data ?? row.qtyDispensed ?? ''}">
                </div>
                `}
            },
            {data: "approvedBy"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary ${!row.access || row.dispensed || urlSuffix !== 'pharmacy' ? 'd-none' : 'deleteRequestBtn'} tooltip-test" title="delete" data-id="${ row.id}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                `      
            },
        ]
    });
}

const getShiftReportTable = (tableId, department, shiftBadgeSpan) => {
    let shiftCount = []
    const shiftReportTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/shiftreport/load', data: {
            'department': department,
        }},
        orderMulti: true,
        searchDelay: 500,
        language: {
            emptyTable: 'No Report'
        },
        rowCallback: (row, data) => {
            if (!data.viewedAt){
                shiftCount.push(row)
            }
        },
        drawCallback: function (settings) {
            if (shiftCount.length){
                shiftBadgeSpan.innerHTML = shiftCount.length
                shiftCount = []
            } else {
                shiftBadgeSpan.innerHTML = ''
            }
        },
        columns: [
            {data: "date"},
            {data: "shift"},
            {data: "writtenBy"},
            {data: "viewedAt"},
            {data: "viewedBy"},
            {data: row => function () {
                return `
                <div class="d-flex flex-">
                <button class=" btn btn-outline-primary viewShiftReportBtn tooltip-test ${row.writtenById == row.userId ? 'd-none' : ''}" title="view" id="viewShiftReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary editShiftReportBtn tooltip-test ${row.writtenById == row.userId ? '' : 'd-none'}" title="edit report" id="editShiftReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteShiftReportBtn tooltip-test ${row.writtenById == row.userId ? '' : 'd-none'}" title="delete" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });

    return shiftReportTable
}

export {getPatientsVisitByFilterTable, getPrescriptionsByConsultation, getExpirationStockTable, getBulkRequestTable, getTheatreRequestTable, getShiftReportTable}