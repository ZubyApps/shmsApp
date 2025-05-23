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
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
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
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
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
            {data: "prescriptions"},
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
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
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
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
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
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
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
            $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
            $( api.column(11).footer() ).html(account.format(api.column( 11, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "hmoOfficer"},
            {data: "dateOfEmployment"},
            {data: "visitsInitiated"},
            {data: "patients"},
            {data: "verified"},
            {data: "treated"},
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
        searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        lengthMenu:[20, 40, 80, 120, 200],
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(2).footer() ).html(account.format(api.column( 2, {page:'current'} ).data().sum()));
            $( api.column(3).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
            $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
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

const getNursesShiftPerformanceTable = (tableId, department, startDate, endDate, date) => {
    const nursesShiftPerformanceTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/shiftperformance/load', data: {
            'department' : department,
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date 
            }
        },
        orderMulti: true,
        search:true,
        searchDelay: 500,
        lengthMenu:[20, 40, 80, 120, 200],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            },
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
        ],
        columns: [
            {data: "shift"},
            {data: row => row.start + ' - ' + row.end},
            {
                visible: false,
                data: "injectablesChartRate"
            },
            {
                visible: false,
                data: "injectablesGivenRate"
            },
            {
                visible: false,
                data: "othersChartRate"
            },
            {
                visible: false,
                data: "othersGivenRate"
            },
            {
                visible: false,
                data: "firstMedRes"
            },
            {
                visible: false,
                data: "firstServRes"
            },
            {
                visible: false,
                data: "firstVitalsRes"
            },
            {
                visible: false,
                data: "medicationTime"
            },
            {
                visible: false,
                data: "serviceTime"
            },
            {
                visible: false,
                data: "intpatientVitalsCount"
            },
            {
                visible: false,
                data: "outpatientVitalsCount"
            },
            {data: row => `
                <div class="d-flex">
                        <button type="submit" class="ms-1 btn btn-outline-white staffBtn tooltip-test" title="approve" data-id="${row.id}">
                                ${row.staff ?? ''}
                        </button>
                        <input class="ms-1 form-control staffInput d-none" id="staffInput" value="${row.staff}">
                    </div>
                   `},
            {data: row => `<button type="button" id="newPatient" class="btn p-0 " data-bs-toggle="dropdown" aria-expanded="false">
            <div class="progress" role="progressbar" aria-label="sponsor bill" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height: 40px">
            <div class="progress-bar text-dark fw-semibold fs-6 overflow-visible bg-${row.performance <= 45 ? 'danger' : row.performance > 45 && row.performance < 65 ? 'warning' : row.performance >= 65 && row.performance <= 91 ? 'primary' : 'success'}-subtle px-1" style="width: ${row.performance}%;"> Performance ${row.performance}% </div>
            </div>
        </button>`},
            {data: row => row.performance > 85 ? 'Yes' : 'No'},
        ]
    })

    return nursesShiftPerformanceTable
}

export {getDoctorsActivityTable, getNursesActivityTable, getLabTechActivityTable, getPharmacyTechActivityTable, getHmoOfficersActivityTable, getBillOfficersActivityTable, getNursesShiftPerformanceTable}