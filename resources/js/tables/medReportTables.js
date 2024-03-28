import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatusX, sponsorAndPayPercent } from '../helpers';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
// import pdfFonts from 'pdfmake/build/vfs_fonts'
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
// pdfMake.vfs = pdfFonts.pdfMake.vfs;
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

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
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
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

const getDischargeReasonTable = (tableId, filterBy, modal, startDate, endDate, date) => {
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

    const dischargeReasonTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/reports/medservices/bydischarge', data: {
            'filterBy'  : filterBy,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date
        }},
        orderMulti: true,
        search:true,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: preparedColumns
    });

    modal._element.addEventListener('hidden.bs.modal', function () {
        modal._element.querySelector('#dischargeMonth').value = ''
        modal._element.querySelector('#from').value = ''
        modal._element.querySelector('#to').value = ''
        dischargeReasonTable.destroy()
    })

    return dischargeReasonTable
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
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        lengthMenu:[20, 40, 80, 120, 200],
        "sAjaxDataProp": "data.data",
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(1).footer() ).html(account.format(api.column( 1, {page:'current'} ).data().sum()));
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showVisitsBtn tooltip-test" title="show visits" data-reason="${row.reason}">${row.reason ?? 'Not discharged'}</span>`},
            {data: "sponsorCount"},
            {data: "patientsCount"},
            {data: "visitCount"},
        ]
    })

    return dischargeSummaryTable
}

export {getMedServiceSummaryTable, getByResourceTable, getNewBirthsTable, getDischargeReasonTable, getDischargeSummaryTable}