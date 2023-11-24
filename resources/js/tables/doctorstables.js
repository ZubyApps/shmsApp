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

const getLabTableByConsultation = (tableId, conId, modal) => {
    const investigationTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/lab', data: {
            'conId': conId,
        }},
        paging: true,
        lengthChange: false,
        searching: false,
        orderMulti: false,
        columns: [
            // {data: "type"},
            {data: "requested"},
            {data: "resource"},
            {data: "dr"},
            {data: "result"},
            {data: "sent"},
            {data: "staff"},
            {data: "doc"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
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
        columns: [
            {data: row => `<i role="button" class="btn btn-outline-primary bi bi-prescription2"></i>`},
            // {data: "category"},
            {data: "resource"},
            {data: "prescription"},
            {data: "dr"},
            {data: "prescribed"},
            {data: "billed"},
            // {
            //     sortable: false,
            //     data: row =>  `
            //     <div class="d-flex flex-">
            //         <button type="submit" class="ms-1 btn btn-outline-primary uploadDocBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
            //         <i class="bi bi-upload"></i>
            //         </button>
            //     </div>
            //     `      
            // },
        ]
    });

    function format(d) {
        // `d` is the original data object for the row
        // console.log(d)
         let things = [ {
            by: "Dr Toby",
            prescribed: "21/11/23 10:47pm",
            prescription: "100mg BD x7/7"    
        }, {
            by: "Mr Nzube",
            prescribed: "22/11/23 8:21pm",
            prescription: "500mg BD x3/7"    
        }
         ]
        return (
           `<table class="table align-middle table-sm">
           <thead >
               <tr class="fw-semibold">
                   <td> </td>
                   <td class="text-primary">Charted</td>
                   <td class="text-primary">By</td>
                   <td class="text-primary">Given</td>
                   <td class="text-primary">Nurse</td>
                   <td class="text-primary">Prescription</td>
                   <td class="text-primary">Dose</td>
               </tr>
           </thead>
           <tbody>
          <tr>
                <td> </td>
                <td>${d.prescribed}</td>
                <td>${d.dr}</td>
                <td>${d.prescribed}</td>
                <td>${d.dr}</td>
                <td>${d.prescription}</td>
                <td>300mg</td>
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

    return treatmentTable
}

export {getAllPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getTreatmentTableByConsultation}