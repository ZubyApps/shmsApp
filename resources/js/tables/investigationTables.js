import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, admissionStatusX, detailsBtn, displayPaystatus, sponsorAndPayPercent } from "../helpers";

const getPatientsVisitsByFilterTable = (tableId, filter) => {
    const preparedColumns = [
        {data: "came"},
        {data: "patient"},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row =>  `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
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
        ajax:  {url: '/investigations/load/consulted', data: {
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

const getInpatientsInvestigationsTable = (tableId, notLab) => {
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
            {data: row => function () {
                const credit = row.sponsorCategoryClass == 'Credit'
                const NHIS = row.sponsorCategory == 'NHIS'
                return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
                }
            },
            {
                sortable: false,
                data: row =>  `
                        <div class="d-flex flex- ${notLab ? 'd-none' : ''}">
                            <button class=" btn btn-outline-primary addResultBtn tooltip-test" id="addResultBtn" title="add result" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                <i class="bi bi-plus-square"></i> Add Result
                            </button>
                        </div>
                `      
            },
        ]
    });

    return investigationsTable
}

const getOutpatientsInvestigationTable = (tableId, notLab) => {
    const investigationsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/investigations/load/outpatients', data: {
            'notLab': notLab
        }},
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
            {data: row => function () {
                const credit = row.sponsorCategoryClass == 'Credit'
                const NHIS = row.sponsorCategory == 'NHIS'
                return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
                }
            },
            {
                sortable: false,
                data: row => function () {
                    if (row.result){
                        return `
                            <div class="d-flex flex-">
                                <button class="btn btn-primary resultAddedBtn tooltip-test" title="result added">
                                <i class="bi bi-check-circle-fill"></i>
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex- ${notLab ? 'd-none' : ''}">
                                <button class="btn btn-outline-primary addResultBtn tooltip-test" id="addResultBtn" title="add result" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                    <i class="bi bi-plus-square"></i> Add Result
                                </button>
                            </div>
                            `
                        }
                    }  
            },
        ]
    });

    return investigationsTable
}

export {getPatientsVisitsByFilterTable, getInpatientsInvestigationsTable, getOutpatientsInvestigationTable}