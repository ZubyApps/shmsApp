import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, detailsBtn, displayPaystatus, getMinsDiff, histroyBtn, prescriptionStatusContorller, sponsorAndPayPercent } from "../helpers";

const getWaitingTable = (tableId) => {
    return new DataTable('#'+tableId, {
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
                if (row.patientType == 'ANC'){
                    return ''
                } else {
                    if (row.vitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                    <i class="bi bi-plus-square-fill"></i>
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
                                        <i class="bi bi-plus-square-fill text-primary"></i> Add
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            `
                        }

                    }
                }
            },
        ]
    });
}

const getPatientsVisitsByFilterTable = (tableId, filter) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/nurses/load/consulted/nurses', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No patient record'
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => function () {
                const chartables = row.chartableMedications
                    return `
                    <div class="d-flex flex">
                        <button class=" btn btn${chartables < 1 ? '-outline' : ''}-primary viewMedicationBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor + ' - ' + row.sponsorCategory }">
                            ${(chartables < 1 ? '' : chartables) + ' ' + row.givenCount + '/' + row.doseCount}
                        </button>
                    </div>`
                }
            },
            {data: row => function () {
                const chartables = row.otherChartables
                if (row.patientType == 'ANC') {
                        return `
                        <div class="d-flex flex" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor + ' - ' + row.sponsorCategory }" data-patientid="${ row.patientId }" data-ancregid="${ row.ancRegId }">
                                ${ row.ancRegId ? '<i class="btn btn-outline-primary bi bi-check-circle-fill tooltip-test" title="view" id="viewRegisterationBtn"></i> <i class="ms-1 btn btn-outline-primary bi bi-pencil-fill tooltip-test" title="edit" id="editRegisterationBtn"></i>': '<i class="btn btn-outline-primary bi bi-plus-square ancRegisterationBtn tooltip-test" title="register"></i>' }
                        </div>`
                    } else {
                        return `
                        <div class="d-flex flex">
                            <button class=" btn btn${chartables < 1 ? '-outline' : ''}-primary viewOtherPrescriptionsBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor + ' - ' + row.sponsorCategory }">
                                ${(chartables < 1 ? '' : chartables) + ' ' + row.doneCount + '/' + row.scheduleCount}
                            </button>
                        </div>`
                    }
                }
            },
            {data: row => function () {
                if (row.patientType == 'ANC') {
                    if (row.ancVitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-${row.ancRegId ? 'primary ancVitalSignsBtn' : 'secondary'}  tooltip-test" title="${row.ancRegId ? 'add vital signs' : 'no anc registeration'}" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-ancregid="${row.ancRegId}" id="ancVitalSignsBtn">
                                ${ row.ancRegId ? '<i class="bi bi-plus-square-fill"></i>' :'<i class="bi bi-x-square-fill"></i>'}
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" id="ancVitalSignsBtn">
                                <i class="bi bi-check-circle-fill">${row.ancVitalSigns}</i>
                                </button>
                            </div>`
                        }
                    } else {
                        if (row.vitalSigns < 1){
                            return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                                        <i class="bi bi-plus-square-fill"></i>
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
                }
            },
            {data: row => admissionStatus(row)},
            {
                sortable: false,
                data: row => detailsBtn(row)},
        ]
    });
}

const getNurseMedicationsByFilter = (tableId, conId, modal, visitId, isHistory) => {
    console.log(isHistory)
    const medicationTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/medications', data: {
            'conId': conId,
            'visitId': visitId
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
            {data: row => `<span class="text-${row.rejected ? 'danger' : 'primary'}">${row.resource + ' ' + displayPaystatus(row, (row.payClass == 'Credit'), (row.sponsorCategory == 'NHIS')) }</span>`},
            {data: row => prescriptionStatusContorller(row, tableId)},
            {data: "qtyBilled"},
            {data: "prescribedBy"},
            {data: "prescribed"},
            {data: "note"},
            {data: row =>  row.chartable ? 'Yes' : 'No'},
            {
                sortable: false,
                data: row =>
                `
                <div class="d-flex flex- ${row.closed || !row.chartable || isHistory ? 'd-none' : ''}">
                    ${row.doseComplete ? 'Complete' : row.discontinued ? 'Discontinued' : `
                        <button type="button" id="chartMedicationBtn" class="btn btn${row.medicationCharts.length ? '' : '-outline'}-primary chatMedicationBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}", data-resource="${row.resource}" data-prescription="${row.prescription}" data-prescribedBy="${row.prescribedBy}" data-patient="${row.patient}" data-sponsor="${row.sponsor}" data-prescribed="${row.prescribedFormatted}" data-consultation="${row.conId}" data-visit="${row.visitId}">
                            ${row.medicationCharts.length ? row.doseComplete ? 'Complete' : 'Charted' : 'Create'}
                        </button>`
                    }
                </div>
                `      
            },
        ]
    });

    function format(data, tableId) {
        const chart = data.medicationCharts
        const discontinued = data.discontinued
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
                                                    <td class="text-secondary">Note</td>
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
                                                <td class="text-secondary">${line.note}</td>
                                                <td class="text-secondary">
                                                    ${isHistory ? '' : `
                                                        <div class="d-flex flex-">
                                                            ${line.status ? `
                                                                <button type="button" id="deleteGivenBtn" class="btn btn-primary deleteGivenBtn tooltip-test" title="remove record" data-id="${line.id}" data-table="${tableId}">
                                                                    </i> <i class="bi bi-x-circle-fill deleteGivenBtn"></i>
                                                                </button>
                                                                ` : `
                                                                ${discontinued ? 'Discontinued' : `
                                                                    <button type="button" id="giveMedicationBtn" class="btn btn-primary giveMedicationBtn tooltip-test" title="give medication" data-id="${line.id}" data-table="${tableId}" data-dose="${line.dosePrescribed}" data-prescription="${data.prescription}" data-treatment="${data.resource}" data-patient="${line.patient}">
                                                                    ${line.doseCount}<i class="bi bi-clipboard-plus"></i>${line.count}
                                                                    </button>` }
                                                                `}
                                                        </div>
                                                    `}
                                                </td>
                                            </tr>   
                                    `
                                })
                        child += ` </tbody>
                        </table>`
                    return (child);
                } else { 
                    if (!data.chartable) {
                        let notApplicable = `
                        <table class="table align-middle table-sm">
                             <tr>
                                 <td align="center" colspan="8" class="text-secondary">
                                    Not chartable
                                 </td>
                             </tr>
                         </table>
                        `
                        return notApplicable
                    }
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
        medicationTable.destroy()
    })

    medicationTable.on('draw', function() {
        const tableId = medicationTable.table().container().id.split('_')[0]
        medicationTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })
    
    return medicationTable
}

const getOtherPrescriptionsByFilterNurses = (tableId, conId, modal, visitId, isHistory) => {
    console.log(isHistory)
    const medicationTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/others', data: {
            'conId': conId,
            'visitId': visitId
        }},
        paging: true,
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No prescription'
        },
        rowCallback: (row, data) => {
                row.classList.add('fw-semibold')
            return row
        },
        columns: [
            {data: row => `<span class="text-${row.rejected ? 'danger' : 'primary'}">${row.resource + ' ' + displayPaystatus(row, (row.payClass == 'Credit'), (row.sponsorCategory == 'NHIS')) }</span>`},
            {data: row => prescriptionStatusContorller(row, tableId)},
            {data: "qtyBilled"},
            {data: "prescribedBy"},
            {data: "note"},
            {data: row =>  row.chartable ? 'Yes' : 'No'},
            {
                sortable: false,
                data: row =>
                `
                <div class="d-flex flex- ${row.closed || !row.chartable ? 'd-none' : ''}">
                    ${row.doseComplete ? 'Complete' : row.discontinued ? 'Discontinued' : `
                        <button type="button" id="chartPrescriptionBtn" class="btn btn${row.prescriptionCharts.length ? '' : '-outline'}-primary chartPrescriptionBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}", data-resource="${row.resource}" data-prescription="${row.prescription}" data-prescribedBy="${row.prescribedBy}" data-patient="${row.patient}" data-sponsor="${row.sponsor}" data-prescribed="${row.prescribedFormatted}" data-consultation="${row.conId}" data-visit="${row.visitId}">
                            ${row.prescriptionCharts.length ? row.doseComplete ? 'Complete' : 'Charted' : 'Create'}
                        </button>`
                    }
                </div>
                `      
            },
        ]
    });

    function format(data, tableId) {
        const chart = data.prescriptionCharts
        const discontinued = data.discontinued
                if (chart.length > 0) {
                    let child = `<table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td> </td>
                                                    <td class="text-secondary">Charted At</td>
                                                    <td class="text-secondary">Charted By</td>
                                                    <td class="text-secondary">Instruction</td>
                                                    <td class="text-secondary">Schedule Time</td>
                                                    <td class="text-secondary">Report</td>
                                                    <td class="text-secondary">Time Done</td>
                                                    <td class="text-secondary">Done By</td>
                                                    <td class="text-secondary">Action</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                            
                                chart.forEach(line => {
                                    child += `<tr>
                                                <td> </td>
                                                <td class="text-secondary">${line.chartedAt}</td>                
                                                <td class="text-secondary">${line.chartedBy}</td>                
                                                <td class="text-secondary">${line.carePrescribed}</td>
                                                <td class="text-secondary">${line.scheduledTime}</td>
                                                <td class="text-secondary">${line.note}</td>
                                                <td class="text-secondary">${line.timeDone}</td>
                                                <td class="text-secondary">${line.doneBy}</td>
                                                <td class="text-secondary">
                                                    ${isHistory ? '' : `
                                                        <div class="d-flex flex-">
                                                            ${line.status ? `
                                                                <button type="button" id="deleteGivenBtn" class="btn btn-primary deleteGivenBtn tooltip-test" title="remove record" data-id="${line.id}" data-table="${tableId}">
                                                                    </i> <i class="bi bi-x-circle-fill deleteGivenBtn"></i>
                                                                </button>
                                                                ` : `
                                                                ${discontinued ? 'Discontinued' : `
                                                                    <button type="button" id="reportServiceBtn" class="btn btn-primary reportServiceBtn tooltip-test" title="Report Service" data-id="${line.id}" data-table="${tableId}" data-care="${line.carePrescribed}" data-instruction="${line.instruction}" data-prescription="${data.prescription}" data-treatment="${data.resource}" data-patient="${line.patient}">
                                                                    ${line.scheduleCount}<i class="bi bi-clipboard-plus"></i>${line.count}
                                                                    </button>` }
                                                                `}
                                                        </div>
                                                    `}
                                                </td>
                                            </tr>   
                                    `
                                })
                        child += ` </tbody>
                        </table>`
                    return (child);
                } else { 
                    if (!data.chartable) {
                        let notApplicable = `
                        <table class="table align-middle table-sm">
                             <tr>
                                 <td align="center" colspan="8" class="text-secondary">
                                    Not chartable
                                 </td>
                             </tr>
                         </table>
                        `
                        return notApplicable
                    }
                   let noChild = `
                   <table class="table align-middle table-sm">
                        <tr>
                            <td align="center" colspan="8" class="text-secondary">
                                This service has not been charted
                            </td>
                        </tr>
                    </table>
                   `
                   return (noChild)
                }
    }

    modal.addEventListener('hidden.bs.modal', function () {
        medicationTable.destroy()
    })

    medicationTable.on('draw', function() {
        const tableId = medicationTable.table().container().id.split('_')[0]
        medicationTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })
    
    return medicationTable
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
                data: row => () => {
                    if (row.given){
                        return 'Served'
                    }
                    return `
                    <div class="d-flex flex-">
                        <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                    `      
                } 
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        medicationChartTable.destroy()
    })

    return medicationChartTable
}

const getPrescriptionChartByPrescription = (tableId, prescriptionId, modal) => {
    const medicationChartTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/nursingchart/load/chart', data: {
            'prescriptionId': prescriptionId,
        }},
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No service has been charted'
        },
        columns: [
            {data: "service"},
            {data: "scheduledTime"},
            {data: "chartedBy"},
            {data: "chartedAt"},
            {
                sortable: false,
                data: row => () => {
                    if (row.given){
                        return 'Served'
                    }
                    return `
                    <div class="d-flex flex-">
                        <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                    `      
                } 
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        medicationChartTable.destroy()
    })

    return medicationChartTable
}

const getUpcomingMedicationsTable = (tableId, bsComponent, type) => {
    const allMedicationChartTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: '/medicationchart/load/upcoming',
        orderMulti: false,
        lengthMenu:[20, 40, 60, 80, 100],
        language: {
            emptyTable: 'No medication has been charted'
        },
        rowCallback: (row, data) => {
            const diff = getMinsDiff(new Date(), new Date(data.rawDateTime))
            if (diff > 15 && diff < 30){
                setInterval(() => {row.classList.toggle('table-warning')}, 1000)
            } 
            if (diff < 15){
                setInterval(() => {row.classList.toggle('table-danger')}, 500)
            }
    },
        columns: [
            {data: "patient"},
            {data: row => () => {
                return row.status == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {data: "ward"},
            {data: "treatment"},
            {data: "prescription"},
            {data: "chartedBy"},
            {data: "date"},
            {data: "time"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                ${row.discontinued ? 'Discontinued' : `
                    <button type="submit" id="giveMedicationBtn" class="ms-1 btn btn-primary giveMedicationBtn tooltip-test" data-table="${tableId}" title="give medication" data-id="${ row.id}" data-dose="${row.dose}" data-prescription="${row.prescription}" data-patient="${row.patient}" data-treatment="${row.treatment}">
                    ${row.doseCount}<i class="bi bi-clipboard-plus"></i>${row.count}
                    </button>`
                }
                </div>
                `      
            },
        ]
    });

    bsComponent.addEventListener(`hidden.bs.${type}`, function () {
        for (var i = 1; i < 99999; i++)
        window.clearInterval(i);
    })

    return allMedicationChartTable
}

const getUpcomingNursingChartsTable = (tableId, bsComponent, type) => {
    const allNursingChartsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax: '/nursingchart/load/upcoming',
        orderMulti: false,
        lengthMenu:[20, 40, 60, 80, 100],
        language: {
            emptyTable: 'No service has been charted'
        },
        rowCallback: (row, data) => {
            const diff = getMinsDiff(new Date(), new Date(data.rawDateTime))
            if (diff > 15 && diff < 30){
                setInterval(() => {row.classList.toggle('table-warning')}, 1000)
            } 
            if (diff < 15){
                setInterval(() => {row.classList.toggle('table-danger')}, 500)
            }
    },
        columns: [
            {data: "patient"},
            {data: row => () => {
                return row.status == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {data: "ward"},
            {data: "care"},
            {data: "instruction"},
            {data: "chartedBy"},
            {data: "date"},
            {data: "time"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                ${row.discontinued ? 'Discontinued' : `
                    <button type="submit" id="reportServiceBtn" class="ms-1 btn btn-primary reportServiceBtn tooltip-test" data-table="${tableId}" title="Report Service" data-id="${ row.id}" data-dose="${row.dose}" data-instruction="${row.instruction}" data-patient="${row.patient}" data-care="${row.care}">
                    ${row.scheduleCount}<i class="bi bi-clipboard-plus"></i>${row.count}
                    </button>`
                }
                </div>
                `      
            },
        ]
    });

    bsComponent.addEventListener(`hidden.bs.${type}`, function () {
        for (var i = 1; i < 99999; i++)
        window.clearInterval(i);
    })

    return allNursingChartsTable
}

const getDeliveryNoteTable = (tableId, conId, view) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/deliverynote/load/details', data: {
            'conId': conId,
        }},
        orderMulti: true,
        searching:false,
        lengthChange: false,
        language: {
            emptyTable: 'No delivery'
        },
        columns: [
            {data: "date"},
            {data: "timeAdmitted"},
            {data: "timeDelivered"},
            {data: "modeOfDelivery"},
            {data: "ebl"},
            {data: "nurse"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${view || row.closed ? '' : 'd-none'}">
                    <button class=" btn btn-outline-primary viewDeliveryNoteBtn tooltip-test" title="view" id="viewDeliveryNoteBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary updateDeliveryNoteBtn tooltip-test" title="update" id="updateDeliveryNoteBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteDeliveryNoteBtn tooltip-test" title="delete" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });
}

const getAncVitalSignsTable = (tableId, ancRegId, modal, viewer) => {
    const vitalSignsByVisit =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/ancvitalsigns/load/table', data: {
            'ancRegId': ancRegId,
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
                    if (Number(row.bloodPressure?.split('/')[0]) > 139){
                    return  `<span class="text-danger fw-semibold">${row.bloodPressure}</span>` 
                    } else {
                    return row.bloodPressure
                    } 
                }
            },
            {data: "weight"},
            {data: "urineProtein"},
            {data: "urineGlucose"},
            {data: "remarks"},
            {data: "by"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex-">
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id}" data-patienttype="${row.patientType}">
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

export {getWaitingTable, getPatientsVisitsByFilterTable, getNurseMedicationsByFilter, getMedicationChartByPrescription, getPrescriptionChartByPrescription, getOtherPrescriptionsByFilterNurses, getUpcomingMedicationsTable, getDeliveryNoteTable, getAncVitalSignsTable, getUpcomingNursingChartsTable}
