import jQuery from "jquery";
import jszip, { forEach } from 'jszip';
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
        language: {
            emptyTable: "No patient"
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
        language: {
            emptyTable: 'No patient is waiting'
        },
        columns: [
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "came"},
            {data: row => function () {
                if (row.vitalSigns.length > 0){
                    return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary border-0 vitalSignsBtn tooltip-test" title="Vitals Signs Added" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                            <i class="bi bi-check-circle-fill"></i>
                            </button>
                        </div>`
                    } else {
                        return ``
                    }
                }
            },
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
        language: {
            emptyTable: 'No vital sign has been recorded'
        },
        columns: [
            {data: "created_at"},
            {data: "temperature"},
            {data: "bloodPressure"},
            {data: "pulseRate"},
            {data: "respiratoryRate"},
            {data: "spO2"},
            {data: "weight"},
            {data: "height"},
            {data: "by"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary ${modal._element.id == 'consultationReviewModal' ? 'd-none' : ''} deleteBtn tooltip-test" title="delete" data-id="${ row.id}">
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

const getPrescriptionTableByConsultation = (tableId, conId, modal) => {
    const prescriptionTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/initial', data: {
            'conId': conId,
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No resource has been added'
        },
        columns: [
            {data: "prescribed"},
            {data: "resource"},
            {data: "prescription"},
            {data: "quantity"},
            {data: "note"},
            {data: "by"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                `      
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        prescriptionTable.destroy()
    })

    return prescriptionTable
}

const getLabTableByConsultation = (tableId, conId, modal, viewer) => {
    const investigationTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/lab', data: {
            'conId': conId,
        }},
        paging: true,
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No lab investigation requested'
        },
        columns: [
            // {data: "type"},
            {data: "requested"},
            {data: "resource"},
            {data: "dr"},
            {data: "result"},
            {data: "sent"},
            {data: "staff"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex- ${viewer == 'nurse' ? 'd-none' : ''}">
                    <button type="submit" class="ms-1 btn btn-outline-primary uploadDocBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                    <i class="bi bi-upload"></i>
                    </button>
                </div>
                `      
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        investigationTable.destroy()
    })

    return investigationTable
}

const getTreatmentTableByConsultation = (tableId, conId, modal) => {
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
            {data: row => `<i role="button" class="text-primary display-4 bi bi-prescription2"></i>`},
            {data: "resource"},
            {data: "prescription"},
            {data: "dr"},
            {data: "prescribed"},
            {data: "billed"},
        ]
    });

    function format(d) {
        // `d` is the original data object for the row
        return (
           `<table class="table align-middle table-sm">
           <thead >
               <tr class="">
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

    treatmentTable.on('click', 'tr', function (e) {
        let tr = e.target.closest('tr');
        let row = treatmentTable.row(tr);
     
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
        }
        else {
            // Open this row
            row.child(format(row.data())).show();
        }
    });

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

export {getAllPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getTreatmentTableByConsultation}