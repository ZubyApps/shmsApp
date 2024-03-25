import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatusX, sponsorAndPayPercent } from '../helpers';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getMedServiceSummaryTable = (tableId, startDate, endDate, date) => {

    const summaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/medservices/summary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
            }
        },
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showPatientsBtn tooltip-test" title="show patients" data-id="${row.id}" data-resource="${row.name}" data-subcategory="${row.subCategory}">${row.name}</span>`},
            {data: "subCategory"},
            {data: "prescriptions"},
            {data: "qtyPrescribed"},
        ]
    })

    return summaryTable
}

const getByResourceTable = (tableId, resourceId, modal, startDate, endDate, date) => {
    const patientsByResourceTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/medservices/byresource`, data: {
            'resourceId': resourceId,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "date"},
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "category"},
            {data: "diagnosis"},
            {data: "doctor"},
            {data: row => account.format(row.Hmsbill)},
            {data: row => account.format(row.Hmobill)},
            {data: row => account.format(row.paid)},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        modal._element.querySelector('#resourceMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
        patientsByResourceTable.destroy()
    })

    return patientsByResourceTable
}

const getNewBirthsTable = (tableId, startDate, endDate, date) => {
    const newBirthsTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/medservices/newbirths`, data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        columns: [
            {data: "date"},
            {data: "timeofDelivery"},
            {data: "modeOfDelivery"},
            {data: "mother"},
            {data: "age"},
            {data: row => row.sponsor + ' - ' + row.category},
            {data: "noteBy"},
            {data: "sex"},
        ]
    })

    return newBirthsTable
}

const getReferredOrDeceasedVisits = (tableId, filterBy, startDate, endDate, date) => {
    const preparedColumns = [
        {data: "date"},
        {data: "patient"},
        {data: "age"},
        {data: "sex"},
        {data: "phone"},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row => admissionStatusX(row)},
        {data: row => account.format(row.totalHmsBill)},
        {data: row => account.format(row.totalHmsPaid)},
        {data: row => account.format(row.totalHmsBill - row.totalHmsPaid)},
    ]

    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/reports/medservices/referredordeceased', data: {
            'filterBy'  : filterBy,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
        }},
        orderMulti: true,
        search:true,
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: preparedColumns
    });
}

const getDischargeSummaryTable = (tableId, startDate, endDate, date) => {
    const dischargeSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/medservices/dischargesummary`, data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[20, 40, 80, 120, 200],
        "sAjaxDataProp": "data.data",
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => row.reason ?? 'Not properly discharged'},
            {data: "sponsorCount"},
            {data: "patientsCount"},
            {data: "visitCount"},
            // {data: "age"},
            // {data: row => row.sponsor + ' - ' + row.category},
            // {data: "noteBy"},
            // {data: "sex"},
        ]
    })

    return dischargeSummaryTable
}

export {getMedServiceSummaryTable, getByResourceTable, getNewBirthsTable, getReferredOrDeceasedVisits, getDischargeSummaryTable}