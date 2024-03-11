import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

const getDistribution1Table = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const distribution1Table = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/reports/patients/dist1',
        orderMulti: true,
        search:true,
        searching: false,
        lengthChange:false,
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row =>  `<span class="btn text-decoration-underline showPatientsBtn" data-id="${row.id}" data-category="${row.category}">${row.category}</span>`},
            {data: "sponsorCount"},
            {data: "female"},
            {data: "male"},
            {data: "patientsCount"},
        ]
    })

    return distribution1Table
}

const getDistribution2Table = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const distribution2Table = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/reports/patients/dist2',
        orderMulti: true,
        search:true,
        lengthMenu:[40, 80, 120, 160, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row =>  `<span class="btn text-decoration-underline showPatientsBtn" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}">${row.sponsor}</span>`},
            {data: "female"},
            {data: "male"},
            {data: "patientsCount"},
            {data: "category"},
        ]
    })

    return distribution2Table
}

const getBySponsorTable = (tableId, urlSuffix, sponsorId, modal, startDate, endDate) => {
    const patientsBySponsorTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/patients/${urlSuffix}`, data: {
            'sponsorId': sponsorId,
            'startDate' : startDate, 
            'endDate'   : endDate,
        }},
        orderMulti: true,
        search:true,
        columns: [
            {data: "patient"},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: "count"},
            {data: "totalHms"},
            {data: "totalHmo"},
            {data: "totalNhis"},
            {data: "totalPaid"},
            {data: "outstanding"},
        ]
    })

    modal._element.addEventListener('hidden.bs.modal', function () {
        patientsBySponsorTable.destroy()
    })

    return patientsBySponsorTable
}

const getFrequencyTable = (tableId) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const frequencyTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  '/reports/patients/frequency',
        orderMulti: true,
        search:true,
        lengthMenu:[10, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "patient"},
            {
                sortable: false,
                data: "age"},
            {
                sortable: false,
                data: "phone"},
            {
                sortable: false,
                data: "sponsor"},
            {
                sortable: false,
                data: "category"},
            {data: "visitCount"},
            {
                sortable: false,
                data: "totalHmsBill"},
            {
                sortable: false,
                data: "totalHmoBill"},
            {
                sortable: false,
                data: row => function () {
                if (row.category == 'NHIS'){
                    return row.totalNhisBill
                }
                return 0
            }},
            {data: "totalPaid"},
        ]
    })

    return frequencyTable
}

const getRegBySponsorTable = (tableId, startDate, endDate) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const regSummaryTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/patients/regsummary', data: {
            'startDate' : startDate, 
            'endDate'   : endDate, 
        }},
        orderMulti: true,
        search:true,
        lengthMenu:[40, 80, 120, 160, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row =>  `<span class="btn text-decoration-underline showPatientsByMonthBtn" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}">${row.sponsor}</span>`},
            {data: "category"},
            {data: "female"},
            {data: "male"},
            {data: "patientCount"},
        ]
    })

    return regSummaryTable
}

export {getDistribution1Table, getDistribution2Table, getBySponsorTable, getFrequencyTable, getRegBySponsorTable}