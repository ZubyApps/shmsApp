import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { detailsBtn } from "../helpers";

const getAllRegularPatientsVisitTable = (tableId) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/investigations/load/consulted/regular/lab',
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
            {data: "sponsor"},
            {data: row =>  `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            ${row.labDone}<i class="bi bi-eyedropper"></i>${row.labPrescribed}
                            </button>
                        </div>`                
            },
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {
                sortable: false,
                data: row => detailsBtn(row) 
            },
        ]
    });
}

const getInpatientsVisitTable = (tableId) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/investigations/load/consulted/inpatient/lab',
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
            {data: "sponsor"},
            {data: row =>  `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            ${row.labDone}<i class="bi bi-eyedropper"></i>${row.labPrescribed}
                            </button>
                        </div>`                
            },
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {
                sortable: false,
                data: row => detailsBtn(row)
            },
        ]
    });
}

const getAncPatientsVisitTable = (tableId) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/investigations/load/consulted/anc/lab',
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
            {data: "sponsor"},
            {data: row =>  `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            ${row.labDone}<i class="bi bi-eyedropper"></i>${row.labPrescribed}
                            </button>
                        </div>`                
            },
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {
                sortable: false,
                data: row => detailsBtn(row)  
            },
        ]
    });
}

export {getAllRegularPatientsVisitTable, getAncPatientsVisitTable, getInpatientsVisitTable}