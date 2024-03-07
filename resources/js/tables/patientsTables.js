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
            // $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row =>  `<span class="btn text-decoration-underline showVisitisBtn" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}">${row.sponsor}</span>`},
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
            // $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "sex"},
            {data: "patientsCount"},
            // {data: "category"},
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
        },
        columns: [
            {data: "sex"},
            {data: "under5"},
            {data: "fiveTo12"},
            {data: "thirteenTo18"},
            {data: "eighteenTo50"},
            {data: "above50"},
            {data: row => +row.under5 + +row.fiveTo12 + +row.thirteenTo18 + +row.eighteenTo50 + +row.above50}
        ]
    })

    return totalPatientsTable
}

export {getSponsorsTable, getAllPatientsTable, getTotalPatientsTable, getSexAggregateTable, getAgeAggregateTable}