import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, detailsBtn, sponsorAndPayPercent } from "../helpers";

const getPatientsVisitsByFilterTable = (tableId, filter) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/investigations/load/consulted', data: {
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
            {data: row =>  `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            ${row.labDone}<i class="bi bi-eyedropper"></i>${row.labPrescribed}
                            </button>
                        </div>`                
            },
            {data: row => admissionStatus(row)},
            {
                sortable: false,
                data: row => detailsBtn(row) 
            },
        ]
    });
}

const getInpatientsInvestigationsTable = (tableId) => {
    const investigationsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/investigations/load/inpatients',
        paging: true,
        orderMulti: false,
        language: {
            emptyTable: 'No lab investigation requested'
        },
        columns: [
            {data: "requested"},
            {data: "type"},
            {data: "doctor"},
            {data: "patient"},
            {data: "diagnosis"},
            {data: row => `<span class="text-primary fw-semibold">${row.resource}</span>`},
            {
                sortable: false,
                data: row =>  `
                        <div class="dropdown">
                            <i class="btn btn-outline-primary bi bi-gear" role="button" data-bs-toggle="dropdown"></i>

                            <ul class="dropdown-menu">
                                <li class="">
                                    <a class="btn btn-outline-primary dropdown-item addResultBtn" id="addResultBtn" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                        <i class="bi bi-plus-square"></i> Add Result
                                    </a>
                                </li>
                                <li>
                                    <a class="btn dropdown-item edit-result-btn" data-investigation="${row.resource}" data-table="${tableId}" title="edit result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-upload"></i> Upload Doc
                                    </a>
                                </li>
                            </ul>
                        </div>
                `      
            },
        ]
    });

    return investigationsTable
}

const getOutpatientsInvestigationTable = (tableId) => {
    const investigationsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/investigations/load/outpatients',
        paging: true,
        orderMulti: false,
        language: {
            emptyTable: 'No lab investigation requested'
        },
        columns: [
            {data: "requested"},
            {data: "type"},
            {data: "doctor"},
            {data: "patient"},
            {data: "diagnosis"},
            {data: row => `<span class="text-primary fw-semibold">${row.resource}</span>`},
            {
                sortable: false,
                data: row => function () {
                    if (row.result){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary border-0 vitalSignsBtn tooltip-test" title="result added">
                                <i class="bi bi-check-circle-fill"></i>
                                </button>
                            </div>`
                        } else {
                            return ``
                        }
                    }  
            },
        ]
    });

    return investigationsTable
}

export {getPatientsVisitsByFilterTable, getInpatientsInvestigationsTable, getOutpatientsInvestigationTable}