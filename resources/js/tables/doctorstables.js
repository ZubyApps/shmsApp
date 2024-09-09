import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, displayPaystatus, flagIndicator, flagPatientReason, flagSponsorReason, getOrdinal, histroyBtn, prescriptionOnLatestConsultation, prescriptionStatusContorller, reviewBtn, sponsorAndPayPercent } from "../helpers";

const getOutpatientsVisitTable = (tableId, filter) => {
    return new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/doctors/load/consulted/outpatient', data: {
            'filterBy' : filter
        }},
        orderMulti: true,
        search:true,
        searchDelay: 1000,
        lengthMenu:[25, 50, 100, 150, 200],
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: row => prescriptionOnLatestConsultation(row)},
            {data: row => sponsorAndPayPercent(row)},
            {data: "30dayCount"},
            {data: row => function () {
                const chartables = row.chartableMedications
                    return `
                    <div class="d-flex flex">
                        <button class=" btn btn${chartables < 1 ? '-outline-primary' : '-primary px-1'} viewMedicationBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${(chartables < 1 ? '' : chartables) + ' ' + row.givenCount + '/' + row.doseCount}
                        </button>
                    </div>`
                }
            },
            {data: row =>  `
                        <div class="d-flex justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn px-1 tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
                            </button>
                        </div>`                
            },
            {data: row => function () {
                   return `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary vitalSignsBtn px-2 tooltip-test" title="View VitalSigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
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
        searchDelay: 1000,
        lengthMenu:[25, 50, 100, 150, 200],
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: row => prescriptionOnLatestConsultation(row)},
            {data: row => sponsorAndPayPercent(row)},
            {data: "30dayCount"},
            {data: row => function () {
                const chartables = row.chartableMedications
                    return `
                    <div class="d-flex flex">
                        <button class=" btn btn${chartables < 1 ? '-outline-primary' : '-primary px-1'} viewMedicationBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${(chartables < 1 ? '' : chartables) + ' ' + row.givenCount + '/' + row.doseCount}
                        </button>
                    </div>`
                }
            },
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
            {data: row => `<small>${row.ward + '-' + row.bedNo}</small>`},
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
        searchDelay: 1000,
        lengthMenu:[25, 50, 100, 150, 200],
        language: {
            emptyTable: "No patient"
        },
        columns: [
            {data: "came"},
            {data: row => histroyBtn(row)},
            {data: "doctor"},
            {data: row => prescriptionOnLatestConsultation(row)},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => row.ancCount+getOrdinal(row.ancCount)},
            {data: row => function () {
                const chartables = row.chartableMedications
                    return `
                    <div class="d-flex flex">
                        <button class=" btn btn${chartables < 1 ? '-outline-primary' : '-primary px-1'} viewMedicationBtn tooltip-test" title="charted medications(s)" data-id="${ row.id }" data-patient="${ row.patient }" data-age="${row.age}" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${(chartables < 1 ? '' : chartables) + ' ' + row.givenCount + '/' + row.doseCount}
                        </button>
                    </div>`
                }
            },
            {data: row =>  `
                        <div class="d-flex flex- justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
                            </button>
                        </div>`                
            },
            {data: row => function () {
                if (row.ancRegId) {
                    if (row.ancVitalSigns < 1){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn  tooltip-test" title="${row.ancRegId ? 'add anc vital signs' : 'no anc registeration'}" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" id="ancVitalSignsBtn">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary ancVitalSignsBtn tooltip-test" title="anc vitalsigns" data-id="${ row.id }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" id="ancVitalSignsBtn">
                                <i class="bi bi-check-circle-fill">${row.ancVitalSigns}</i>
                                </button>
                            </div>`
                        }
                    } else {
                        if (row.vitalSigns < 1){
                            return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="add regular vitalsigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                        <i class="bi bi-plus-square-dotted"></i>
                                    </button>
                                </div>`
                            } else {
                                return `
                                <div class="d-flex flex-">
                                    <button class=" btn btn-outline-primary vitalSignsBtn tooltip-test" title="regular vitalsigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
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
        searchDelay: 1000,
        lengthMenu:[25, 50, 100, 150, 200],
        language: {
            emptyTable: 'No patient is waiting'
        },
        columns: [
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "sex"},
            {data: "age"},
            {data: row => `<span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span>`},
            {data: row => `<span class="tooltip-test" title="initiated by ${row.initiatedBy}">${row.came}</span>`},
            {data: "waitingFor"},
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
            {data: row => `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary emergencyBtn tooltip-test ms-1" title="emergency items" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${ row.sponsorCategory }">
                                    <i class="bi bi-prescription">${row.emergency ? row.emergency : ''}</i>
                                </button>
                            </div>
                        `
            },
            {data: row => function () {
                if (row.doctor === ''){
                    return `
                        <div class="d-flex flex-">
                        ${row.closed ? `<i class="ms-1 btn btn-outline-primary bi bi-lock-fill openVisitBtn" id="openVisitBtn" data-id="${row.id}"></i>` : `
                            <button class=" btn btn-outline-primary consultBtn tooltip-test" title="consult" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-age="${row.age.split('y')[0]}">
                                <i class="bi bi-clipboard2-plus-fill"></i>
                            </button>
                                <div class="dropdown ms-1">
                                    <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="remove" data-bs-toggle="dropdown" href="" >
                                    <i class="bi bi-file-minus-fill"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a role="button" class="dropdown-item closeVisitBtn tooltip-test" title="close visits" id="closeVisitBtn" data-id="${ row.id }">
                                                <i class="bi bi-lock-fill text-primary"></i> Close Visit
                                            </a>
                                        </li>
                                        <li>
                                            <a role="button" class="dropdown-item deleteVisitBtn tooltip-test" title="delete visit" id="deleteVisitBtn" data-id="${ row.id }">
                                                <i class="bi bi-x-circle-fill text-primary"></i> Delete Visit
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            `}
                        </div>`
                    } else {
                        return `
                        <div class="dropdown">
                            <a class="btn text-black tooltip-test text-decoration-none" title="doctor" data-bs-toggle="dropdown">
                                ${row.doctor}
                                <i class="bi bi-chevron-double-down"> </i>
                            </a>
                                <ul class="dropdown-menu">
                                    ${row.closed ? `<a class="dropdown-item openVisitBtn btn tooltip-test" data-id="${row.id}"><i class="bi bi-lock-fill text-primary" id="openVisitBtn"></i> Closed </a>` : `
                                        <li>
                                            <a class="dropdown-item consultBtn btn tooltip-test" title="consult"  data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-age="${row.age.split('y')[0]}">
                                                <i class="bi bi-clipboard2-plus-fill text-primary"></i> Consult
                                            </a>
                                            <a class="dropdown-item closeVisitBtn btn tooltip-test" title="close" id="closeVisitBtn"  data-id="${ row.id }">
                                                <i class="bi bi-lock-fill text-primary"></i> Close Visit
                                            </a>
                                            <a class="dropdown-item deleteVisitBtn btn tooltip-test" title="delete" id="deleteVisitBtn"  data-id="${ row.id }">
                                                <i class="bi bi-x-circle-fill text-primary"></i> Delete Visit
                                            </a>
                                        </li>
                                    `}
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
        lengthMenu:[50, 100, 150, 200, 250],
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
            {data: "headCircumference"},
            {data: "midArmCircuference"},
            {data: "fluidDrain"},
            {data: "urineOutput"},
            {data: "fetalHr"},
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

const getPrescriptionTableByConsultation = (tableId, conId, visitId, modal) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})
    const prescriptionTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/initial', data: {
            'conId': conId,
            'visitId': visitId,
        }},
        orderMulti: true,
        search:true,
        searchDelay: 1000,
        language: {
            emptyTable: 'No resource has been added'
        },
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "prescribed"},
            {data: "resource"},
            {data: "prescription"},
            {data: "route"},
            {
                data: row =>  () => {
                    const catgories = ['Medications', 'Consumables']
                    if (!catgories.includes(row.category)){
                        return `
                        <div class="d-flex">
                            <span class="changeBillSpan tooltip-test" title="${row.quantity ? 'Change Bill' : 'Bill'}" data-id="${row.id}" data-stock="${row.stock}">${row.quantity ? row.quantity : 'Bill'}</span>
                            <input class="ms-1 form-control billInput d-none" id="billInput" value="${row.quantity ?? ''}">
                        </div>
                        `    
                    }
                    return row.quantity
                }
            },
            {data: row => account.format(row.hmsBill)},
            {data: "note"},
            {data: "chartable"},
            {data: "by"},
            {
                sortable: false,
                data: row =>  `
                <div class="d-flex flex- ${visitId && !conId ? 'd-none': ''}">
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
                        <div class="dropdown">
                            <i class="btn btn-outline-primary bi bi-gear" role="button" data-bs-toggle="dropdown"></i>

                            <ul class="dropdown-menu">
                                <li class="${row.sent || (viewer !== 'lab' && viewer !== 'doctor') ? 'd-none' : ''}">
                                    <a class="btn btn-outline-primary dropdown-item addResultBtn" id="addResultBtn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${ row.sponsorCategory }" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-plus-square"></i> Add Result
                                    </a>
                                </li>
                                <li  class="${!row.sent || (viewer !== 'lab' && viewer !== 'doctor') ? 'd-none' : ''}">
                                    <a class="btn btn-outline-primary dropdown-item updateResultBtn" id="updateResultBtn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${ row.sponsorCategory }" data-table="${tableId}" title="update result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}">
                                        <i class="bi bi-pencil-fill"></i> Update Result
                                    </a>
                                </li>
                                <li  class="${!row.sent ? 'd-none' : ''}">
                                    <a class="btn btn-outline-primary dropdown-item printThisBtn" id="printThisBtn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-result="${ row.result }" data-sent="${ row.sent }" data-table="${tableId}" title="print this" data-id="${ row.id}" data-stafffullname="${ row.staffFullName}">
                                        <i class="bi bi-download"></i> Print This
                                    </a>
                                </li>
                                <li  class="${!row.sent ? 'd-none' : ''}">
                                    <a class="btn btn-outline-primary dropdown-item printAllBtn" id="printAllBtn" data-investigation="${row.resource}" data-patient="${ row.patient }" data-result="${ row.result }" data-sent="${ row.sent }" data-table="${tableId}" title="print all" data-id="${ row.id}" data-stafffullname="${ row.staffFullName}">
                                        <i class="bi bi-download"></i> Print All
                                    </a>
                                </li>
                                <li class="${!row.sent || (viewer !== 'lab' && viewer !== 'doctor') ? 'd-none' : ''}">
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
                if (data.result || data.removalReason) {
                    return `   
                                <table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td> </td>
                                                    <td class="text-secondary">Sample</td>
                                                    <td class="text-secondary">Result</td>
                                                    <td class="text-secondary">Entered By</td>
                                                    <td class="text-secondary">DateTime</td>
                                                    <td class="text-secondary">Comments</td>
                                                </tr>
                                            </thead>
                                        <tbody>
                                             <tr>
                                                <td> </td>
                                                <td class="text-secondary">${data.sample ?? ''}</td>
                                                <td class="text-secondary">
                                                    <p>${data.result ?? ''}</p>
                                                </td>
                                                <td class="text-secondary">${data.staff}</td>
                                                <td class="text-secondary">${data.sent}</td>
                                                <td class="text-secondary">${data.removalReason}</td>
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

const getMedicationsByFilter = (tableId, conId, modal, visitId) => {
    const treatmentTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/medications', data: {
            'conId': conId,
            'visitId': visitId,
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
            {data: row => `<span class="text-${row.rejected ? 'danger' : 'primary'}">${row.resource + ' ' + displayPaystatus(row, (row.payClass == 'Credit'), (row.sponsorCategory == 'NHIS')) } ${(row.chartable ? `<span class="text-secondary">(${row.givenCount + '/' + row.doseCount})</span>` : '')}</span>`},
            {data: row => prescriptionStatusContorller(row, tableId)},
            {data: "route"},
            {data: "qtyBilled"},
            {data: "qtyDispensed"},
            {data: "prescribedBy"},
            {data: "prescribed"},
            {data: "note"},
            {data: row => () => {
                    return row.qtyBilled ? '<i class=" text-primary bi bi-check-circle-fill"></i>' : '-'
            } },
            {data:  row => () => {
                return row.qtyDispensed ? '<i class=" text-primary bi bi-check-circle-fill"></i>' : '-'
            }},
            {data: row => () => {
                return row.doseComplete ? 'Complete' : row.discontinued ? 'Discontinued' : row.medicationCharts.length ? 'Charted' : row.chartable ? 'Uncharted' : 'N/A'}
            } 
        ]
    });

    function format(data) {
        const chart = data?.medicationCharts
        const discontinued = data?.discontinued
                if (chart?.length > 0) {
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
                                                    <td class="text-secondary">Status</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                            
                                chart?.forEach(line => {
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
                                                <td class="text-secondary">${line.status ? 'Given' : discontinued ? 'Discontined' : 'Not Given' }</td>
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

const getOtherPrescriptionsByFilter = (tableId, conId, modal, visitId) => {
    const treatmentTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/prescription/load/others', data: {
            'conId': conId,
            'visitId': visitId,
        }},
        paging: true,
        lengthChange: false,
        searching: false,
        orderMulti: false,
        language: {
            emptyTable: 'No other prescription'
        },
        rowCallback: (row, data) => {
            row.classList.add('fw-semibold')
        return row
        },
        columns: [
            {data: row => `<i role="button" class="text-primary fs-5 bi bi-prescription2"></i>`},
            {data: row => `<span class="text-${row.rejected ? 'danger' : 'primary'}">${row.resource + ' ' + displayPaystatus(row, (row.payClass == 'Credit'), (row.sponsorCategory == 'NHIS')) } ${(row.chartable ? `<span class="text-secondary">(${row.givenCount + '/' + row.doseCount})</span>` : '')}</span>`},
            {data: row => prescriptionStatusContorller(row, tableId)},
            {data: "route"},
            {data: "qtyBilled"},
            {data: "qtyDispensed"},
            {data: "prescribedBy"},
            {data: "prescribed"},
            {data: "note"},
            {data: row => () => {
                    return row.qtyBilled ? '<i class=" text-primary bi bi-check-circle-fill"></i>' : '-'
            } },
            {data:  row => () => {
                return row.qtyDispensed ? '<i class=" text-primary bi bi-check-circle-fill"></i>' : '-'
            }},
            {data: row => () => {
                return row.doseComplete ? 'Complete' : row.discontinued ? 'Discontinued' : row.prescriptionCharts.length ? 'Charted' : row.chartable ? 'Uncharted' : 'N/A'}
            } 
        ]
    });

    function format(data) {
        const chart = data?.prescriptionCharts
        const discontinued = data?.discontinued
                if (chart?.length > 0) {
                    let child = `<table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td> </td>
                                                    <td> </td>
                                                    <td class="text-secondary">Charted At</td>
                                                    <td class="text-secondary">Charted By</td>
                                                    <td class="text-secondary">Instruction</td>
                                                    <td class="text-secondary">Schedule Time</td>
                                                    <td class="text-secondary">Report</td>
                                                    <td class="text-secondary">Time Done</td>
                                                    <td class="text-secondary">Done By</td>
                                                    <td class="text-secondary">Status</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                            
                                chart?.forEach(line => {
                                    child += `<tr>
                                                <td> </td>
                                                <td> </td>
                                                <td class="text-secondary">${line.chartedAt}</td>                
                                                <td class="text-secondary">${line.chartedBy}</td>                
                                                <td class="text-secondary">${line.carePrescribed}</td>
                                                <td class="text-secondary">${line.scheduledTime}</td>
                                                <td class="text-secondary">${line.note}</td>
                                                <td class="text-secondary">${line.timeDone}</td>
                                                <td class="text-secondary">${line.doneBy}</td>
                                                <td class="text-secondary">${line.status ? 'Done' : discontinued ? 'Discontined' : 'Not Done' }</td>
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

const getSurgeryNoteTable = (tableId, visitId, view, modal) => {
    const surgeryNoteTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/surgerynote/load/details', data: {
            'visitId': visitId,
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
                <div class="d-flex flex- ${view || row.closed ? '' : 'd-none'}">
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

    modal.addEventListener('hidden.bs.modal', function () {
        surgeryNoteTable.destroy()
    })

    return surgeryNoteTable
}

const getMedicalReportTable = (tableId, visitId, modal, isDoctor) => {
    const medicalReportTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/medicalreports/load', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        searching:true,
        searchDelay: 1000,
        lengthChange: true,
        language: {
            emptyTable: 'No medical report'
        },
        columns: [
            {data: "date"},
            {data: "type"},
            {data: "doctor"},
            {data: "recipientsAddress"},
            {data: "requestedBy"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${row.closed ? 'd-none' : ''}">
                    <button class=" btn btn-outline-primary viewMedicalReportBtn tooltip-test" title="view" id="viewMedicalReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="ms-1 btn btn-outline-primary editMedicalReportBtn tooltip-test ${isDoctor ? '' : 'd-none'}" title="update" id="editMedicalReportBtn" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteMedicalReportBtn tooltip-test ${isDoctor ? '' : 'd-none'}" title="delete" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        medicalReportTable.destroy()
    })

    return medicalReportTable
}

const getPatientsFileTable = (tableId, visitId, modal) => {
    const patientsFileTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:   {url: '/patientsfiles/load/files', data: {
            'visitId': visitId,
        }},
        orderMulti: true,
        searching:true,
        searchDelay: 1000,
        lengthChange: true,
        language: {
            emptyTable: 'No file/document'
        },
        columns: [
            {data: "createdAt"},
            {data: "filename"},
            {data: "thirdParty"},
            {data: "comment"},
            {data: row =>  `<span class="position-relative"><a href="/patientsfiles/download/${row.id}" target="blank">
                                <i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a>
                            </span>`},
            {data: "uploadedBy"},
            {data: row => function () {
                return `
                <div class="d-flex flex- ${row.closed ? 'd-none' : ''}">
                    <button type="submit" class="btn btn-outline-primary deleteFileBtn" title="delete file" data-id="${row.id}" data-table="${tableId}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            `
                }
            },
        ]
    });

    modal.addEventListener('hidden.bs.modal', function () {
        patientsFileTable.destroy()
    })

    return patientsFileTable
}

export {getOutpatientsVisitTable, getInpatientsVisitTable, getAncPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getMedicationsByFilter, getOtherPrescriptionsByFilter, getSurgeryNoteTable, getMedicalReportTable, getPatientsFileTable}
