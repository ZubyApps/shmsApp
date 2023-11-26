import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';


const getAllPatientsVisitTable = (tableId) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  '/visits/load/consulted/nurses',
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
            {data: row => () => {
                return row.admissionStatus == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                <button class="btn btn-outline-primary consultationDetailsBtn" data-id="${ row.id }" data-patientType="${ row.patientType }">Details</button>
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

const getNurseTreatmentByConsultation = (tableId, conId, modal) => {
    const treatmentTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/treatment', data: {
            'conId': conId,
        }},
        paging: true,
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No medication or treatment prescribed'
        },
        rowCallback: (row, data) => {
                row.classList.add('fw-semibold')
            return row
        },
        columns: [
            {data: "resource"},
            {data: "prescription"},
            {data: "dr"},
            {data: "prescribed"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="button" id="chartMedicationBtn" class="btn btn-outline-primary chatMedicationBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                        Chart
                    </button>
                </div>
                `      
            },
        ]
    });

    function format(d) {
        // `d` is the original data object for the row
        return (
           `<table class="table align-middle table-sm">
           <thead >
               <tr class="fw-semibold fs-italics">
                   <td> </td>
                   <td class="text-secondary">Charted</td>
                   <td class="text-secondary">By</td>
                   <td class="text-secondary">Given</td>
                   <td class="text-secondary">Nurse</td>
                   <td class="text-secondary">Prescription</td>
                   <td class="text-secondary">Dose</td>
               </tr>
           </thead>
           <tbody>
          <tr>
                <td> </td>
                <td class="text-secondary">${d.prescribed}</td>
                <td class="text-secondary">${d.dr}</td>
                <td class="text-secondary">${d.prescribed}</td>
                <td class="text-secondary">${d.dr}</td>
                <td class="text-secondary">${d.prescription}</td>
                <td class="text-secondary">300mg</td>
            </tr>
           
           </tbody>`
        );
    }

    modal.addEventListener('hidden.bs.modal', function () {
        treatmentTable.destroy()
    })


    treatmentTable.on('draw', function() {
        console.log('reached')
        treatmentTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data())).show()
        })
    })
    

    return treatmentTable
}


export {getWaitingTable, getAllPatientsVisitTable, getNurseTreatmentByConsultation}