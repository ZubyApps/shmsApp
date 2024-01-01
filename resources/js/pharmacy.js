
import {Modal } from "bootstrap";
import {getOrdinal, textareaHeightAdjustment, loadingSpinners} from "./helpers"
import { getPatientsVisitByFilterTable, getPrescriptionsByConsultation } from "./tables/pharmacyTables";
import http from "./http";
import $ from 'jquery';
import { getLabTableByConsultation, getTreatmentTableByConsultation, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";


window.addEventListener('DOMContentLoaded', function () {
    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const billingDispenseModal      = new Modal(document.getElementById('billingDispenseModal'))

    const treatmentDiv              = document.querySelector('#treatmentDiv')

    const [outPatientsTab, inPatientsTab, ancPatientsTab]  = [document.querySelector('#nav-outPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-ancPatients-tab')]

    const textareaHeight = 90;
    textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))

    let inPatientsVisitTable, ancPatientsVisitTable, visitPrescriptionsTable

    const outPatientsTable = getPatientsVisitByFilterTable('outPatientsTable', 'Outpatient')

    outPatientsTab.addEventListener('click', function() {outPatientsTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsTable' )){
            $('#inPatientsTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitByFilterTable('inPatientsTable', 'Inpatient')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsTable' )){
            $('#ancPatientsTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitByFilterTable('ancPatientsTable', 'ANC')
        }
    })

    document.querySelectorAll('#outPatientsTable, #inPatientsTable, #ancPatientsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
            const billingDispenseBtn         = event.target.closest('.billingDispenseBtn')
    
            const viewer = 'hmo'

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
    
                            openPharmacyModals(reviewDetailsModal, treatmentDiv, patientBio)
    
                            consultations.forEach(line => {
                                iteration++
    
                                iteration > 1 ? count++ : ''
    
                                if (patientType === 'ANC') {
                                    treatmentDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                                } else {
                                    treatmentDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                                }
                            })
    
                            getVitalSignsTableByVisit('#vitalSignsTableNurses', visitId, reviewDetailsModal, viewer)
                            reviewDetailsModal.show()
    
                        }
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        consultationDetailsBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }

            if (billingDispenseBtn) {
                const tableId = '#' + billingDispenseModal._element.querySelector('.visitPrescriptionsTable').id
                const visitId = billingDispenseBtn.getAttribute('data-id')
                billingDispenseModal._element.querySelector('#patient').value = billingDispenseBtn.getAttribute('data-patient')
                billingDispenseModal._element.querySelector('#sponsor').value = billingDispenseBtn.getAttribute('data-sponsor')
                billingDispenseModal._element.querySelector('#markDoneBtn').setAttribute('data-id', visitId)
    
                visitPrescriptionsTable = getPrescriptionsByConsultation(tableId, visitId, billingDispenseModal)
                billingDispenseModal.show()
            }
        })
    })

    document.querySelector('#visitPrescriptionsTable').addEventListener('click', function (event) {
        const billQtySpan       = event.target.closest('.billQtySpan')
        const dispenseQtySpan   = event.target.closest('.dispenseQtySpan')

        if (billQtySpan){
            const prescriptionId    = billQtySpan.getAttribute('data-id')
            const billQtyInput      = billQtySpan.parentElement.querySelector('.billQtyInput')
            billQtySpan.classList.add('d-none')
            billQtyInput.classList.remove('d-none')
            billQtyInput.focus()
            
            billQtyInput.addEventListener('blur', function () {
                
                http.patch(`/pharmacy/bill/${prescriptionId}`, {quantity: billQtyInput.value})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        visitPrescriptionsTable.draw()
                    }
                })
                .catch((error) => {
                    console.log(error)
                    visitPrescriptionsTable.draw()
                }) 
                               
            })
        }

        if (dispenseQtySpan){
            const prescriptionId   = dispenseQtySpan.getAttribute('data-id')
            const qtyBilled        = dispenseQtySpan.getAttribute('data-qtybilled')
            const dispenseQtyInput  = dispenseQtySpan.parentElement.querySelector('.dispenseQtyInput')
            dispenseQtySpan.classList.add('d-none')
            dispenseQtyInput.classList.remove('d-none')
            dispenseQtyInput.focus()
            
            dispenseQtyInput.addEventListener('blur', function () {
                if (dispenseQtyInput.value > qtyBilled){
                    alert('Quantity to be dispensed should not be more than Quantity billed')
                    setTimeout(function(){dispenseQtyInput.focus()}, 1)
                } else {
                    http.patch(`/pharmacy/dispense/${prescriptionId}`, {quantity: dispenseQtyInput.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            visitPrescriptionsTable.draw()
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        visitPrescriptionsTable.draw()
                    })
                }
                 
                               
            })
        }
    })

    billingDispenseModal._element.addEventListener('hide.bs.modal', function () {
        treatmentDiv.innerHTML = ''
        outPatientsTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseBtn = event.target.closest('.collapseBtn')
        const approvalBtn = event.target.closest('#approvalBtn')
        const viewer = 'hmo'

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const investigationTableId = gotoDiv.querySelector('.investigationTable').id
            console.log(investigationTableId)
            const treatmentTableId = gotoDiv.querySelector('.treatmentTable').id
            console.log(treatmentTableId)
            const conId = gotoDiv.querySelector('.investigationTable').dataset.id

            if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                $('#' + investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                $('#' + treatmentTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "pharmacy")
                getLabTableByConsultation(investigationTableId, reviewDetailsModal._element, viewer, conId, null)
                getTreatmentTableByConsultation(treatmentTableId, conId, reviewDetailsModal._element)
            }
            setTimeout(goto, 300)
        }
    })

    reviewDetailsModal._element.addEventListener('hide.bs.modal',function () {
        outPatientsTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })
})

function openPharmacyModals(modal, button, { id, visitId, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}