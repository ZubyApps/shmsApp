import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

const getDoctorsActivityTable = (tableId, designation, startDate, endDate, date) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const doctorsActivityTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/users/doctors', data: {
            'designation' : designation, 
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
            // $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "doctor"},
            {data: "dateOfEmployment"},
            {data: "visitsInitiated"},
            {data: "firstConsultations"},
            {data: "consultations"},
            {data: "prescriptions"},
            {data: "surgeryNotes"},
            {data: "vitalSigns"},
            {data: "AncVitalSigns"},
        ]
    })

    return doctorsActivityTable
}

const getByResourceTable = (tableId, resourceId, modal, startDate, endDate) => {
    const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

    const patientsByResourceTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: `/reports/pharmacy/byresource`, data: {
            'resourceId': resourceId,
            'startDate' : startDate, 
            'endDate'   : endDate,
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
            {data: "Hmsbill"},
            {data: "Hmobill"},
            {data: "paid"},
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

export {getDoctorsActivityTable, getByResourceTable}