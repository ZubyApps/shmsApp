import { Offcanvas } from "bootstrap";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors, openModals} from "./helpers"
import http from "./http";
import jQuery from "jquery";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-select-bs5';
import 'datatables.net-staterestore-bs5';


window.addEventListener('DOMContentLoaded', function () {

    const waitingListTable = new DataTable('#waitingListTable', {
        serverSide: true,
        ajax:  '/visits/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "patient"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "came"},
            {data: row => function () {
                    if (row.doctor === ''){
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary consultBtn tooltip-test" title="consult" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientTyp="${ row.patientType }">
                                    <i class="bi bi-clipbord2-plus-fill"></i>
                                </button>
                            </div>`
                        } else {
                            row.doctor
                        }
            }},
            {
                sortable: false,
                data: row => function () {
                    if (row.doctor !== '') {
                        `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary removeBtn tooltip-test" title="update" data-id="${ row.id }">
                            <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>
                    `
                    }
                }
                }
        ]
    });

    document.querySelector('#sponsorCategoryTable').addEventListener('click', function (event) {
        const consultBtn    = event.target.closest('.consultBtn')
        const removeBtn  = event.target.closest('.removeBtn')

        if (consultBtn) {
            consultBtn.setAttribute('disabled', 'disabled')
            const visitId       = consultBtn.getAttribute('data-id')
            const patientId     = consultBtn.getAttribute('data-patientId')
            const patientType   = consultBtn.getAttribute('data-patientType')


            http.post(`/visits/consult/${ visitId }`, {patientId, patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        
                    }
                    consultBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (removeBtn){
            removeBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Category?')) {
                const sponsorCategoryId = removeBtn.getAttribute('data-id')
                http.delete(`/sponsorcategory/${sponsorCategoryId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            waitingListTable.draw()
                        }
                        removeBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
            
        }
    })

    
})
