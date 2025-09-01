import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, admissionStatusX, deferredCondition, detailsBtn, displayPaystatus, getOrdinal, selectReminderOptions, sponsorAndPayPercent, flagSponsorReason, flagPatientReason, flagIndicator, searchPlaceholderText, searchMin, preSearch, searchDecider, visitType } from "../helpers";
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getWaitingTable = (tableId) => {
    const waitingTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  '/visits/load/waiting',
        orderMulti: true,
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: 'No patient is waiting',
            searchPlaceholder: searchPlaceholderText
        },
        columns: [
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "sex"},
            {data: "age"},
            {data: row => `<div><span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span></div>${row.visitType == 'ANC' ? visitType(row, null, 50) : ''}`},
            {data: row => `<span class="tooltip-test" title="initiated by ${row.initiatedBy}">${row.came}</span>`},
            {data: "waitingFor"},
            {data: "doctor"},
            {data: row => 
                        `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary ${row.vitalSigns ? '' : 'd-none'} tooltip-test" title="View VitalSigns" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                    </div>
                        `   
            },
            {data: row => () => {
                const show = row.prescriptions > 0 || row.payments > 0 ? false : true
                    return  `
                    <div class="dropdown ms-1">
                        <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="remove" data-bs-toggle="dropdown" href="" >
                            <i class="bi bi-file-minus-fill"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a role="button" class="dropdown-item closeVisitBtn tooltip-test" title="close visits" id="closeVisitBtn" data-id="${ row.id }">
                                    <i class="bi bi-lock-fill text-primary"></i> Close Visit
                                </a>
                            </li>
                            <li>
                                <a role="button" class="dropdown-item deleteVisitBtn tooltip-test ${show ? '' : 'd-none'}" title="delete visit" id="deleteVisitBtn" data-id="${ row.id }">
                                    <i class="bi bi-x-circle-fill text-primary"></i> Delete Visit
                                </a>
                            </li>
                        </ul>
                    </div>
                        `
                
            }
                
            },
        ]
    });

    waitingTable.on('draw.init', searchMin(waitingTable, tableId, 2))

    return waitingTable
}

const getVerificationTable = (tableId) => {
    const verificationTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  '/hmo/verification/list',
        orderMulti: true,
        search:true,
        // searchDelay: 500,
        lengthMenu:[25, 50, 100, 150, 200],
        language: {
            emptyTable: 'No verification requested',
            searchPlaceholder: searchPlaceholderText
        },
        columns: [
            {data: "came"},
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}">${row.patient}</span>`},
            {data: "sex"},
            {data: "age"},
            {data: row => 
                        `
                    <div><button class="btn changeSponsorBtn ${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}" data-id="${ row.id }" data-patient="${ row.patient }" data-phone="${ row.phone }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-staffid="${ row.staffId }">${row.sponsor}</button></div>${row.visitType == 'ANC' ? visitType(row, null, 50) : ''}`
            },
            {data: "30dayCount"},
            {data: "doctor"},
            {data: row => 
                        `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary verifyPatientBtn tooltip-test" title="${row.status ? row.status : 'verify'}" data-id="${ row.id }" data-patient="${ row.patient }" data-phone="${ row.phone }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-staffid="${ row.staffId }" data-status="${ row.status }" data-codeText="${ row.codeText }">
                            ${row.status ? row.status : 'Verify'}
                        </button>
                    </div>
                        `
                
            },
        ]
    });

    verificationTable.on('draw.init', searchDecider(verificationTable, tableId, 2))

    return verificationTable
}

const getAllHmoPatientsVisitTable = (tableId, filter) => {
    const allHmoPatientsVisitTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/consulted', data: {
            'filterBy': filter 
        }},
        orderMulti: true,
        search:true,
        // searchDelay: 500,
        lengthMenu:[100, 150, 200, 250, 300, 500],
        language: {
            emptyTable: "No patient",
            searchPlaceholder: searchPlaceholderText
        },
        columns: [
            {data: "came"},
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: row => sponsorAndPayPercent(row)},
            {data: row => () => {
                return row.visitType == "ANC" ? row.thirtyDayCount+getOrdinal(row.thirtyDayCount)+' ANC' : row.thirtyDayCount
                }
            },
            {data: row =>  `
                        <div class="d-flex justify-content-center">
                            <button class=" btn btn-${row.viewedAt ? '' : 'primary'} treatVisitBtn tooltip-test ${row.viewedAt ? 'p-0' : ''}" title="Treat Visit" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${row.viewedAt ? row.viewedBy : 'Treat'}
                            </button>
                        </div>`                
            },
            {data: row =>  `
                        <div class="d-flex justify-content-center">
                            <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${row.labDone}<i class="bi bi-eyedropper"></i>${row.labPrescribed}
                            </button>
                        </div>`                
            },
            {data: row => admissionStatusX(row)},
            {
                sortable: false,
                data: row => `
                <div class="dropdown">
                    <a class="btn btn-outline-primary tooltip-test text-decoration-none ${row.closed ? 'px-1': ''}" title="${row.closed ? 'record closed by ' + row.closedBy : ''}" data-bs-toggle="dropdown">
                        More${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}
                    </a>
                        <ul class="dropdown-menu">
                        <li>
                            <a class=" btn btn-outline-primary dropdown-item consultationDetailsBtn tooltip-test" title="details"  data-id="${ row.id }" data-patientId="${ row.patientId }" data-visitType="${ row.visitType }" data-ancregid="${row.ancRegId}">
                                Details
                            </a>
                            <a class="dropdown-item patientBillBtn btn tooltip-test" title="patient's bill"  data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-hmodoneby="${ row.hmoDoneBy }" data-age="${ row.age }" data-sex="${ row.sex }" data-staffId="${ row.staffId }" data-phone="${ row.phone }">
                                ${row.hmoDoneBy ? 'Bill sent <i class="bi bi-check-circle-fill tooltip-test text-primary" title="sent"></i>' : 'Make bill'}
                            </a>
                            <a class="dropdown-item btn btn-outline-primary medicalReportBtn" data-id="${ row.id }" data-patient="${ row.patient }" data-patientid="${ row.patientId }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}" data-age="${ row.age }" data-sex="${ row.sex }">Report/Refer/Result</a>
                            <a class="dropdown-item closeVisitBtn btn tooltip-test" title="${row.closed ? 'closed': 'close'}"  data-id="${ row.id }">
                            ${row.closed ? '': 'Close'}
                            </a>
                        </li>
                    </ul>
                </div>
                `
            },
        ]
    });

    allHmoPatientsVisitTable.on('draw.init', searchDecider(allHmoPatientsVisitTable, tableId, 2))

    return allHmoPatientsVisitTable
}

const getApprovalListTable = (tableId, sponsor) => {
    const approvalListTable =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/approval/list', data: {
            'sponsor': sponsor 
        }},
        orderMulti: true,
        search:true,
        searchDelay: 500,
        lengthMenu:[25, 50, 100, 150, 200],
        language: {
            emptyTable: 'No items for approval',
            searchPlaceholder: searchPlaceholderText
        },
        drawCallback: function (settings) {
            var api = this.api() 
        },
        columns: [
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: row => `<span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span>`},
            {data: "doctor"},
            {data: "prescribed"},
            {data: "diagnosis"},
            {data:row => () => {
                return `<span class="text-primary fw-semibold position-relative">${row.resource} ${row.resourceFlagged ? `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">flagged</span>` : ''}</span> ${row.approved ? 
                    `<i class="ms-1 text-primary bi bi-check-circle-fill"></i>` : row.rejected ? 
                    `<i class="ms-1 text-danger bi bi-x-circle-fill"></i>` : ''}` 
            }},
            {data: row => account.format(row.resourcePrice)},
            {data: "prescription"},
            {data: "note"},
            {data: "quantity"},
            {data: "totalQuantity"},
            {data: row => account.format(row.hmsBill)},
            {data: "hmsBillDate"},
            {
                sortable: false,
                data: row =>  () => {
                    if (row.approved || row.rejected){
                        return `
                        <div class="dropdown">
                            <a class="btn text-black tooltip-test text-decoration-none approvedBy" title="User" data-bs-toggle="dropdown">
                                ${row.approvedBy || row.rejectedBy} <i class="bi bi-chevron-double-down"> </i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item resetBtn btn tooltip-test" title="reset" data-id="${ row.id }">
                                    <i class="bi bi-arrow-clockwise text-primary resetBtn" ></i> Reset
                                    </a>
                                </li>
                            </ul>
                        </div>
                        `
                    }
                    return `
                    <div class="d-flex">
                        <button type="submit" class="ms-1 btn btn-outline-primary approveBtn tooltip-test" title="approve" data-id="${row.id}">
                                <i class="bi bi-check-circle"></i>
                        </button>
                        <button type="submit" class="ms-1 btn btn-outline-danger rejectBtn tooltip-test" title="reject" data-id="${ row.id}">
                                <i class="bi bi-x-circle"></i>
                        </button>
                        <input class="ms-1 form-control noteInput d-none" id="noteInput">
                    </div>
                    `    
                }
            },
        ]
    });

    approvalListTable.on('draw.init', searchDecider(approvalListTable, tableId, 2))

    return approvalListTable
}

const getVisitPrescriptionsTable = (tableId, visitId, modal) => {
    const visitPrescriptionsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/visit/prescriptions', data: {
            'visitId': visitId 
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200],
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: "No Bills",
            searchPlaceholder: searchPlaceholderText
        },
        drawCallback: function (settings) {
            var api = this.api()                
                $( 'tr:eq(0) td:eq(7)', api.table().footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
                $( 'tr:eq(0) td:eq(8)', api.table().footer() ).html(account.format(api.column(8, {page:'current'} ).data().sum()));
                
                const value = (account.format(api.column( 8, {page:'current'} ).data().sum() - (api.column( 7, {page:'current'} ).data().sum())))
                $( 'tr:eq(0) td:eq(9)', api.table().footer() ).html(`<span class="text-${value < 0 ? 'danger': value == 0 ? 'primary': 'success'}">Diff: ${value}</span>`);
                
                $( 'tr:eq(1) td:eq(7)', api.table().footer() ).html(account.format(api.data()[0].paidHms));
                $( 'tr:eq(1) td:eq(8)', api.table().footer() ).html(account.format(api.data()[0].paidHms));
                
                $( 'tr:eq(2) td:eq(7)', api.table().footer() ).html(account.format((api.column( 7, {page:'current'} ).data().sum() - api.data()[0].paidHms)));
                $( 'tr:eq(2) td:eq(8)', api.table().footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum() - api.data()[0].paidHms));

        },
        columns: [
            {data: "doctor"},
            {data: "prescribed"},
            {data: row => () => {
                return row.approved ? row.resource + `<i class="ms-1 text-primary bi bi-check-circle-fill"></i>` : 
                       row.rejected ? row.resource + `<i class="ms-1 text-danger bi bi-x-circle-fill"></i>` :
                       row.resource
            } },
            {data: "diagnosis"},
            {data: "prescription"},
            {data: "note"},
            {data: "quantity"},
            {data: "hmsBill",
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                                <span>${data}</span>
                            </div>
                            `}
            },
            {
                data: 'hmoBill',
                render: (data, type, row) => {
                    return ` <div class="d-flex justify-content-center">
                    <span class="${row.hmoDoneBy ? 'unmarkSent' : 'hmoBillSpan'} btn btn-white" data-id="${row.id}" data-hmodone"${row.hmoDoneBy}">${row.rejected && !data ? 'Not approved' : data ?? 'Bill'}</span>
                    <input class="ms-1 form-control hmoBillInput d-none" id="hmoBillInput" type="number" value="${data == 0 ? '' : data}">
                </div>
                `}
            },
            {data: "hmoBillBy"},
        ]
    });

    modal._element.addEventListener('hidden.bs.modal', function () {
        visitPrescriptionsTable.destroy()
    })

    visitPrescriptionsTable.on('draw.init', searchMin(visitPrescriptionsTable, tableId, 2))

    return visitPrescriptionsTable
}

const getSentBillsTable = (tableId, startDate, endDate, date, filterByOpen) => {
    const sentBillsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/sentbills', data: {
            'startDate'      : startDate, 
            'endDate'        : endDate,
            'date'           : date,
            'filterByOpen'   : filterByOpen, 
        }},
        orderMulti: true,
        lengthMenu:[25, 50, 100, 150, 200, 500],
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
        language: {
            emptyTable: "No patient"
        },
        drawCallback: function (settings) {
            var api = this.api()
            $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
            $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: "came"},
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: row => sponsorAndPayPercent(row)},
            {data: "doctor"},
            {data: "diagnosis"},
            {data: "sentBy"},
            {data: "totalHmsBill"},
            {data: "totalHmoBill"},
            {
                sortable: false,
                data: row => `
                <div class="d-flex justify-content-center">
                    <button class="ms-1 btn btn-outline-primary patientBillBtn tooltip-test" title="See bill" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                        <i class="bi bi-eye-fill"></i>
                    </button>   
                    <button class="ms-1 btn btn-outline-primary closeVisitBtn tooltip-test" title="${row.closed ? 'closed': 'close'}" data-id="${ row.id }">
                    ${row.closed ? '<i class="bi bi-lock-fill"></i>': 'Close'}
                    </button>   
                </div>
                `
            },
        ]
    });

    sentBillsTable.on('draw.init', searchDecider(sentBillsTable, tableId, 2))

    return sentBillsTable
}

const getHmoReportsTable = (tableId, category, startDate, endDate, date) => {
    const sponsors = ['NHIS', 'HMO']
    const reportSummayTable =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/summary', data: {
            'category': category,
            'startDate': startDate,
            'endDate': endDate,
            'date'   : date,
        }},
        orderMulti: false,
        lengthMenu:[25, 50, 100, 150, 200],
        searchDelay: 500,
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
        language: {
            emptyTable: category ? '<span class="colour-change2">This is comparism mode, search for a sponsor to see records</span>' : 'No report',
            searchPlaceholder: searchPlaceholderText
        },
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
                $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showVisitsBtn ${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}" ${row.yearMonth ? `data-yearmonth="${row.year + '-' + row.month}"` : ''}>${row.sponsor + '-' + row.category}</span>`},
            {data: row => row.monthName + ' ' + row.year},
            {
                visible: false,
                data: "visitsCount"
            },
            {
                visible: false,
                data: "billsSent"
            },
            {data: row => account.format(row.totalHmoBill)},
            {data: row => account.format(row.totalHmsBill)},
            {data: row => account.format(row.totalHmoBill - row.totalHmsBill)},
            {data: row => account.format(row.totalPaid)},
            {data: row => account.format(+row.totalCapitation)},
            {data: row => account.format((+row.totalPaid + +row.totalCapitation + +row.discount) - +row.totalHmsBill)},
            {data: row => account.format((+row.totalPaid + +row.totalCapitation + +row.discount) - +row.totalHmoBill)},
            {data: row => () => {
                if (row.totalHmsBill == 0){
                    return 'No bill'
                }
                let debt = (((+row.totalHmsBill - (+row.totalPaid + +row.totalCapitation + +row.discount))/row.totalHmsBill) * 100).toFixed(1)
                return  `<span class="text-${debt >= 10 ? 'danger': debt <= 10 && debt >= 7 ? 'primary' : 'success' } fw-bold">${debt + '%'}</span>`
                }
            },
            {data: row => () => {
                if (!sponsors.includes(row.category)){
                    return 'N/A'
                }
                if (row.totalHmoBill == 0){
                    return 'No bill'
                }
                let debt = (((+row.totalHmoBill - (+row.totalPaid + +row.totalCapitation + +row.discount))/row.totalHmoBill) * 100).toFixed(1)
                return  `<span class="text-${debt >= 10 ? 'danger': debt <= 10 && debt >= 7 ? 'primary' : 'success' } fw-bold">${debt + '%'}</span>`
                }
            },
            {data: "reminderSet",
                render: (data, type, row) => {
                    if (data){
                        return data                       
                    }
                    return `
                            <button class="px-2 btn btn-${data ? 'outline-' :''}primary registerBillSent tooltip-test ${category == 'Compare' ? 'd-none': ''}" title="" data-id="${ row.id }" data-sponsor="${row.sponsor}" data-monthYear="${row.monthYear}">
                                ${data ? 'Bill Sent' :'Not Sent'}
                            </button>
                            
                        `}
            },
        ]
    });
    reportSummayTable.on('draw.init', searchDecider(reportSummayTable, tableId, 2, 'hmoSponsors'))

    return reportSummayTable
}

const getHmoReconciliationTable = (tableId, sponsorId, modal, from, to, date) => {
    const reconciliationTable =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/reconciliation', data: {
            'sponsorId': sponsorId,
            'from'  : from,
            'to'    : to,
            'date'  : date,
        }},
        paging: true,
        orderMulti: false,
        lengthMenu:[10, 20, 50, 100],
        // searchDelay: 500,
        language: {
            emptyTable: 'No Visits',
            searchPlaceholder: searchPlaceholderText
        },
        drawCallback: function (settings) {
            var api = this.api()
                $( api.column(4).footer() ).html(account.format(api.column( 4, {page:'current'} ).data().sum()));
                $( api.column(5).footer() ).html(account.format(api.column( 5, {page:'current'} ).data().sum()));
                $( api.column(6).footer() ).html(account.format(api.column( 6, {page:'current'} ).data().sum()));
                $( api.column(7).footer() ).html(account.format(api.column( 7, {page:'current'} ).data().sum()));
                $( api.column(8).footer() ).html(account.format(api.column( 8, {page:'current'} ).data().sum()));
        },
        rowCallback: (row, data) => {
                row.classList.add('table-light')
            return row
        },
        columns: [
            {data: "came"},
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "consultBy"},
            {data: row =>  `<span class="text-primary fw-semibold">${row.diagnosis}</span>`},
            {data: row => account.format(row.totalHmoBill)},
            {data: row => account.format(row.totalHmsBill)},
            {data: row => account.format(row.totalNhisBill)},
            {data: row => account.format(row.totalCapitation)},
            {data: row => account.format(row.totalPaid)},
            {data : row => 
                `<div class="d-flex text-secondary">
                    <span class="btn payBulkSpan ${row.sponsorCategory == 'NHIS' ? 'd-none' : ''}" data-id="${row.id}" data-totalhmobill="${row.totalHmoBill}" data-totalpaid="${row.totalPaid}">Pay Bulk</span>
                    <input class="ms-1 form-control payBulkInput d-none text-secondary" type="number" style="width:6rem;" value="${row.totalPaid == 0 ? '' : row.totalPaid}" name="bulkPayment" id="bulkPayment">
                </div>`
            }
        ]
    });

    function format(data, tableId) {
        // `d` is the original data object for the row
        const credit = data?.sponsorCategoryClass == 'Credit'
        const NHIS = data?.sponsorCategory == 'NHIS'
        const prescriptions = data?.prescriptions
        let count = 1
                if (prescriptions?.length > 0) {
                    let child = `<table class="table align-middle ">
                                            <thead >
                                                <tr class="fw-semibold fs-italics">
                                                    <td class="text-secondary">S/N</td>
                                                    <td class="text-secondary">Prescribed</td>
                                                    <td class="text-secondary">Item</td>
                                                    <td class="text-secondary">Prescription</td>
                                                    <td class="text-secondary">Qty</td>
                                                    <td class="text-secondary">Note</td>
                                                    <td class="text-secondary">HMO Staff</td>
                                                    <td class="text-secondary">HMO Bill</td>
                                                    <td class="text-secondary">HMS Bill</td>
                                                    <td class="text-secondary">NHIS Bill</td>
                                                    <td class="text-secondary">Capitation</td>
                                                    <td class="text-secondary">Paid</td>
                                                </tr>
                                            </thead>
                                        <tbody>`
                                prescriptions.forEach(p => {
                                        child += `
                                            <tr>
                                                <td class="text-secondary">${count++}</td>
                                                <td class="text-secondary">${p.prescribed}</td>                
                                                <td class="text-${p.rejected ? 'danger' : 'primary'} fw-semibold">${p.item +' '+ displayPaystatus(p, credit, NHIS)}</td>                
                                                <td class="text-secondary">${p.prescription}</td>                
                                                <td class="text-secondary">${p.qtyBilled+' ('+p.unit +')'}</td>
                                                <td class="text-secondary">${p.note}</td>
                                                <td class="text-primary fst-italic">${p.hmoNote ? p.statusBy+'-'+p.hmoNote: p.statusBy}</td>
                                                <td class="text-secondary fw-semibold">${p.hmoBill}</td>
                                                <td class="text-secondary fw-semibold">${p.hmsBill}</td>
                                                <td class="text-secondary fw-semibold">${p.nhisBill}</td>
                                                <td class="text-secondary fw-semibold">${p.capitation}</td>
                                                <td class="text-secondary"> 
                                                    <div class="d-flex text-secondary">
                                                        <span class="btn payBtnSpan" data-id="${p.id}">${p.paid ? p.paid : 'Pay'}</span>
                                                        <input class="ms-1 form-control payInput d-none text-secondary" type="number" style="width:6rem;" value="${p.paid == 0 ? '' : p.paid}" name="amountPaid" id="amountPaid">
                                                        <span class="ms-1 ${p.paid > 0 ? '' : 'd-none'} btn addSpanBtn text-primary" data-id="${p.id}">Add </span> <input class="ms-1 form-control addAmount d-none" type="number" style="width:6rem;" name="addAmount" id="addAmount">
                                                    </div>
                                                </td>
                                            </tr>
                                            `
                                    })
                            child += `</tbody>
                                </table>`
                    return (child);
                } else {
                   let noChild = `
                   <table class="table align-middle table-sm">
                        <tr>
                            <td align="center" colspan="8" class="text-secondary">
                                No prescriptions
                            </td>
                        </tr>
                    </table>
                   `
                   return (noChild)
                }
    }

    modal._element.addEventListener('hidden.bs.modal', function () {
        reconciliationTable.destroy()
    })

    reconciliationTable.on('draw', function() {
        searchMin(reconciliationTable, tableId, 2)
        reconciliationTable.rows().every(function () {
            let tr = $(this.node())
            let row = this.row(tr);
            this.child(format(row.data(), tableId)).show()
        })
    })

    reconciliationTable.on('click', 'tr', function (e) {
        let tr = e.target.closest('tr');
        let row = reconciliationTable.row(tr);
     
        if (row.child.isShown()) {
            row.child.hide();
        }
        else {
            row.child(format(row.data()), tableId).show();
        }
    });
    
    return reconciliationTable
}

const getNhisReconTable = (tableId, date) => {
    const nhisReconTable =  new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/hmo/capitation', data: {
            'date': date,
        }},
        orderMulti: false,
        searchDelay: 500,
        lengthMenu:[25, 50, 100, 150, 200],
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        language: {
            emptyTable: 'No Sponsors'
        },
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
                $( api.column(10).footer() ).html(account.format(api.column( 10, {page:'current'} ).data().sum()));
        },
        columns: [
            {data: row => `<span class="btn text-decoration-underline showVisitisBtn" data-id="${row.id}" data-sponsor="${row.sponsor}" data-category="${row.category}">${row.sponsor}</span>`},
            {data: "patientsR"},
            {data: "patientsC"},
            {data: "visitsC"},
            {data: "visitsP"},
            {data: "prescriptions"},
            {data: row => account.format(row.hmsBill)},
            {data: row => account.format(row.nhisBill)},
            {data: row => account.format(row.paid)},
            {data: "capitationPayment",
                render: (data, type, row) => {
                    if (data){
                        return account.format(data)
                    }
                    return `
                            <button class="ms-1 btn btn-outline-primary enterCapitationPaymentBtn tooltip-test" title="enter payment" data-id="${ row.id }" data-sponsor="${row.sponsor}" data-prescriptionCount="${row.prescriptions}" data-monthYear="${row.monthYear}">
                                Enter
                            </button>
                            
                        `}
            },
            {data: row => account.format(+row.paid + +row.capitationPayment - +row.hmsBill)},
        ]
    });

    return nhisReconTable
}

const getBillReminderTable = (tableId, startDate, endDate, date) => {
    const billReimndersTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/reminders/load/hmo', data: {
            'startDate' : startDate, 
            'endDate'   : endDate,
            'date'      : date,
            }
        },
        orderMulti: true,
        search:true,
        searchDelay: 500,
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
        lengthMenu:[20, 40, 80, 120, 200],
        columns: [
            {data: "sponsor"},
            {data: "monthSentFor"},
            {
                visible: false,
                data: "setFrom"
            },
            {data: row => `<span class="${row.daysAgo > row.maxDays && row.paid == 'Pending' ? 'fw-bold colour-change3' : ''} ${row.paid != 'Pending' ? 'd-none' : ''}">${row.daysAgo}</span>`},
            {data: "maxDays"},
            {data: "daysToPay"},
            {data: row => `<span class="${row.secondReminder ? '' : 'deleteFirstReminderBtn'}" data-id="${ row.id}">${row.firstReminder}</span>`},
            {
                visible: false,
                data: "firstDate"
            },
            {data: row => `<span class="${row.finalReminder ? '' : 'deleteSecondReminderBtn'}" data-id="${ row.id}">${row.secondReminder}</span>`},
            {
                visible: false,
                data: "secondDate"
            },
            {data: row => `<span class="deleteFinalReminderBtn" data-id="${ row.id}">${row.finalReminder}</span>`},
            {
                visible: false,
                data: "finalDate"
            },
            {data: "remind"},
            {data: row => () => {
                return `<span class="fw-bold ${row.paid == 'Pending' ? '' : 'deletePaidBtn'} ${row.paid == 'Pending' ? row.daysAgo > row.maxDays ? 'colour-change3' :'text-warning' : 'text-primary'}" data-id="${row.id}">${row.paid}</span> ${row.paid == 'Pending' ? 
                   `<i class=" bi-dash-circle-fill text-secondary"></i>` : `<i class="ms-1 text-primary bi bi-p-circle-fill tooltip-test" title="paid"></i>`}`
            }},
            {
                visible: false,
                data: "createdAt"
            },
            {
                visible: false,
                data: "setBy"
            },
            {
                visible: false,
                data: "comment"
            },
            {
                visible: false,
                sortable: false,
                data: row => () => {
                        return `
                        <div class="d-flex flex- ${row.paid != 'Pending' ? 'd-none' : ''}">
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBillReminderBtn tooltip-test" data-table="${tableId}" title="delete" data-id="${ row.id}">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                        `  
                }      
            },
        ]
    })

    return billReimndersTable
}

const getDueHmoRemindersTable = (tableId) => {
    const dueHmoRemindersTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/reminders/load/hmo/due', data: {
        }},
        orderMulti: true,
        search:true,
        searchDelay: 500,
        lengthMenu:[50, 100, 150, 200],
        language: {
            emptyTable: 'No reminders due'
        },
        drawCallback: function (settings) {
            var api = this.api() 
        },
        columns: [
            {data: "sponsor"},
            {data: "monthSentFor"},
            {data: "daysAgo"},
            {data: "maxDays"},
            {data: "firstReminder", 
                render: (data, type, row) => {
                    if (deferredCondition(data)){
                        return data
                    }
                    return  selectReminderOptions(row, 'firstReminderSelect')
                }
            },
            {data: "secondReminder", 
                render: (data, type, row) => {
                    if (deferredCondition(data)){
                        return data
                    }
                    return selectReminderOptions(row, 'secondReminderSelect')
                }
            },
            {data: "finalReminder", 
                render: (data, type, row) => {
                    if (deferredCondition(data)){
                        return data
                    }
                    return  selectReminderOptions(row, 'finalReminderSelect')
                }
            },
            {data: "paid", 
                render: (data, type, row) => {
                    if (data != 'Pending'){
                        return data
                    }
                    return  `<button class="btn btn-primary confirmedPaidBtn" data-id="${row.id}" data-sponsor="${row.sponsor}" data-monthYear="${row.monthSentFor}">Pay</button>`
                     //`<input class="ms-1 form-control confirmedPaidInput text-secondary" type="date" style="width:8rem;" data-id="${row.id}"> `
                }
            },
            {data: "comment"},
            {data: "setBy"},
        ]
    });

    return dueHmoRemindersTable
}

export {getWaitingTable, getVerificationTable, getAllHmoPatientsVisitTable, getApprovalListTable, getVisitPrescriptionsTable, getSentBillsTable, getHmoReportsTable, getHmoReconciliationTable, getNhisReconTable, getBillReminderTable, getDueHmoRemindersTable}