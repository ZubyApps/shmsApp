import jQuery from "jquery";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-select-bs5';
import 'datatables.net-staterestore-bs5';

const getAllPatientsVisitTable = (tableId) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  '/visits/load/consulted',
        orderMulti: true,
        search:true,
        columns: [
            {data: "came"},
            {data: "patient"},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: "sponsor"},
            {data: "status"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                <button class="btn btn-outline-primary consultationReviewBtn" data-id="${ row.id }" data-patientType="${ row.patientType }">Review</button>
                </div>
                `      
            },
        ]
    });
}

const getWaitingTable = (tableId) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  '/visits/load/waiting',
        orderMulti: true,
        search:true,
        columns: [
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "came"},
            {data: row => function () {
                if (row.doctor === ''){
                    return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary consultBtn tooltip-test" title="consult" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                                <i class="bi bi-clipboard2-plus-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary removeBtn tooltip-test" title="remove" data-id="${ row.id }">
                            <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>`
                    } else {
                        return `
                        <div class="dropdown">
                            <a class="text-black tooltip-test text-decoration-none" title="doctor" data-bs-toggle="dropdown" href="" >
                                ${row.doctor}
                                <i class="bi bi-chevron-double-down"> </i>
                            </a>
                                <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item consultBtn tooltip-test" title="consult"  href="#" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                                        <i class="bi bi-clipboard2-plus-fill text-primary"></i> Consult
                                    </a>
                                    <a class="dropdown-item removeBtn tooltip-test" title="remove" href="#" data-id="${ row.id }">
                                        <i class="bi bi-x-circle-fill text-primary"></i> Remove
                                    </a>
                                </li>
                            </ul>
                        </div>
                        `
                    }
                }
            },
        ]
    });
}

const getVitalSignsTableByVisit = (tableId, visitId, modal) => {
    const vitalSignsByVisit =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/vitalsigns/load/visit_vitalsigns', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        search:true,
        columns: [
            {data: "created_at"},
            {data: "temperature"},
            {data: "bloodPressure"},
            {data: "respiratoryRate"},
            {data: "spO2"},
            {data: "pulseRate"},
            {data: "weight"},
            {data: "height"},
            {data: "by"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                `      
            },
        ]
    });

    modal._element.addEventListener('hidden.bs.modal', function () {
        vitalSignsByVisit.destroy()
    })

    return vitalSignsByVisit
}

export {getAllPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit}