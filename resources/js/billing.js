import { Offcanvas, Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsInvestigations";
import { getWaitingTable, getPatientsVisitsByFilterTable } from "./tables/billingTables";


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas     = new Offcanvas(document.getElementById('waitingListOffcanvas2'))

    const waitingBtn            = document.querySelector('#waitingBtn')

    const outPatientsTab        = document.querySelector('#nav-outPatients-tab')
    const inPatientsTab         = document.querySelector('#nav-inPatients-tab')
    const ancPatientsTab        = document.querySelector('#nav-ancPatients-tab')


    let inPatientsVisitTable, ancPatientsVisitTable

    const outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient')
    const waitingTable = getWaitingTable('waitingTable')

    outPatientsTab.addEventListener('click', function() {outPatientsVisitTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitsByFilterTable('inPatientsVisitTable', 'Inpatient')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('ancPatientsVisitTable', 'ANC')
        }
    })

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })
    
    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const removeBtn  = event.target.closest('.removeBtn')

        if (removeBtn){
            removeBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Visit?')) {
                const visitId = removeBtn.getAttribute('data-id')
                http.delete(`/visits/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        waitingTable.draw()
                    }
                    removeBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                    removeBtn.removeAttribute('disabled')
                })
            }  
        }
    
    })

})