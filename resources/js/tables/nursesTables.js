import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, detailsBtn, detailsBtn1, detailsBtn2, displayPaystatus, flagIndicator, flagPatientReason, flagSponsorReason, getMinsDiff, getOrdinal, histroyBtn, prescriptionStatusContorller, preSearch, searchDecider, searchMin, searchPlaceholderText, sponsorAndPayPercent, visitType, wardState } from "../helpers";

const getWaitingTable = (tableId) => {
    const waitingTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  '/visits/load/waiting',
        orderMulti: true,
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: 'No patient is waiting',
            searchPlaceholder: searchPlaceholderText
        },
        columns: [
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "sex"},
            {data: "age"},
            {data: row => `<div><span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span></div>${row.visitType == 'ANC' ? visitType(row, null, 50) : ''}`},
            {data: row => `<span class="tooltip-test" title="initiated by ${row.initiatedBy}">${row.came}</span>`},
            {data: "waitingFor"},
            {data: "doctor"},
            {data: row => function () {
                if (row.visitType == 'ANC'){
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
    waitingTable.on('draw.init', searchMin(waitingTable, tableId, 2))

    return waitingTable;
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
                const isAnc = row.visitType == 'ANC'
                        return `
                        <div class="d-flex flex" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor }" data-patientid="${ row.patientId }" data-ancregid="${ row.ancRegId }" data-sponsorcat="${row.sponsorCategory}">
                                <button class=" btn btn${chartables < 1 ? row.scheduleCount ? '-outline-primary px-1' : '-outline-primary' : '-primary px-1'} viewOtherPrescriptionsBtn tooltip-test" title="other medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${row.sponsor}" data-sponsorcat="${row.sponsorCategory}">
                                    ${(chartables < 1 ? '' : chartables + ' | ') + ' ' + (row.scheduleCount ? (row.doneCount + '/' + row.scheduleCount) + ' | ' : '' )+ ' ' + row.otherPrescriptions}
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
                if (row.visitType == 'ANC') {
                    if (row.ancVitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-${row.ancRegId ? 'primary ancVitalSignsBtn' : 'secondary'}  tooltip-test" title="${row.ancRegId ? 'add vital signs' : 'no anc registeration'}" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-visittype="${ row.visitType }" id="ancVitalSignsBtn">
                                ${ row.ancRegId ? '<i class="bi bi-plus-square-fill"></i>' :'<i class="bi bi-x-square-fill"></i>'}
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn tooltip-test px-2" title="Add Vitals Signs" data-id="${ row.id }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-visittype="${ row.visitType }" id="ancVitalSignsBtn">
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
                data: row => tableId === '#inPatientsVisitTable' ? detailsBtn1(row) : detailsBtn2(row)}
    ]

    filter === 'Inpatient' ? preparedColumns.splice(9, 0, {data: row => wardState(row)},) : ''

    const allPatientsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/nurses/load/consulted/nurses', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 75, 100, 125],
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: 'No patient record',
            searchPlaceholder: searchPlaceholderText
        },
        columns: preparedColumns
    });
    allPatientsTable.on('draw.init', searchDecider(allPatientsTable, tableId, 2, filter))

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
            {data: "route"},
            {data: "qtyBilled"},
            {data: "qtyDispensed"},
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
                        <button type="button" id="chartMedicationBtn" class="btn btn${row.medicationCharts.length ? '' : '-outline'}-primary chatMedicationBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}" data-resource="${row.resource}" data-prescription="${row.prescription}" data-prescribedBy="${row.prescribedBy}" data-patient="${row.patient}" data-sponsor="${row.sponsor}" data-prescribed="${row.prescribedFormatted}" data-consultation="${row.conId}" data-visit="${row.visitId}">
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
            {data: "route"},
            {data: "qtyBilled"},
            {data: "qtyDispensed"},
            {data: "prescribedBy"},
            {data: "prescribed"},
            {data: "note"},
            {data: row =>  row.chartable ? 'Yes' : 'No'},
            {
                sortable: false,
                data: row =>
                `
                <div class="d-flex flex- ${row.closed || !row.chartable ? 'd-none' : ''}">
                    ${row.doseComplete ? 'Complete' : row.discontinued ? 'Discontinued' : `
                        <button type="button" id="chartPrescriptionBtn" class="btn btn${row.prescriptionCharts.length ? '' : '-outline'}-primary chartPrescriptionBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}", data-resource="${row.resource}" data-prescription="${row.prescription + ' - ' + row.note}" data-prescribedBy="${row.prescribedBy}" data-patient="${row.patient}" data-sponsor="${row.sponsor}" data-prescribed="${row.prescribedFormatted}" data-consultation="${row.conId}" data-visit="${row.visitId}">
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
        searchDelay: 500,
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
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: row => () => {
                return row.status == 'Inpatient' || row.status == 'Observation' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {data: row => wardState(row)},
            {data: row => `<span class="position-relative p-2"> ${row.treatment} ${row.notGiven == 'Snooze 60 mins' ||  row.notGiven == 'Snooze 30 mins' ? `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${row.notGiven + ' by ' + row.givenBy + ' at ' + row.timeGiven}</span>` : ''} </span>`},
            {data: "prescription"},
            {data: "dose"},
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
        searchDelay: 500,
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
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: row => () => {
                return row.status == 'Inpatient' ? 
                `<span class="fw-bold text-primary tooltip-test" title="Inpatient"><i class="bi bi-hospital-fill"></i></span>` :
                `<span class="fw-bold tooltip-test" title="Outpatient"><i class="bi bi-hospital"></i></span>`
            } },
            {data: row => wardState(row)},
            {data: row => `<span class="position-relative p-2"> ${row.care} ${row.notDone == 'Snooze 60 mins' ||  row.notDone == 'Snooze 30 mins' ? `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${row.notDone +  ' by ' + row.doneBy + ' at ' + row.timeDone}</span>` : ''} </span>`},
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
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id}" data-visittype="${row.visitType}">
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
        ajax: {url: `/prescription/load/emergency`, data: {
            'viewer' : viewer
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200],
        search:true,
        searchDelay: 500,
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
            {data: row => `<div class="d-flex text-secondary">
                                <span class="${row.qtyDispensed || +row.closed ? '': 'billQtySpan'} btn btn-${row.qtyBilled ? 'white text-secondary' : 'outline-primary'}" data-id="${row.id}" data-stock="${row.stock}">${row.qtyBilled ? row.qtyBilled+' '+row.unit : 'Bill'}</span>
                                <input class="ms-1 form-control billQtyInput d-none text-secondary" type="number" style="width:6rem;" id="billQtyInput" value="${row.qtyBilled == 0 ? '' : row.qtyBilled}" name="quantity" id="quantity">
                            </div>`
                        },
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
                <div class="d-flex text-secondary  ${viewer === 'pharmacy' ? '' : 'd-none'}">
                    <span class="${row.qtyBilled ? 'dispenseQtySpan' : ''} btn btn-${row.qtyDispensed ? 'white text-secondary' : 'outline-primary'}" data-id="${row.id}" data-qtybilled="${row.qtyBilled}" data-stock="${row.stock}">${row.qtyDispensed ? 'Dispensed: '+row.qtyDispensed : 'Dispense'}</span>
                    <input class="ms-1 form-control dispenseQtyInput d-none text-secondary" type="number" style="width:6rem;" value="${row.qtyDispensed == 0 ? '' : row.qtyDispensed}" name="quantity" id="quantity">
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
        searchDelay: 500,
        language: {
            emptyTable: 'No Report'
        },
        columns: [
            {data: "date"},
            {data: "shift"},
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

const getShiftReportTable = (tableId, department, shiftBadgeSpan) => {
    let shiftCount = []
    const shiftReportTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/shiftreport/load', data: {
            'department': department,
        }},
        orderMulti: true,
        searchDelay: 500,
        language: {
            emptyTable: 'No Report'
        },
        rowCallback: (row, data) => {
            if (data.notify){
                shiftCount.push(row)
            }
            row.classList.add('fw-semibold')
        },
        drawCallback: function (settings) {
            if (shiftCount.length){
                shiftBadgeSpan.innerHTML = shiftCount.length
                shiftCount = []
            } else {
                shiftBadgeSpan.innerHTML = ''
            }
        },
        columns: [
            {data: "date"},
            {data: "shift"},
            {data: "writtenBy"},
            {data: row => function () {
                return `
                <div class="d-flex flex-">
                <button class=" btn btn-outline-primary viewShiftReportBtn tooltip-test ${row.writtenById == row.userId ? 'd-none' : ''}" title="view" id="viewShiftReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary editShiftReportBtn tooltip-test ${row.writtenById == row.userId ? '' : 'd-none'}" title="edit report" id="editShiftReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteShiftReportBtn tooltip-test ${row.writtenById == row.userId ? '' : 'd-none'}" title="delete" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });

    function format(data, tableId) {
                if (data.viewedAt) {
                    let child = `<table class="table align-middle ">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td class="text-secondary">Shift</td>
                                                    <td class="text-secondary">Viewed At</td>
                                                    <td class="text-secondary">Viewed By</td>
                                                </tr>
                                            </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-secondary">${data.viewedShift}</td>
                                                <td class="text-secondary">${data.viewedAt}</td>
                                                <td class="text-secondary">${data.viewedBy}</td>
                                            </tr>
                                            <tr class="${data.viewedAt1 ? '' : 'd-none'}">
                                                <td class="text-secondary">${data.viewedShift1}</td>
                                                <td class="text-secondary">${data.viewedAt1}</td>
                                                <td class="text-secondary">${data.viewedBy1}</td>
                                            </tr>
                                            <tr class="${data.viewedAt2 ? '' : 'd-none'}">
                                                <td class="text-secondary">${data.viewedShift2}</td>
                                                <td class="text-secondary">${data.viewedAt2}</td>
                                                <td class="text-secondary">${data.viewedBy2}</td>
                                            </tr>
                                        </tbody>
                                        `
                                        return (child);
                                    } 
                else {
                   let noChild = `
                   <table class="table align-middle table-sm">
                        <tr>
                            <td align="center" colspan="3" class="text-secondary">
                                No views yet
                            </td>
                        </tr>
                    </table>
                   `
                   return (noChild)
                }
    }

    shiftReportTable.on('draw', function() {
        const tableId = shiftReportTable.table().container().id.split('_')[0]
        shiftReportTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })

    return shiftReportTable
}

const getLabourRecordTable = (tableId, visitId, view, modal) => {
    const labourRecordTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/labourrecord/load', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        searching:false,
        lengthChange: false,
        language: {
            emptyTable: 'No Labour Record'
        },
        columns: [
            {data: "date"},
            {data: "onset"},
            {data: "membranesRuptured"},
            {data: "contractionsBegan"},
            {data: "examiner"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${view || row.closed ? '' : 'd-none'}">
                    <button class=" btn btn-outline-primary viewLabourSummaryBtn tooltip-test ${row.summarizedBy ? '' : 'd-none'}" title="view labour summary" id="viewLabourSummaryBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary updateLabourSummaryBtn tooltip-test" title="update labour summary" id="updateLabourSummaryBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary deleteLabourSummaryBtn tooltip-test" title="delete summary" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
            {data: row => function () {
                return `
                <div class="d-flex flex- ${view || row.closed ? '' : 'd-none'}">
                    <button class=" btn btn-outline-primary viewLabourRecordBtn tooltip-test" title="view labour record" id="viewLabourRecordBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary updateLabourRecordBtn tooltip-test" title="update labour record" id="updateLabourRecordBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary deleteLabourRecordBtn tooltip-test" title="delete labour record" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
            {data: row => function () {
                return `
                <div class="d-flex flex- ${view || row.closed ? '' : 'd-none'}">
                    <button class=" btn btn-outline-primary partographBtn tooltip-test" title="create partograph" id="partographBtn" data-id="${row.id}" data-sponsor="${row.sponsorName + ' - ' + row.sponsorCategory}" data-patient="${row.patient}">
                        Open
                    </button>
                </div>
            `
                }
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        labourRecordTable.destroy()
    })

    return labourRecordTable
}

const getPartographTable = (tableId, labourRecordId, modal, parameterType, labourInProgressDebounced, accordionCollapseList) => {
    const partographTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/partograph/load', data: {
            'labourRecordId': labourRecordId,
            'parameterType': parameterType,
        }},
        orderMulti: true,
        searching:false,
        lengthChange: false,
        language: {
            emptyTable: 'No Record Found'
        },
        columns: [
            {data: row => `<div class="d-flex">
                            <span class="recordedAtSpanBtn tooltip-test" title="edit time">${row.recordedAt}</span>
                            <input class="ms-1 form-control recordedAtInput d-none" value="${row.recordedAtRaw}" data-record='${JSON.stringify(row)}' data-id="${row.id}" data-table="${tableId}" type="datetime-local" style="width:10rem;" name="recordedAt">
                        </div>
                    `},
            {data: row => () => {
                const entries = [];
                for (const [key, value] of Object.entries(row.value)){
                   entries.push(
                    `<div class="d-flex">
                        ${key}:
                        <span class="valueSpanBtn tooltip-test" title="edit value">${value}</span>
                        <input class="ms-1 form-control valueInput d-none" value="${value}" data-record='${JSON.stringify(row)}' data-key="${key}" data-id="${row.id}" data-table="${tableId}" type="text" style="width:4rem;" name="value">
                    </div>

                    `);
                }
                return entries.join(', ');
            }},
            {data: "recordedBy"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${row.closed ? 'd-none' : ''}">
                    <button type="button" class="ms-1 btn btn-outline-primary deletePartographBtn tooltip-test" title="delete partograph record" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });

    modal._element.addEventListener('hidden.bs.modal', function () {
        labourInProgressDebounced(0)
        accordionCollapseList.forEach((item) => {
            item.hide()
        })
    })

    return partographTable
}

export {getWaitingTable, getPatientsVisitsByFilterTable, getNurseMedicationsByFilter, getMedicationChartByPrescription, getPrescriptionChartByPrescription, getOtherPrescriptionsByFilterNurses, getUpcomingMedicationsTable, getDeliveryNoteTable, getAncVitalSignsTable, getUpcomingNursingChartsTable, getEmergencyTable, getNursesReportTable, getShiftReportTable, getLabourRecordTable, getPartographTable}
