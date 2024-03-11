import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

const getSponsorsTable = (tableId) => {
    const sponsorsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/sponsors/load',
        orderMulti: true,
        search:true,
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
        search:true,
        columns: [
            {data: "card"},
            {data: "name"},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "category"},
            {data: "createdAt"},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary initiateVisitBtn tooltip-test ${row.active > 0 ? 'd-none' : ''}" title="initiate visit" data-id="${ row.id }" data-patient="${ row.patient }">
                            <i class="bi bi-arrow-up-right-square-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary initiateVisitBtn tooltip-test ${row.active > 0 ? 'd-none' : ''}" title="initiate visit" data-id="${ row.id }" data-patient="${ row.patient }">
                                <i class="bi bi-arrow-up-right-square-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    } 
                }}
        ]
    })

    return allPatientsTable
}

const getTotalPatientsTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const totalPatientsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load/summary/sponsor',
        orderMulti: true,
        search:true,
        lengthMenu:[40, 80, 120, 160, 200],
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

    return totalPatientsTable
}

const getSexAggregateTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const totalPatientsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load/summary/sex',
        orderMulti: true,
        search:false,
        searching:false,
        lengthMenu:[40, 80, 120, 160, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "sex"},
            {data: "patientsCount"},
        ]
    })

    return totalPatientsTable
}
const getAgeAggregateTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const totalPatientsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load/summary/age',
        orderMulti: true,
        search:false,
        searching:false,
        lengthMenu:[40, 80, 120, 160, 200],
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

    return totalPatientsTable
}

const getVisitsSummaryTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const visitsSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/patients/load/summary/visits',
        orderMulti: true,
        search:true,
        lengthMenu:[40, 80, 120, 160, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "sponsor"},
            {data: "outpatients"},
            {data: "inpatients"},
            {data: "observations"},
            {data: "patientsCount"},
        ]
    })

    return visitsSummaryTable
}

const getPatientsBySponsorTable = (tableId, sponsorId, modal) => {
    const patientsBySponsorTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/patients/load/bysponsor', data: {
            'sponsorId': sponsorId,
        }},
        orderMulti: true,
        search:true,
        columns: [
            {data: "card"},
            {data: "name"},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: "count"},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        patientsBySponsorTable.destroy()
    })

    return patientsBySponsorTable
}

const getVisitsTable = (tableId, startDate, endDate) => {
    const visitsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax: {url: '/patients/load/visits', data: {
            'startDate' : startDate, 
            'endDate'   : endDate, 
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 160, 200],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            }
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

export {getSponsorsTable, getAllPatientsTable, getTotalPatientsTable, getSexAggregateTable, getAgeAggregateTable, getVisitsSummaryTable, getPatientsBySponsorTable, getVisitsTable}