import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
import { flagIndicator, flagPatientReason, flagSponsorReason } from '../helpers';
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getSponsorsTable = (tableId) => {
    const sponsorsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/sponsors/load',
        orderMulti: true,
        lengthMenu:[20, 40, 80, 120, 200],
        search:true,
        searchDelay: 1000,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        columns: [
            {data: "name"},
            {data: "phone"},
            {data: "email"},
            {data: "category"},
            {data: row => () =>{
                if (row.approval == 'false'){
                    return 'No'
                } else {
                    return 'Yes'
                }
                }},
            {data: "registrationBill"},
            {data: "maxPayDays"},
            {data: row => row.flag ? '<span class="fw-bold text-danger">Yes</span>' : 'No' },
            {data: "createdAt"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary updateBtn" data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        </div>
                    `
                    }
                }}
        ]
    })

    return sponsorsTable
}

const getAllPatientsTable = (tableId) => {
    const allPatientsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load',
        orderMulti: true,
        lengthMenu:[50, 100, 150, 200, 300],
        search:true,
        searchDelay: 1000,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        columns: [
            {data: "card"},
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: row => `<span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span>`},
            {data: "category"},
            {data: "createdAt"},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <div>
                                <button class=" btn btn-outline-primary initiateVisitBtn tooltip-test ${row.active > 0 ? 'd-none' : ''}" title="initiate visit" data-id="${ row.id }" data-patient="${ row.patient }">
                                    <i class="bi bi-arrow-up-right-square-fill"></i>
                                </button>
                            </div>
                            <div class="dropdown ms-1">
                                <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                    <i class="bi bi-gear" role="button"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="btn dropdown-item appointmentBtn tooltip-test" title="appointment" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${row.sponsor + ' - ' + row.category}">
                                            <i class="bi bi-cursor-fill text-primary"></i> Set Appointment
                                        </a>
                                        <a class="btn dropdown-item updateBtn tooltip-test" title="update"  data-id="${ row.id }">
                                            <i class="bi bi-pencil-fill text-primary"></i> Update
                                        </a>
                                        <a class="btn dropdown-item deleteBtn tooltip-test" title="delete"  data-id="${ row.id }">
                                            <i class="bi bi-x-circle-fill text-primary"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <div>
                                <button class=" btn btn-outline-primary initiateVisitBtn tooltip-test ${row.active > 0 ? 'd-none' : ''}" title="initiate visit" data-id="${ row.id }" data-patient="${ row.patient }">
                                    <i class="bi bi-arrow-up-right-square-fill"></i>
                                </button>
                            </div>
                            <div class="dropdown ms-1">
                                <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                    <i class="bi bi-gear" role="button"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="btn dropdown-item appointmentBtn tooltip-test" title="appointment" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${row.sponsor + ' - ' + row.category}">
                                            <i class="bi bi-cursor-fill text-primary"></i> Set Appointment
                                        </a>
                                        <a class="btn dropdown-item updateBtn tooltip-test" title="update"  data-id="${ row.id }">
                                            <i class="bi bi-pencil-fill text-primary"></i> Update
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    `
                    } 
                }}
        ]
    })

    return allPatientsTable
}

const getNewRegisteredPatientsTable = (tableId, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const newRegisteredPatientsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax: {url: '/patients/load/summary/sponsor', data: {
            'date' : date
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[50, 100, 150, 200, 300],
        "sAjaxDataProp": "data.data",
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row =>  `<span class="btn text-decoration-underline showPatientsBtn" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}">${row.sponsor}</span>`},
            {data: "patientsCount"},
            {data: "category"},
        ]
    })

    return newRegisteredPatientsTable
}

const getSexAggregateTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const sexAggregateTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load/summary/sex',
        orderMulti: true,
        search:false,
        searching:false,
        lengthChange: false,
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "sex"},
            {data: "patientsCount"},
        ]
    })

    return sexAggregateTable
}
const getAgeAggregateTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const ageAggregateTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load/summary/age',
        orderMulti: true,
        search:false,
        searching:false,
        lengthChange: false,
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "sex"},
            {data: "zeroTo3m"},
            {data: "threeTo12m"},
            {data: "oneTo5yrs"},
            {data: "fiveto13yrs"},
            {data: "thirteenTo18yrs"},
            {data: "eighteenTo48yrs"},
            {data: "fortyEightTo63yrs"},
            {data: "above63yrs"},
            {data: row => +row.zeroTo3m + +row.threeTo12m + +row.oneTo5yrs + +row.fiveto13yrs + +row.thirteenTo18yrs + +row.eighteenTo48yrs + +row.fortyEightTo63yrs + +row.above63yrs}
        ]
    })

    return ageAggregateTable
}

const getVisitsSummaryTable = (tableId, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const visitsSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax: {url: '/patients/load/summary/visits', data : {
            'date' : date,
        }} ,
        orderMulti: true,
        search:true,
        searchDelay: 1000,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        lengthMenu:[50, 100, 150, 200, 300],
        "sAjaxDataProp": "data.data",
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => row.sponsor + ' - ' + row.category},
            {data: "outpatients"},
            {data: "inpatients"},
            {data: "observations"},
            {data: "patientsCount"},
        ]
    })

    return visitsSummaryTable
}

const getPatientsBySponsorTable = (tableId, sponsorId, modal, date) => {
    const patientsBySponsorTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/patients/load/bysponsor', data: {
            'sponsorId': sponsorId,
            'date'     : date
        }},
        orderMulti: true,
        search:true,
        searchDelay: 1000,
        columns: [
            {data: "card"},
            {data: "name"},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: "createdAt"},
            {data: "count"},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        patientsBySponsorTable.destroy()
    })

    return patientsBySponsorTable
}

const getVisitsTable = (tableId, startDate, endDate, filterListBy) => {
    const visitsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax: {url: '/patients/load/visits', data: {
            'startDate'      : startDate, 
            'endDate'        : endDate, 
            'filterListBy'   : filterListBy, 
        }},
        orderMulti: true,
        search:true,
        searchDelay: 1000,
        lengthMenu:[50, 100, 150, 200, 250],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            },
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
        ],
        columns: [
            {data: "came"},
            {
                visible: false,
                data: "seen"
            },
            {data: "patientType"},
            {data: "patient"},
            {data: "phone"},
            {data: "address"},
            {
                visible: false,
                data: "state"
            },
            {data: "sex"},
            {data: "age"},
            {data: "nok"},
            {data: "nokPhone"},
            {data: "status"},
            {
                visible: false,
                data: "sponsor"
            },
            {
                visible: false,
                data: "sponsorCategory"
            },
            {data: "doctor"},
            {data: row => `
                        <a class="consultationDetailsBtn tooltip-test text-dark" title="details" href="#"  data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }" data-ancregid="${row.ancRegId}">
                            ${row.diagnosis}
                        </a>
                `},
        ]
    })

    return visitsTable
}

const getPrePatientsTable = (tableId) => {
    const prePatientsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/prepatients/load',
        orderMulti: true,
        lengthMenu:[50, 100, 150, 200, 300],
        search:true,
        searchDelay: 1000,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        columns: [
            {data: "card"},
            {data: "patient"},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: row => `<span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span>`},
            {data: "category"},
            {data: "createdAt"},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    
                        return `
                        <div class="d-flex flex-">
                            <button class="ms-1 btn btn-outline-primary confirmBtn tooltip-test" title="confirm" data-id="${ row.id }">
                                <i class="bi bi-check-square-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                }}
        ]
    })

    return prePatientsTable
}

export {getSponsorsTable, getAllPatientsTable, getNewRegisteredPatientsTable, getSexAggregateTable, getAgeAggregateTable, getVisitsSummaryTable, getPatientsBySponsorTable, getVisitsTable, getPrePatientsTable}