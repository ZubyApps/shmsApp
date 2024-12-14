import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
import { flagIndicator, flagPatientReason, flagSponsorReason, getMinsDiff } from '../helpers';
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getAppointmentsTable = (tableId, filter, span) => {
    let [diffCount1, diffCount2] = [[], []]
    const appointmentTable = new DataTable(`#${tableId}`, {
        serverSide: true,
        ajax:  {url: '/appointments/load', data: {
            'filterBy' : filter
        }},
        orderMulti: true,
        lengthMenu:[20, 40, 80, 120, 200],
        search:true,
        language: {
            emptyTable: 'No pending appointments'
        },
        rowCallback: (row, data) => {
            const diff = getMinsDiff(new Date(), new Date(data.rawDateTime))
                if (diff > 60 && diff < 90){
                    diffCount1.push(diff)
                } else if (diff > 0 && diff < 60){
                    diffCount2.push(diff)
                }         
            },
        drawCallback: function (settings) {
            if (diffCount1.length || diffCount2.length){
                span ? span.innerHTML = diffCount1.length + diffCount2.length : ''
                diffCount1 = []
                diffCount2 = []
            } else {
                span ? span.innerHTML = '' : ''
            }
        },
        // searchDelay: 500,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        columns: [
            {data: "createdAt"},
            {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
            {data: "phone"},
            {data: row => `<span class="${flagIndicator(row.flagSponsor)} tooltip-test" title="${flagSponsorReason(row.flagSponsor)}">${row.sponsor}</span>`},
            {data: "lastVisitDate"},
            {data: "lastDiagnosis"},
            {data: "doctor"},
            {data: row => function () {
                const diff = getMinsDiff(new Date(), new Date(row.rawDateTime))
                   return  `<span class="${diff > 60 && diff < 90 ? 'colour-change2 fw-bold' : diff > 0 && diff < 60 ? 'colour-change3 fw-bold' : ''}">${row.date}</span>`
                }
            },
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                        return `
                        <div class="d-flex flex-">
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteApBtn tooltip-test" title="delete Ap" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                }}
        ]
    })

    return appointmentTable
}

export {getAppointmentsTable}