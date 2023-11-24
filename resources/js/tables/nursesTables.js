import jQuery from "jquery";
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';


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
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                <button class="btn btn-outline-primary consultationReviewBtn" data-id="${ row.id }" data-patientType="${ row.patientType }">Details</button>
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
            {data: "doctor"},
            {data: row => function () {
                if (row.vitalSigns.length < 1){
                    return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                            <i class="bi bi-plus-square-dotted"></i>
                            </button>
                        </div>`
                    } else {
                        return `
                        <div class="dropdown">
                            <a class="text-black tooltip-test text-decoration-none" title="doctor" data-bs-toggle="dropdown" href="" >
                            <i class="btn btn-outline-primary bi bi-check-circle-fill"></i>
                            </a>
                                <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item vitalSignsBtn tooltip-test" title="Add Vitals Signs"  href="#" data-id="${ row.id }">
                                    <i class="bi bi-plus-square-dotted text-primary"></i> Add
                                    </a>
                                    <a class="dropdown-item deleteBtn tooltip-test" title="remove" href="#" data-id="${ row.id } data-vitalSigns"${row.vitalSigns ?? ''}">
                                        <i class="bi bi-x-circle-fill text-primary"></i> Delete
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


export {getWaitingTable, getAllPatientsVisitTable}