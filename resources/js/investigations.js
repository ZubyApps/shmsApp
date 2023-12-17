import { Offcanvas, Modal } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, loadingSpinners, clearValidationErrors} from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getAllRegularPatientsVisitTable, getInpatientsVisitTable, getAncPatientsVisitTable } from "./tables/investigationTables";
import { getLabTableByConsultation } from "./tables/doctorstables";

window.addEventListener('DOMContentLoaded', function () {
    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const addResultModal            = new Modal(document.getElementById('addResultModal'))
    const investigationsModal       = new Modal(document.getElementById('investigationsModal'))
    // const investigationsList        = new Offcanvas(document.getElementById('offcanvasInvestigations'))

    const treatmentDiv              = document.querySelector('#treatmentDiv')
    const resultDiv                 = addResultModal._element.querySelector('#resultDiv')

    const saveResultBtn             = addResultModal._element.querySelector('#saveResultBtn')

    const [allRegularPatientsTab, inPatientsTab, ancPatientsTab]  = [document.querySelector('#nav-allRegularPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-ancPatients-tab')]

     // Auto textarea adjustment
     const textareaHeight = 90;
     textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))

    let inPatientsVisitTable, ancPatientsVisitTable

    const allRegularPatientsTable = getAllRegularPatientsVisitTable('allRegularPatientsVisitTable')

    allRegularPatientsTab.addEventListener('click', function() {allRegularPatientsTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getInpatientsVisitTable('inPatientsVisitTable')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getAncPatientsVisitTable('ancPatientsVisitTable')
        }
    })

    // investigationsList._element.addEventListener('hide.bs.offcanvas', function () {
    //     allRegularPatientsTable.draw()
    //     inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
    //     ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    // })

    document.querySelectorAll('#allRegularPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn = event.target.closest('.consultationDetailsBtn')
            const investigationsBtn = event.target.closest('.investigationsBtn')
            const viewer = 'Lab'

            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
    
                const visitId = consultationDetailsBtn.getAttribute('data-id')
                const patientType = consultationDetailsBtn.getAttribute('data-patientType')
                
                http.get(`/consultation/consultations/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            let iteration = 0
                            let count = 0
    
                            const consultations = response.data.consultations.data
                            const patientBio = response.data.bio
    
                            openLabModals(reviewDetailsModal, treatmentDiv, patientBio)
    
                            addResultModal._element.querySelector('#patient').value = patientBio.patientId
                            addResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
    
                            
                            consultations.forEach(line => {
                                iteration++
    
                                iteration > 1 ? count++ : ''
    
                                if (patientType === 'ANC') {
                                    treatmentDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                                } else {
                                    treatmentDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                                }
                            })
    
                            reviewDetailsModal.show()
    
                        }
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
    
            if (investigationsBtn) {
                investigationsBtn.setAttribute('disabled', 'disabled')
                const tableId = investigationsModal._element.querySelector('.investigationsTable').id
                const visitId = investigationsBtn.getAttribute('data-id')
                investigationsModal._element.querySelector('#patient').value = investigationsBtn.getAttribute('data-patient')
                investigationsModal._element.querySelector('#sponsor').value = investigationsBtn.getAttribute('data-sponsor')
    
                getLabTableByConsultation(tableId, investigationsModal._element, viewer, null, visitId)
    
                investigationsModal.show()
                investigationsBtn.removeAttribute('disabled')
            }
    
        })
    })

    reviewDetailsModal._element.addEventListener('hide.bs.modal', function () {
        allRegularPatientsTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        treatmentDiv.innerHTML = ''
    })

    

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseBtn  = event.target.closest('.collapseBtn')
        const addResultBtn = event.target.closest('#addResultBtn')
        const deleteResultBtn = event.target.closest('.deleteResultBtn')
        const viewer = 'lab'

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const investigationTableId = gotoDiv.querySelector('.investigationTable').id
            const conId = gotoDiv.querySelector('.investigationTable').dataset.id

            if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                $('#' + investigationTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, '/' + 'investigations')
                getLabTableByConsultation(investigationTableId, reviewDetailsModal._element, viewer, conId, null)
            }
            setTimeout(goto, 300)
        }

        if (addResultBtn) {
            saveResultBtn.setAttribute('data-id', addResultBtn.getAttribute('data-id'))
            saveResultBtn.setAttribute('data-table', addResultBtn.getAttribute('data-table'))
            addResultModal._element.querySelector('#diagnosis').value = addResultBtn.getAttribute('data-diagnosis')
            addResultModal._element.querySelector('#investigation').value = addResultBtn.getAttribute('data-investigation')
            addResultModal.show()
        }

        if (deleteResultBtn){
            deleteResultBtn.setAttribute('disabled', 'disabled')
            const prescriptionTableId = deleteResultBtn.getAttribute('data-table')
            if (confirm('Are you sure you want to delete this result?')) {
                const prescriptionId = deleteResultBtn.getAttribute('data-id')
                http.patch(`/prescription/remove/${prescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            
                            if ($.fn.DataTable.isDataTable('#' + prescriptionTableId)) {
                                $('#' + prescriptionTableId).dataTable().fnDraw()
                            }
                        }
                        deleteResultBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteResultBtn.removeAttribute('disabled')
                    })
            }
        }
    })

    saveResultBtn.addEventListener('click', function () {
        const prescriptionId = saveResultBtn.getAttribute('data-id')
        const investigationTableId = saveResultBtn.getAttribute('data-table')
        saveResultBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(resultDiv), prescriptionId }

        http.patch(`/prescription/${prescriptionId}`, { ...data }, { "html": resultDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {

                    clearDivValues(resultDiv)
                    clearValidationErrors(resultDiv)

                    if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                        $('#' + investigationTableId).dataTable().fnDraw()
                    }
                }
                saveResultBtn.removeAttribute('disabled')
                addResultModal.hide()
            })
            .catch((error) => {
                console.log(error)
                saveResultBtn.removeAttribute('disabled')
            })
    })
})

function openLabModals(modal, button, { id, visitId, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}