import jQuery from "jquery";
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, displayPaystatus, histroyBtn, reviewBtn, sponsorAndPayPercent } from "../helpers";

const getOutpatientsVisitTable = (tableId, filter) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/doctors/load/consulted/outpatient', data: {
            'filterBy' : filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row =>  `
                        <div class="d-flex justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                             ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
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
            {data: row => admissionStatus(row)},
            {
                sortable: false,
                data: row => reviewBtn(row)
            },
        ]
    });
}

const getInpatientsVisitTable = (tableId, filter) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/doctors/load/consulted/inpatient', data: {
            'filterBy' : filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row =>  `
                        <div class="d-flex flex- justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
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
            {data: row => admissionStatus(row)},
            {
                sortable: false,
                data: row => reviewBtn(row)
            },
        ]
    });
}

const getAncPatientsVisitTable = (tableId, filter) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/doctors/load/consulted/anc', data: {
            'filterBy' : filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row =>  `
                        <div class="d-flex flex- justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
                            </button>
                        </div>`                
            },
            {data: row => function () {
                if (row.ancRegId) {
                    if (row.ancVitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn  tooltip-test" title="${row.ancRegId ? 'add anc vital signs' : 'no anc registeration'}" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-ancregid="${row.ancRegId}" id="ancVitalSignsBtn">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn tooltip-test" title="anc vitalsigns" data-id="${ row.id }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" id="ancVitalSignsBtn">
                                <i class="bi bi-check-circle-fill">${row.ancVitalSigns}</i>
                                </button>
                            </div>`
                        }
                    } else {
                        if (row.vitalSigns < 1){
                            return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="add regular vitalsigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                        <i class="bi bi-plus-square-dotted"></i>
                                    </button>
                                </div>`
                            } else {
                                return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="regular vitalsigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                    <i class="bi bi-check-circle-fill">${row.vitalSigns}</i>
                                    </button>
                                </div>`
                            }
                    }
                }
            },
            {data: row => admissionStatus(row) },
            {
                sortable: false,
                data: row => reviewBtn(row)
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
                if (row.vitalSigns > 0 || row.ancVitalSigns > 0){
                    return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary border-0 vitalSignsBtn tooltip-test" title="Vitals Signs Added">
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
                            <button class=" btn btn-outline-primary consultBtn tooltip-test" title="consult" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}">
                                <i class="bi bi-clipboard2-plus-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary removeBtn tooltip-test" title="remove" data-id="${ row.id }">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>`
                    } else {
                        return `
                        <div class="dropdown">
                            <a class="btn text-black tooltip-test text-decoration-none" title="doctor" data-bs-toggle="dropdown">
                                ${row.doctor}
                                <i class="bi bi-chevron-double-down"> </i>
                            </a>
                                <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item consultBtn btn tooltip-test" title="consult"  data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}">
                                        <i class="bi bi-clipboard2-plus-fill text-primary"></i> Consult
                                    </a>
                                    <a class="dropdown-item removeBtn btn tooltip-test" title="remove"  data-id="${ row.id }">
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

const getVitalSignsTableByVisit = (tableId, visitId, modal, viewer) => {
    const vitalSignsByVisit =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/vitalsigns/load/table', data: {
            'visitId': visitId,
        }},
        orderMulti: false,
        searching: false,
        lengthMenu:[40, 80, 120, 160, 200],
        language: {
            emptyTable: 'No vital sign has been recorded'
        },
        columns: [
            {data: "created_at"},
            {data: row => () => {
                    if (Number(row.temperature?.split('°C')[0]) > 37.2){
                    return  `<span class="text-danger fw-semibold">${row.temperature}</span>` 
                    } else {
                    return row.temperature
                    }
                }
            },
            {data: row => () => {
                    if (Number(row.bloodPressure?.split('/')[0]) > 139){
                    return  `<span class="text-danger fw-semibold">${row.bloodPressure}</span>` 
                    } else {
                    return row.bloodPressure
                    } 
                }
            },
            {data: "pulseRate"},
            {data: "respiratoryRate"},
            {data: "spO2"},
            {data: "weight"},
            {data: "height"},
            {data: row => () => {
                if (Number(row.bmi) > 24.9 || Number(row.bmi) < 18.5){
                return  `<span class="text-danger fw-semibold">${row.bmi}</span>` 
                } else {
                return row.bmi
                } 
            }
            },
            {data: "note"},
            {data: "by"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary ${modal._element.id == 'consultationReviewModal' ? 'd-none' : ''} deleteBtn tooltip-test" title="delete" data-id="${ row.id}" data-patienttype="${row.patientType}">
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
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}" data-conid="${conId}">
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

const getLabTableByConsultation = (tableId, modal, viewer, conId, visitId) => {
    const investigationTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/lab', data: {
            'conId': conId,
            'visitId': visitId,
        }},
        paging: true,
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No lab investigation requested'
        },
        rowCallback: (row, data) => {
            row.classList.add('fw-semibold')
        return row
        },
        columns: [
            {data: "type"},
            {data: row => `<span class="text-${row.rejected ? 'danger' : 'primary'}">${row.resource + ' ' + displayPaystatus(row, (row.payClass == 'Credit'), (row.sponsorCategory == 'NHIS')) }</span>`},
            {data: "dr"},
            {data: "requested"},
            {
                sortable: false,
                data: row =>  `
                        <div class="dropdown ${viewer == 'lab' ? '' :'d-none'}">
                            <i class="btn btn-outline-primary bi bi-gear" role="button" data-bs-toggle="dropdown"></i>

                            <ul class="dropdown-menu">
                                <li class="${row.sent ? 'd-none' : ''}">
                                    <a class="btn btn-outline-primary dropdown-item addResultBtn" id="addResultBtn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-plus-square"></i> Add Result
                                    </a>
                                </li>
                                <li  class="${!row.sent ? 'd-none' : ''}">
                                    <a class="btn btn-outline-primary dropdown-item updateResultBtn" id="updateResultBtn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-table="${tableId}" title="update result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-pencil-fill"></i> Update Result
                                    </a>
                                </li>
                                <li>
                                    <a class="btn dropdown-item upload-result-btn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-table="${tableId}" title="edit result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-upload"></i> Upload Doc
                                    </a>
                                </li>
                                <li>
                                    <a class="btn dropdown-item deleteResultBtn" data-table="${tableId}" title="delete" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-trash3-fill"></i> Delete Result
                                    </a>
                                </li>
                            </ul>
                        </div>
                `      
            },
        ]
    });

    function formatChild(data) {
        // const chart = data.chart
                if (data.result) {
                    return `   
                                <table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td> </td>
                                                    <td class="text-secondary">Sample</td>
                                                    <td class="text-secondary">Result</td>
                                                    <td class="text-secondary">Entered By</td>
                                                    <td class="text-secondary">DateTime</td>
                                                    <td class="text-secondary">Document </td>
                                                </tr>
                                            </thead>
                                        <tbody>
                                             <tr>
                                                <td> </td>
                                                <td class="text-secondary">${data.sample}</td>
                                                <td class="text-secondary">
                                                    <div>${data.result}</div>
                                                </td>
                                                <td class="text-secondary">${data.staff}</td>
                                                <td class="text-secondary">${data.sent}</td>
                                                <td class="text-secondary">${data.doc ?? 'No files'}</td>
                                            </tr>   
                                     </tbody>
                                </table>
                            `
                } else {
                   return  `
                                <table class="table align-middle table-sm">
                                        <tr>
                                            <td align="center" colspan="8" class="text-secondary">
                                                No result
                                            </td>
                                        </tr>
                                    </table>
                            `
                }
            }

    modal.addEventListener('hidden.bs.modal', function () {
        investigationTable.destroy()
    })

    investigationTable.on('draw', function() {
        const tableId = investigationTable.table().container().id.split('_')[0]
        investigationTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(formatChild(row.data(), tableId)).show()
        })
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
            {data: row => `<i role="button" class="text-primary fs-5 bi bi-prescription2"></i>`},
            {data: row => `<span class="text-${row.rejected ? 'danger' : 'primary'}">${row.resource + ' ' + displayPaystatus(row, (row.payClass == 'Credit'), (row.sponsorCategory == 'NHIS')) }</span>`},
            {data: "prescription"},
            {data: "qtyBilled"},
            {data: "prescribedBy"},
            {data: "prescribed"},
            {data: row => () => {
                    return row.qtyBilled ? '<i class=" text-primary bi bi-check-circle-fill"></i>' : '-'
            } },
            {data:  row => () => {
                return row.qtyDispensed ? '<i class=" text-primary bi bi-check-circle-fill"></i>' : '-'
            }},
        ]
    });

    function format(data) {
        const chart = data.chart
                if (chart.length > 0) {
                    let child = `<table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td> </td>
                                                    <td> </td>
                                                    <td class="text-secondary">Charted At</td>
                                                    <td class="text-secondary">Charted By</td>
                                                    <td class="text-secondary">Dose</td>
                                                    <td class="text-secondary">Dose Time</td>
                                                    <td class="text-secondary">Dose Given</td>
                                                    <td class="text-secondary">Time Given</td>
                                                    <td class="text-secondary">Given By</td>
                                                    <td class="text-secondary">Note</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                            
                                chart.forEach(line => {
                                    child += `<tr>
                                                <td> </td>
                                                <td> </td>
                                                <td class="text-secondary">${line.chartedAt}</td>                
                                                <td class="text-secondary">${line.chartedBy}</td>                
                                                <td class="text-secondary">${line.dosePrescribed}</td>
                                                <td class="text-secondary">${line.scheduledTime}</td>
                                                <td class="text-secondary">${line.givenDose}</td>
                                                <td class="text-secondary">${line.timeGiven}</td>
                                                <td class="text-secondary">${line.givenBy}</td>
                                                <td class="text-secondary">${line.note}</td>
                                            </tr>   
                                    `
                                })
                        child += ` </tbody>
                        </table>`
                    return (child);
                } else {
                   let noChild = ``
                   return (noChild)
                }
            }

    modal.addEventListener('hidden.bs.modal', function () {
        treatmentTable.destroy()
    })

    treatmentTable.on('click', 'tr', function (e) {
        let tr = e.target.closest('tr');
        let row = treatmentTable.row(tr);
     
        if (row.child.isShown()) {
            row.child.hide();
            treatmentTable.draw()
        }
        else {
            row.child(format(row.data())).show();
        }
    });

    return treatmentTable
}

const getSurgeryNoteTable = (tableId, conId, view) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/surgerynote/load/details', data: {
            'conId': conId,
        }},
        orderMulti: true,
        searching:false,
        lengthChange: false,
        language: {
            emptyTable: 'No surgery'
        },
        columns: [
            {data: "date"},
            {data: "typeOfOperation"},
            {data: "typeOfAneasthesia"},
            {data: "surgeon"},
            {data: "surgeonsNotes"},
            {data: "postOpNotes"},
            {data: "doctor"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${view ? '' : 'd-none'}">
                    <button class=" btn btn-outline-primary viewSurgeryNoteBtn tooltip-test" title="view" id="viewSurgerNoteBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary updateSurgeryNoteBtn tooltip-test" title="update" id="updateSurgeryNoteBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteSurgeryNoteBtn tooltip-test" title="delete" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });
}

export {getOutpatientsVisitTable, getInpatientsVisitTable, getAncPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getTreatmentTableByConsultation, getSurgeryNoteTable}
