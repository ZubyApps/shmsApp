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
        ajax:  '/hmo/load/verification',
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No patient is waiting'
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
                            Verify
                        </button>
                    </div>
                        `
                
            },
        ]
    });
}

const getAllPatientsVisitTable = (tableId, filter) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/visits/load/consulted/', data: {
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
            {data: row => function () {
                   return `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="View VitalSigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                        <i class="bi bi-check-circle-fill">${row.vitalSigns}</i>
                        </button>
                    </div>`
                }
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
                data: row => detailsBtn(row)
            },
        ]
    });
}

const getApprovalListTable = (tableId) => {
    const prescriptionTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: '/prescription/load/hmo',
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No prescriptions for approval'
        },
        columns: [
            {data: "patient"},
            {data: "sponsor"},
            {data: "doctor"},
            {data: "prescribed"},
            {data: "diagnosis"},
            {data: "resource"},
            {data: "prescription"},
            {data: "quantity"},
            {data: "bill"},
            {data: "billed"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex">
                    <button type="submit" class="ms-1 btn btn-outline-primary approveBtn tooltip-test" data-table="${tableId}" title="approve" data-id="${row.id}">
                            <i class="bi bi-check-circle"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary rejectBtn tooltip-test" data-table="${tableId}" title="reject" data-id="${ row.id}">
                            <i class="bi bi-x-square"></i>
                    </button>
                    <input class="ms-1 form-control commentInput d-none" id="rejectInput">
                </div>
                `      
            },
        ]
    });

    return prescriptionTable
}

export {getWaitingTable, getVerificationTable, getAllPatientsVisitTable, getApprovalListTable}