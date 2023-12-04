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
            {data: row => function () {
                if (row.vitalSigns < 1){
                    return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            <i class="bi bi-plus-square-dotted"></i>
                            </button>
                        </div>`
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            <i class="bi bi-check-circle-fill">${row.vitalSigns}</i>
                            </button>
                        </div>`
                    }
                }
            },
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
                if (row.vitalSigns < 1){
                    return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            <i class="bi bi-plus-square-dotted"></i>
                            </button>
                        </div>`
                    } else {
                        return `
                        <div class="dropdown">
                            <a class="text-black tooltip-test text-decoration-none" title="vital signs" data-bs-toggle="dropdown" href="" >
                            <i class="btn btn-outline-primary bi bi-check-circle-fill">${row.vitalSigns}</i>
                            </a>
                                <ul class="dropdown-menu">
                                <li>
                                    <a role="button" class="dropdown-item vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                    <i class="bi bi-plus-square-dotted text-primary"></i> Add
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
            {data: "prescribedBy"},
            {data: "prescribed"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="button" id="chartMedicationBtn" class="btn btn-outline-primary chatMedicationBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}", data-resource="${row.resource}" data-prescription="${row.prescription}" data-prescribedBy="${row.prescribedBy}" data-prescribed="${row.prescribedFormatted}" data-consultation="${row.conId}" data-visit="${row.visitId}">
                        Chart
                    </button>
                </div>
                `      
            },
        ]
    });

    function format(data, tableId) {
        // `d` is the original data object for the row
        const chart = data.chart

                if (chart.length > 0) {
                    let child = `<table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td> </td>
                                                    <td class="text-secondary">Charted At</td>
                                                    <td class="text-secondary">Charted By</td>
                                                    <td class="text-secondary">Dose</td>
                                                    <td class="text-secondary">Schedule Time</td>
                                                    <td class="text-secondary">Dose Given</td>
                                                    <td class="text-secondary">Time Given</td>
                                                    <td class="text-secondary">Given By</td>
                                                    <td class="text-secondary">Action</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                            
                                chart.forEach(line => {
                                    child += `<tr>
                                                <td> </td>
                                                <td class="text-secondary">${line.chartedAt}</td>                
                                                <td class="text-secondary">${line.chartedBy}</td>                
                                                <td class="text-secondary">${line.dosePrescribed}</td>
                                                <td class="text-secondary">${line.scheduledTime}</td>
                                                <td class="text-secondary">${line.givenDose}</td>
                                                <td class="text-secondary">${line.timeGiven}</td>
                                                <td class="text-secondary">${line.givenBy}</td>
                                                <td class="text-secondary"><div class="d-flex flex-">
                                                <button type="button" id="giveMedicationBtn" class="btn btn-primary giveMedicationBtn tooltip-test" title="give medication" data-id="${line.id}" data-table="${tableId}">
                                                    Give
                                                </button>
                                            </div></td>
                                            </tr>   
                                    `
                                })
                        child += ` </tbody>
                        </table>`
                    return (child);
                } else {
                   let noChild = `
                   <table class="table align-middle table-sm">
                        <tr>
                            <td align="center" colspan="8" class="text-secondary">
                                This treatment/medication has not been charted
                            </td>
                        </tr>
                    </table>
                   `
                   return (noChild)
                }
    }

    modal.addEventListener('hidden.bs.modal', function () {
        treatmentTable.destroy()
    })

    treatmentTable.on('draw', function() {
        const tableId = treatmentTable.table().container().id.split('_')[0]
        treatmentTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })
    

    return treatmentTable
}

const getMedicationChartByPrescription = (tableId, prescriptionId, modal) => {
    const medicationChartTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/medicationchart/load/chart', data: {
            'prescriptionId': prescriptionId,
        }},
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No medication has been charted'
        },
        columns: [
            {data: "dose"},
            {data: "scheduledTime"},
            {data: "chartedBy"},
            {data: "chartedAt"},
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
        medicationChartTable.destroy()
    })

    return medicationChartTable
}


export {getWaitingTable, getAllPatientsVisitTable, getNurseTreatmentByConsultation, getMedicationChartByPrescription}


                        // <div class="dropdown">
                        //     <a class="text-black tooltip-test text-decoration-none" title="vital signs" data-bs-toggle="dropdown" href="" >
                        //     <i class="btn btn-outline-primary bi bi-check-circle-fill">${row.vitalSigns}</i>
                        //     </a>
                        //         <ul class="dropdown-menu">
                        //         <li>
                        //             <a role="button" class="dropdown-item vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                        //             <i class="bi bi-plus-square-dotted text-primary"></i> Add
                        //             </a>
                        //         </li>
                        //     </ul>
                        // </div>
                        