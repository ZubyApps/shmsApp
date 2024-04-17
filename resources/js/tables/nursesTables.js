import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, detailsBtn, detailsBtn1, displayPaystatus, getMinsDiff, getOrdinal, histroyBtn, prescriptionStatusContorller, sponsorAndPayPercent } from "../helpers";

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
            {data: row => `<span class="tooltip-test" title="initiated by ${row.initiatedBy}">${row.came}</span>`},
            {data: "doctor"},
            {data: row => function () {
                if (row.patientType == 'ANC'){
                    return row.ancVitalSigns ? `<i class="btn btn-outline-primary bi bi-check-circle-fill">${row.ancVitalSigns}</i>` : ''
                } else {
                    if (row.vitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
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
                                        <a role="button" class="dropdown-item vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
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
            {data: row => `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary addPrescriptionBtn tooltip-test ms-1" title="add emergency item" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${ row.sponsorCategory }">
                                    <i class="bi bi-prescription">${row.emergency ? row.emergency : ''}</i>
                                </button>
                            </div>
                        `
            }
        ]
    });
}

const getPatientsVisitsByFilterTable = (tableId, filter) => {
    const preparedColumns = [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => function () {
                const chartables = row.chartableMedications
                    return `
                    <div class="d-flex flex">
                        <button class=" btn btn${chartables < 1 ? '-outline-primary' : '-primary px-1'} viewMedicationBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-conid="${ row.conId }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${(chartables < 1 ? '' : chartables) + ' ' + row.givenCount + '/' + row.doseCount}
                        </button>
                    </div>`
                }
            },
            {data: row => function () {
                const chartables = row.otherChartables
                const isAnc = row.patientType == 'ANC'
                        return `
                        <div class="d-flex flex" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor }" data-patientid="${ row.patientId }" data-ancregid="${ row.ancRegId }" data-sponsorcat="${row.sponsorCategory}">
                                <button class=" btn btn${chartables < 1 ? '-outline-primary' : '-primary px-1'} viewOtherPrescriptionsBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${row.sponsor}" data-sponsorcat="${row.sponsorCategory}">
                                    ${(chartables < 1 ? '' : chartables) + ' ' + row.doneCount + '/' + row.scheduleCount}
                                </button>
                                ${ row.ancRegId ? 
                                    `<div class="dropdown ${isAnc ? '' : 'd-none'}">
                                        <a class="text-black tooltip-test text-decoration-none ms-1" title="registered (${row.ancCount+getOrdinal(row.ancCount)+" anc encounter"})" data-bs-toggle="dropdown" href="" >
                                        <i class="btn btn-outline-primary bi bi-check-circle-fill">${row.ancCount}</i>
                                        </a>
                                            <ul class="dropdown-menu">
                                            <li>
                                                <a role="button" class="dropdown-item viewRegisterationBtn tooltip-test" title="view" id="viewRegisterationBtn" data-ancregid="${ row.ancRegId }" data-id="${ row.id }">
                                                    <i class="bi bi-plus-square-fill text-primary"></i> View
                                                </a>
                                            </li>
                                            <li>
                                                <a role="button" class="dropdown-item editRegisterationBtn tooltip-test" title="edit" id="editRegisterationBtn" data-ancregid="${ row.ancRegId }" data-id="${ row.id }">
                                                    <i class="bi bi-plus-square-fill text-primary"></i> Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>`
                                    : 
                                    `<button class="${isAnc ? '' : 'd-none'} ms-1 btn btn-outline-primary bi bi-plus-square ancRegisterationBtn tooltip-test" title="register"></button>` 
                                }
                        </div>`
                }
            },
            {data: row => function () {
                if (row.patientType == 'ANC') {
                    if (row.ancVitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-${row.ancRegId ? 'primary ancVitalSignsBtn' : 'secondary'}  tooltip-test" title="${row.ancRegId ? 'add vital signs' : 'no anc registeration'}" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-patienttype="${ row.patientType }" id="ancVitalSignsBtn">
                                ${ row.ancRegId ? '<i class="bi bi-plus-square-fill"></i>' :'<i class="bi bi-x-square-fill"></i>'}
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn tooltip-test px-2" title="Add Vitals Signs" data-id="${ row.id }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-patienttype="${ row.patientType }" id="ancVitalSignsBtn">
                                <i class="bi bi-check-circle-fill">${row.ancVitalSigns}</i>
                                </button>
                            </div>`
                        }
                    } else {
                        if (row.vitalSigns < 1){
                            return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                        <i class="bi bi-plus-square-fill"></i>
                                    </button>
                                </div>`
                            } else {
                                return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test px-2" title="Add Vitals Signs" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
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
                data: row => tableId === 'inPatientsVisitTable' ? detailsBtn1(row) : detailsBtn(row)}
    ]

    filter === 'Inpatient' ? preparedColumns.splice(9, 0, {data: row => `<small>${row.ward + '-' + row.bedNo}</small>`},) : ''

    const allPatientsTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/nurses/load/consulted/nurses', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 75, 100, 125],
        search:true,
        language: {
            emptyTable: 'No patient record'
        },
        columns: preparedColumns
    });

    allPatientsTable

    return allPatientsTable
}

const getNurseMedicationsByFilter = (tableId, conId, modal, visitId, isHistory) => {
    const medicationTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/medications', data: {
            'conId': conId,
            'visitId': visitId
        }},
        paging: true,
        lengthMenu:[25, 50, 75, 100, 125],
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
                        <button type="button" id="chartPrescriptionBtn" class="btn btn${row.prescriptionCharts.length ? '' : '-outline'}-primary chartPrescriptionBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}", data-resource="${row.resource}" data-prescription="${row.note}" data-prescribedBy="${row.prescribedBy}" data-patient="${row.patient}" data-sponsor="${row.sponsor}" data-prescribed="${row.prescribedFormatted}" data-consultation="${row.conId}" data-visit="${row.visitId}">
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
                                                                <button type="button" id="deleteServiceBtn" class="btn btn-primary deleteGivenBtn tooltip-test" title="remove record" data-id="${line.id}" data-table="${tableId}">
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
    const prescriptionChartTable =  new DataTable('#'+tableId, {
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
        prescriptionChartTable.destroy()
    })

    return prescriptionChartTable
}

const getUpcomingMedicationsTable = (tableId, button, span) => {
    let [diffCount1, diffCount2] = [[], []]
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
                if (diff > 5 && diff < 15){
                    row.classList.add('table-warning')
                    button.classList.remove('btn-primary')
                    button.classList.add('colour-change')
                    diffCount1.push(diff)
                } else if (diff < 5){
                    row.classList.add('table-danger')
                    button.classList.remove('btn-primary')
                    button.classList.add('colour-change1')
                    diffCount2.push(diff)
                }         
            },
        drawCallback: function (settings) {
            if (diffCount1.length || diffCount2.length){
                span.innerHTML = diffCount1.length + diffCount2.length
                diffCount1 = []
                diffCount2 = []
            } else {
                span.innerHTML = ''
                button.classList.add('btn-primary')
                button.classList.remove('colour-change', 'colour-change1')
            }
        },
        columns: [
            {data: "patient"},
            {data: row => () => {
                return row.status == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {data: row => row.ward + '-' + row.bedNo},
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
                    <button type="submit" id="giveMedicationBtn" class="ms-1 btn btn-primary giveMedicationBtn tooltip-test px-1" data-table="${tableId}" title="give medication" data-id="${ row.id}" data-dose="${row.dose}" data-prescription="${row.prescription}" data-patient="${row.patient}" data-treatment="${row.treatment}">
                    ${row.doseCount}<i class="bi bi-clipboard-plus"></i>${row.count}
                    </button>`
                }
                </div>
                `      
            },
        ]
    });
    return allMedicationChartTable
}

const getUpcomingNursingChartsTable = (tableId, button, span) => {
    let [diffCount1, diffCount2] = [[], []]
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
            if (diff > 5 && diff < 15){
                row.classList.add('table-warning')
                button.classList.remove('btn-primary')
                button.classList.add('colour-change')
                diffCount1.push(diff)
            } else if (diff < 5){
                row.classList.add('table-danger')
                button.classList.remove('btn-primary')
                button.classList.add('colour-change1')
                diffCount2.push(diff)
            }
        },
        drawCallback: function (settings) {
            if (diffCount1.length || diffCount2.length){
                span.innerHTML = diffCount1.length + diffCount2.length
                diffCount1 = []
                diffCount2 = []
            } else {
                span.innerHTML = ''
                button.classList.add('btn-primary')
                button.classList.remove('colour-change', 'colour-change1')
            }
        },
        columns: [
            {data: "patient"},
            {data: row => () => {
                return row.status == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {data: row => row.ward + '-' + row.bedNo},
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
                    <button type="submit" id="reportServiceBtn" class="ms-1 btn btn-primary reportServiceBtn tooltip-test px-1" data-table="${tableId}" title="Report Service" data-id="${ row.id}" data-dose="${row.dose}" data-instruction="${row.instruction}" data-patient="${row.patient}" data-care="${row.care}">
                    ${row.scheduleCount}<i class="bi bi-clipboard-plus"></i>${row.count}
                    </button>`
                }
                </div>
                `      
            },
        ]
    });

    return allNursingChartsTable
}

const getDeliveryNoteTable = (tableId, visitId, view, modal) => {
    const deliveryNoteTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/deliverynote/load/details', data: {
            'visitId': visitId,
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
            {data: "sex"},
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

    modal.addEventListener('hidden.bs.modal', function () {
        deliveryNoteTable.destroy()
    })

    return deliveryNoteTable
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

const getEmergencyTable = (tableId, viewer) => {
    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax: `/prescription/load/emergency`,
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No pending emergency prescriptions'
        },
        columns: [
            {data: "prescribed"},
            {data: "patient"},
            {data: "sponsor"},
            {data: row => 
                    `
                        <div class="d-flex flex">
                            <button class=" btn btn${row.medicationCharts.length > 0 ? '-outline-primary viewMedicationBtn' : ''} tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-visitid="${ row.visitId }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor + ' - ' + row.sponsorCategory }">
                                ${row.item} ${ row.doseComplete ? '<i class="bi bi-check-circle-fill tooltip-test" title="complete"></i>' : ''}
                            </button>
                        </div>
                    `
            },
            {data: "prescription"},
            {data: "quantity"},
            {data: "prescribedBy"},
            {data: "note"},
            {data: "doc"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex- ${viewer === 'doctor' ? '' : 'd-none'}">
                    <button class="ms-1 btn ${row.admissionStatus ? 'btn-primary' : 'btn-outline-primary'} tooltip-test confirmBtn" title="confirm" data-id="${ row.id}" data-tableid="${tableId}">
                        <i class="bi bi-arrow-down-square-fill"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary tooltip-test deleteBtn" title="delete" data-id="${ row.id}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                <div class="d-flex flex- ${row.closed || !row.chartable || viewer !== 'nurse' ? 'd-none' : ''}">
                    ${row.doseComplete ? 'Complete' : row.discontinued ? 'Discontinued' : `
                        <button type="button" id="chartMedicationBtn" class="btn btn${row.medicationCharts.length ? '' : '-outline'}-primary chatMedicationBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}", data-resource="${row.item}" data-prescription="${row.prescription}" data-prescribedBy="${row.prescribedBy}" data-patient="${row.patient}" data-sponsor="${row.sponsor}" data-prescribed="${row.prescribedFormatted}" data-consultation="${''}" data-visit="${row.visitId}">
                            ${row.medicationCharts.length ? row.doseComplete ? 'Complete' : 'Charted' : 'Create'}
                        </button>`
                    }
                </div>
                `      
            },
        ]
    });
}

const getNursesReportTable = (tableId, visitId, modal) => {
    const nursesRportTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/nursesreport/load', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        language: {
            emptyTable: 'No Report'
        },
        columns: [
            {data: "date"},
            {data: "report"},
            {data: "writtenBy"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${ row.closed ? 'd-none' : ''}">
                    <button class="ms-1 btn btn-outline-primary editNursesReportBtn tooltip-test" title="edit report" id="editNursesReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteNursesReportBtn tooltip-test" title="delete" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });

    modal._element.addEventListener('hidden.bs.modal', function () {
        nursesRportTable.destroy()
    })

    return nursesRportTable
}

export {getWaitingTable, getPatientsVisitsByFilterTable, getNurseMedicationsByFilter, getMedicationChartByPrescription, getPrescriptionChartByPrescription, getOtherPrescriptionsByFilterNurses, getUpcomingMedicationsTable, getDeliveryNoteTable, getAncVitalSignsTable, getUpcomingNursingChartsTable, getEmergencyTable, getNursesReportTable}
