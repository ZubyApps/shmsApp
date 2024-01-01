import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { detailsBtn } from "../helpers";

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

const getVerificationTable = (tableId) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/hmo/load/verification/list/',
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No verification requested'
        },
        columns: [
            {data: "came"},
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "doctor"},
            {data: row => 
                        `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary verifyPatientBtn tooltip-test" title="Verify" data-id="${ row.id }" data-patient="${ row.patient }" data-phone="${ row.phone }" data-sponsor="${ row.sponsor }" data-staffid="${ row.staffId }">
                            ${row.status ? row.status : 'Verify'}
                        </button>
                    </div>
                        `
                
            },
        ]
    });
}

const getAllHmoPatientsVisitTable = (tableId, filter) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/load/consulted/', data: {
            'filterBy': filter 
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: "patient"},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: "sponsor"},
            {data: row =>  `
                        <div class="d-flex justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            ${row.labDone}<i class="bi bi-eyedropper"></i>${row.labPrescribed}
                            </button>
                        </div>`                
            },
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
                `<div class="d-flex flex- justify-content-center">
                <span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>
                </div>` :
                `<div class="d-flex flex- justify-content-center">
                <span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>
                </div>`
            } },
            {
                sortable: false,
                data: row => `
                <div class="dropdown">
                    <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="status" data-bs-toggle="dropdown">
                        ${row.closed ? 'Closed': 'Open'}
                    </a>
                        <ul class="dropdown-menu">
                        <li>
                            <a class=" btn btn-outline-primary dropdown-item consultationDetailsBtn tooltip-test" title="details"  data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                                Details
                            </a>
                            <a class="dropdown-item patientBillBtn btn tooltip-test" title="patient's bill"  data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                Bill
                            </a>
                            <a class="dropdown-item openCloseBtn btn tooltip-test" title="${row.closed ? 'open': 'close'}"  data-id="${ row.id }">
                            ${row.closed ? 'Open': 'Close'}
                            </a>
                        </li>
                    </ul>
                </div>
                `
            },
        ]
    });
}

const getApprovalListTable = (tableId) => {
    const prescriptionTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: '/hmo/load/approval/list/',
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No items for approval'
        },
        columns: [
            {data: "patient"},
            {data: "sponsor"},
            {data: "doctor"},
            {data: "prescribed"},
            {data: "diagnosis"},
            {data:row => () => {
                return row.approved ? row.resource + `<i class="ms-1 text-primary bi bi-check-circle-fill"></i>` : 
                       row.rejected ? row.resource + `<i class="ms-1 text-danger bi bi-x-circle-fill"></i>` :
                       row.resource
            }},
            {data: "prescription"},
            {data: "quantity"},
            {data: "hmsBill"},
            {data: "hmsBillDate"},
            {
                sortable: false,
                data: row =>  () => {
                    if (row.approved || row.rejected){
                        return row.approvedBy || row.rejectedBy
                    }
                    return `
                    <div class="d-flex">
                        <button type="submit" class="ms-1 btn btn-outline-primary approveBtn tooltip-test" data-table="${tableId}" title="approve" data-id="${row.id}">
                                <i class="bi bi-check-circle"></i>
                        </button>
                        <button type="submit" class="ms-1 btn btn-outline-danger rejectBtn tooltip-test" data-table="${tableId}" title="reject" data-id="${ row.id}">
                                <i class="bi bi-x-circle"></i>
                        </button>
                        <input class="ms-1 form-control noteInput d-none" id="noteInput">
                    </div>
                    `    
                }  
            },
        ]
    });

    return prescriptionTable
}

const getVisitPrescriptionsTable = (tableId, visitId, modal) => {
    const visitPrescriptionsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/load/visit/prescriptions', data: {
            'visitId': visitId 
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: "No patient"
        },
        drawCallback: function (settings) {
            var api = this.api()                
                $( 'tr:eq(0) td:eq(7)', api.table().footer() ).html(api.column( 7, {page:'current'} ).data().sum());
                $( 'tr:eq(0) td:eq(8)', api.table().footer() ).html( api.column(8, {page:'current'} ).data().sum());
                
                const value = (api.column( 8, {page:'current'} ).data().sum()) - (api.column( 7, {page:'current'} ).data().sum())
                $( 'tr:eq(0) td:eq(9)', api.table().footer() ).html(`<span class="text-${value < 0 ? 'danger': value == 0 ? 'primary': 'success'}">Diff: ${value}</span>`);
                
                $( 'tr:eq(1) td:eq(7)', api.table().footer() ).html(api.data()[0].paidHms);
                $( 'tr:eq(1) td:eq(8)', api.table().footer() ).html(api.data()[0].paidHms);
                
                $( 'tr:eq(2) td:eq(7)', api.table().footer() ).html((api.column( 7, {page:'current'} ).data().sum() - api.data()[0].paidHms));
                $( 'tr:eq(2) td:eq(8)', api.table().footer() ).html((api.column( 8, {page:'current'} ).data().sum() - api.data()[0].paidHms));

        },
        columns: [
            {data: "doctor"},
            {data: "prescribed"},
            {data: row => () => {
                return row.approved ? row.resource + `<i class="ms-1 text-primary bi bi-check-circle-fill"></i>` : 
                       row.rejected ? row.resource + `<i class="ms-1 text-danger bi bi-x-circle-fill"></i>` :
                       row.resource
            } },
            {data: "diagnosis"},
            {data: "prescription"},
            {data: "note"},
            {data: "quantity"},
            {data: "hmsBill",
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                                <span>${data}</span>
                            </div>
                            `}
            },
            {
                data: 'hmoBill',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="${ row.rejected ? '' : 'hmoBillSpan'} btn btn-white" data-id="${row.id}">${row.rejected ? 'Not approved' : data ?? 'Bill'}</span>
                    <input class="ms-1 form-control hmoBillInput d-none" id="hmoBillInput" value="${data ?? ''}">
                </div>
                `}
            },
            {data: "hmoBillBy"},
        ]
    });

    modal._element.addEventListener('hidden.bs.modal', function () {
        visitPrescriptionsTable.destroy()
    })

    return visitPrescriptionsTable
}

export {getWaitingTable, getVerificationTable, getAllHmoPatientsVisitTable, getApprovalListTable, getVisitPrescriptionsTable}