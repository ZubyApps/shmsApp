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

export {getSponsorsTable, getAllPatientsTable}