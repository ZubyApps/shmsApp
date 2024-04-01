import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})


const getDoctorsActivityTable = (tableId, designation, startDate, endDate, date) => {
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
            {data: "doctor"},
            {data: "dateOfEmployment"},
            {data: "visitsInitiated"},
            {data: "firstConsultations"},
            {data: "consultations"},
            {data: "prescriptions"},
            {data: "discountinued"},
            {data: "surgeryNotes"},
            {data: "vitalSigns"},
            {data: "AncVitalSigns"},
        ]
    })

    return doctorsActivityTable
}

const getNursesActivityTable = (tableId, designation, startDate, endDate, date) => {
    const nursesActivityTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/users/nurses', data: {
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
            {data: "nurse"},
            {data: "dateOfEmployment"},
            {data: "vitalSigns"},
            {data: "AncVitalSigns"},
            {data: "discountinued"},
            {data: "deiveryNotes"},
            {data: "charted"},
            {data: "served"},
            {data: "nursingCharts"},
            {data: "nursesReports"},
        ]
    })

    return nursesActivityTable
}

const getLabTechActivityTable = (tableId, designation, startDate, endDate, date) => {
    const nursesActivityTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/users/labtechs', data: {
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
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "labTech"},
            {data: "dateOfEmployment"},
            {data: "results"},
        ]
    })

    return nursesActivityTable
}

const getPharmacyTechActivityTable = (tableId, designation, startDate, endDate, date) => {
    const pharmacyTechsActivityTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/users/pharmacytechs', data: {
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
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 3, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "pharmacyTech"},
            {data: "dateOfEmployment"},
            {data: "rxBilled"},
            {data: "rxDispensed"},
        ]
    })

    return pharmacyTechsActivityTable
}

const getHmoOfficersActivityTable = (tableId, designation, startDate, endDate, date) => {
    const hmoOfficersActivityTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/users/hmoofficers', data: {
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
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
            $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
            $( api.column(9).footer() ).html(account.format(api.column( 9, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "hmoOfficer"},
            {data: "dateOfEmployment"},
            {data: "visitsInitiated"},
            {data: "patients"},
            {data: "verified"},
            {data: "closedAndOpened"},
            {data: "billsProcessed"},
            {data: "rxHmoBilled"},
            {data: "rxApproved"},
            {data: "rxRejected"},
            {data: "rxPaid"},
        ]
    })

    return hmoOfficersActivityTable
}

const getBillOfficersActivityTable = (tableId, designation, startDate, endDate, date) => {
    const billOfficersActivityTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reports/users/billofficers', data: {
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
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "billOfficer"},
            {data: "dateOfEmployment"},
            {data: "patients"},
            {data: "visitsInitiated"},
            {data: "closedAndOpened"},
            {data: "thirdPartyServices"},
            {data: "payments"},
            {data: row => account.format(row.paymentsTotal)},
        ]
    })

    return billOfficersActivityTable
}

export {getDoctorsActivityTable, getNursesActivityTable, getLabTechActivityTable, getPharmacyTechActivityTable, getHmoOfficersActivityTable, getBillOfficersActivityTable}