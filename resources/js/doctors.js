import { Modal, Collapse, Toast, Offcanvas } from "bootstrap"
import * as ECT from "@whoicd/icd11ect"
import "@whoicd/icd11ect/style.css"
import { clearDivValues, getOrdinal, getDivData, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, clearValidationErrors, doctorsModalClosingTasks, bmiCalculator, lmpCalculator, filterPatients, openModals, populateConsultationModal, populateDischargeModal, populatePatientSponsor, populateVitalsignsModal, lmpCurrentCalculator, displayConsultations, displayVisits, closeReviewButtons, openMedicalReportModal, displayMedicalReportModal} from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import http from "./http";
import { getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getMedicationsByFilter, getInpatientsVisitTable, getOutpatientsVisitTable, getAncPatientsVisitTable, getSurgeryNoteTable, getOtherPrescriptionsByFilter, getMedicalReportTable} from "./tables/doctorstables"
import { getAncVitalsignsChart, getVitalsignsChartByVisit } from "./charts/vitalsignsCharts"
import $ from 'jquery';
import { getbillingTableByVisit } from "./tables/billingTables"
import { getAncVitalSignsTable, getDeliveryNoteTable, getEmergencyTable } from "./tables/nursesTables"
import { visitDetails } from "./dynamicHTMLfiles/visits"
import html2pdf  from "html2pdf.js"

window.addEventListener('DOMContentLoaded', function () {
    const waitingListOffcanvas              = new Offcanvas(document.getElementById('waitingListOffcanvas1'))
    const newConsultationModal              = new Modal(document.getElementById('newConsultationModal'))
    const ancConsultationModal              = new Modal(document.getElementById('ancConsultationModal'))
    const ancReviewModal                    = new Modal(document.getElementById('ancReviewModal'))
    const consultationReviewModal           = new Modal(document.getElementById('consultationReviewModal'))
    const consultationHistoryModal          = new Modal(document.getElementById('consultationHistoryModal'))
    const ancConsultationReviewModal        = new Modal(document.getElementById('ancConsultationReviewModal'))
    const newSurgeryModal                   = new Modal(document.getElementById('newSurgeryModal'))
    const updateSurgeryModal                = new Modal(document.getElementById('updateSurgeryModal'))
    const viewSurgeryModal                  = new Modal(document.getElementById('viewSurgeryModal'))
    const fileModal                         = new Modal(document.getElementById('fileModal'))
    const newReviewModal                    = new Modal(document.getElementById('newReviewModal'))
    const specialistConsultationModal       = new Modal(document.getElementById('specialistConsultationModal'))
    const vitalsignsModal                   = new Modal(document.getElementById('vitalsignsModal'))
    const ancVitalsignsModal                = new Modal(document.getElementById('ancVitalsignsModal'))
    const addResultModal                    = new Modal(document.getElementById('addResultModal'))
    const updateResultModal                 = new Modal(document.getElementById('updateResultModal'))
    const investigationsModal               = new Modal(document.getElementById('investigationsModal'))
    const investigationAndManagementModal   = new Modal(document.getElementById('investigationAndManagementModal'))
    const dischargeModal                    = new Modal(document.getElementById('dischargeModal'))
    const medicalReportListModal            = new Modal(document.getElementById('medicalReportListModal'))
    const newMedicalReportTemplateModal     = new Modal(document.getElementById('newMedicalReportTemplateModal'))
    const editMedicalReportTemplateModal    = new Modal(document.getElementById('editMedicalReportTemplateModal'))
    const viewMedicalReportModal            = new Modal(document.getElementById('viewMedicalReportModal'))
    
    const regularConsultationReviewDiv      = consultationReviewModal._element.querySelector('#consultationReviewDiv')
    const ancConsultationReviewDiv          = ancConsultationReviewModal._element.querySelector('#consultationReviewDiv')
    const visitHistoryDiv                   = consultationHistoryModal._element.querySelector('#visitHistoryDiv')
    const knownClinicalInfoDiv              = document.querySelectorAll('#knownClinicalInfoDiv')
    const consultationDiv                   = document.querySelectorAll('#consultationDiv')
    const addResultDiv                      = addResultModal._element.querySelector('#resultDiv')
    const updateResultDiv                   = updateResultModal._element.querySelector('#resultDiv')
    const dischargeDetailsDiv               = dischargeModal._element.querySelector('#dischargeDetails')
    const newMedicalReportDetailsDiv        = newMedicalReportTemplateModal._element.querySelector('#medicalReportDetailsDiv')
    const editMedicalReportDetailsDiv       = editMedicalReportTemplateModal._element.querySelector('#medicalReportDetailsDiv')
    
    const reviewPatientbtn                  = consultationReviewModal._element.querySelector('#reviewPatientBtn')
    const reviewAncPatientbtn               = ancConsultationReviewModal._element.querySelector('#reviewAncPatientBtn')
    const [dischargeBtn, saveDischargeBtn]  = [document.querySelectorAll('#dischargeBtn'), document.querySelector('#saveDischargeBtn')]
    const specialistConsultationbtn         = consultationReviewModal._element.querySelector('#specialistConsultationBtn')
    const createResultBtn                   = addResultModal._element.querySelector('#createResultBtn')
    const saveResultBtn                     = updateResultModal._element.querySelector('#saveResultBtn')
    const addInvestigationAndManagmentBtn   = document.querySelectorAll('#addInvestigationAndManagementBtn')
    const updateKnownClinicalInfoBtn        = document.querySelectorAll('#updateKnownClinicalInfoBtn')
    const addVitalsignsBtn                  = document.querySelectorAll('#addVitalsignsBtn')
    const saveConsultationBtn               = document.querySelectorAll('#saveConsultationBtn')
    const waitingBtn                        = document.querySelector('#waitingBtn')
    const clearDiagnosisBtns                = document.querySelectorAll('.clearDiagnosis')
    const createSurgeryNoteBtn              = newSurgeryModal._element.querySelector('#createSurgeryNoteBtn')
    const saveSurgeryNoteBtn                = updateSurgeryModal._element.querySelector('#saveSurgeryNoteBtn')
    const moreHistoryBtn                    = consultationHistoryModal._element.querySelector('#moreHistoryBtn')
    const newMedicalReportBtn               = medicalReportListModal._element.querySelector('#newMedicalReportBtn')
    const createMedicalReportBtn            = newMedicalReportTemplateModal._element.querySelector('#createMedicalReportBtn')
    const saveMedicalReportBtn              = editMedicalReportTemplateModal._element.querySelector('#saveMedicalReportBtn')
    const emboldenBtn                       = newMedicalReportTemplateModal._element.querySelector('.emboldenBtn')
    const italicsBtn                        = newMedicalReportTemplateModal._element.querySelector('.italicsBtn')
    const underlineBtn                      = newMedicalReportTemplateModal._element.querySelector('.underlineBtn')
    const downloadReportBtn                 = viewMedicalReportModal._element.querySelector('#downloadReportBtn')
    const reportModalBody                   = viewMedicalReportModal._element.querySelector('.reportModalBody')
    const patientsFullName                  = viewMedicalReportModal._element.querySelector('#patientsFullName')
    const patientsInfo                      = viewMedicalReportModal._element.querySelector('#patientsInfo')
    const [outPatientsTab, ancPatientsTab, inPatientsTab, emergencyTab]  = [document.querySelector('#nav-outPatients-tab'), document.querySelector('#nav-ancPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-emergency-tab')]
    const [resourceInput]                   = [document.querySelectorAll('#resource')]

    bmiCalculator(document.querySelectorAll('#height, .weight'))
    lmpCalculator(document.querySelectorAll('#lmp'), consultationDiv)

    clearDiagnosisBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            btn.parentElement.parentElement.querySelector('#selectedDiagnosis').value = ''
        })
    })
    // Auto textarea adjustment
    const textareaHeight = 65;
    textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))
    // ICD11settings
    const mySettings = { apiServerUrl: "https://icd11restapi-developer-test.azurewebsites.net", popupMode: false}
    // ICD11 callbacks
    const myCallbacks = {
        selectedEntityFunction: (selectedEntity) => {
            document.querySelector('.selectedDiagnosis-' + selectedEntity.iNo).value += selectedEntity.code + '-' + selectedEntity.selectedText + '\r\n\n'
            document.querySelector('.selectedDiagnosis-' + selectedEntity.iNo).dispatchEvent(new Event('input', { bubbles: true }))
            ECT.Handler.clear(selectedEntity.iNo)
        }
    }

    // ICD11 handler
    ECT.Handler.configure(mySettings, myCallbacks)
    //visit Tables and consultations that are active
    let inPatientsVisitTable, ancPatientsVisitTable, prescriptionTable, medicalReportTable, emergencyTable

    let outPatientsVisitTable = getOutpatientsVisitTable('#outPatientsVisitTable', 'My Patients')
    const waitingTable = getWaitingTable('#waitingTable')

    outPatientsTab.addEventListener('click', function() {outPatientsVisitTable.draw()})

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getAncPatientsVisitTable('#ancPatientsVisitTable', 'My Patients')
        }
    })

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getInpatientsVisitTable('#inPatientsVisitTable', 'My Patients')
        }
    })

    emergencyTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#emergencyTable' )){
            $('#emergencyTable').dataTable().fnDraw()
        } else {
            emergencyTable = getEmergencyTable('emergencyTable', 'doctor')
        }
    })
    
    document.querySelectorAll('#filterListOutPatients, #filterListInPatients, #filterListAncPatients').forEach(filterInput => {
            filterInput.addEventListener('change', function () {
                if (filterInput.id == 'filterListOutPatients'){
                    $.fn.DataTable.isDataTable( '#outPatientsVisitTable' ) ? $('#outPatientsVisitTable').dataTable().fnDestroy() : ''
                    outPatientsVisitTable = getOutpatientsVisitTable('#outPatientsVisitTable', filterInput.value)
                }
                if (filterInput.id == 'filterListInPatients'){
                    $.fn.DataTable.isDataTable( '#inPatientsVisitTable' ) ? $('#inPatientsVisitTable').dataTable().fnDestroy() : ''
                    inPatientsVisitTable = getInpatientsVisitTable('#inPatientsVisitTable', filterInput.value)
                }
                if (filterInput.id == 'filterListAncPatients'){
                    $.fn.DataTable.isDataTable( '#ancPatientsVisitTable' ) ? $('#ancPatientsVisitTable').dataTable().fnDestroy() : ''
                    ancPatientsVisitTable = getAncPatientsVisitTable('#ancPatientsVisitTable', filterInput.value)
                }
            })
    })
    
    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationReviewBtn = event.target.closest('.consultationReviewBtn')
            const vitalsignsBtn         = event.target.closest('.vitalSignsBtn, .ancVitalSignsBtn')
            const investigationsBtn     = event.target.closest('.investigationsBtn')
            const dischargedBtn         = event.target.closest('.dischargedBtn')
            const historyBtn            = event.target.closest('.historyBtn')
            const toggleVisitBtn        = event.target.closest('#closeVisitBtn, #openVisitBtn')
            const medicalReportBtn      = event.target.closest('.medicalReportBtn')
            const updateResourceListBtn = event.target.closest('#updateResourceListBtn')
            const viewer                = 'doctor'
            let [iteration, count]        = [0, 0]
    
            if (consultationReviewBtn) {
                consultationReviewBtn.setAttribute('disabled', 'disabled')
                const [visitId, patientType, ancRegId, isDoctorDone, closed] = [consultationReviewBtn.dataset.id, consultationReviewBtn.dataset.patienttype, consultationReviewBtn.dataset.ancregid, consultationReviewBtn.dataset.doctordone, +consultationReviewBtn.dataset.closed] 
                
                const isAnc = patientType === 'ANC'
                resourceInput.forEach(input => {input.setAttribute('data-sponsorcat', consultationReviewBtn.getAttribute('data-sponsorcat'))})
                populateConsultationModal(newReviewModal, reviewPatientbtn, visitId, ancRegId, patientType)
                populateConsultationModal(specialistConsultationModal, specialistConsultationbtn, visitId, ancRegId, patientType)
                populateConsultationModal(ancReviewModal, reviewAncPatientbtn, visitId, ancRegId, patientType)
                
                populateDischargeModal(dischargeModal, consultationReviewBtn, visitId)
                
                populatePatientSponsor(addResultModal, consultationReviewBtn)
                populatePatientSponsor(updateResultModal, consultationReviewBtn)
                populatePatientSponsor(investigationAndManagementModal, consultationReviewBtn)
                
                const [modal, div, displayFunction, vitalSignsTable, vitalSignsChart, id, url, suffixId] = isAnc ? [ancConsultationReviewModal, ancConsultationReviewDiv, AncPatientReviewDetails, getAncVitalSignsTable, getAncVitalsignsChart, ancRegId, 'ancvitalsigns', 'AncConReview'] : [consultationReviewModal, regularConsultationReviewDiv, regularReviewDetails, getVitalSignsTableByVisit, getVitalsignsChartByVisit, visitId, 'vitalsigns', 'ConReview']
                
                closeReviewButtons(modal, closed)
                http.get(`/consultation/consultations/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        const consultations = response.data.consultations.data
                        const patientBio    = response.data.bio
                        const lmp           = response.data.latestLmp

                        openDoctorModals(modal, div, patientBio)
                        isAnc ? lmpCurrentCalculator(lmp.lmp, modal._element.querySelector('.lmpDetailsDiv')) : ''
                        consultations.forEach(line => {
                            iteration++
                            iteration > 1 ? count++ : ''
                            displayConsultations(div, displayFunction, iteration, getOrdinal, count, consultations.length, line, viewer, isDoctorDone, closed)
                        })
    
                        vitalSignsTable(`#vitalSignsConsultation${suffixId}`, id, modal)
                        http.get(`/${url}/load/chart`,{params: {  visitId: id, ancRegId: id }})
                        .then((response) => {
                            vitalSignsChart(modal._element.querySelector(`#vitalsignsChart${suffixId}`), response, modal)
                        })
                        .catch((error) => {
                            console.log(error)
                        })
                        getbillingTableByVisit(`billingTable${suffixId}`, visitId, modal._element)
                        
                        modal.show()
                    }
                    consultationReviewBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultationReviewBtn.removeAttribute('disabled')
                    console.log(error)
                })
            }

            if (updateResourceListBtn){
                updateResourceListBtn.setAttribute('disabled', 'disabled')
                resourceInput.forEach(input => {input.setAttribute('data-sponsorcat', updateResourceListBtn.getAttribute('data-sponsorcat'))})
                investigationAndManagementModal._element.querySelector('.investigationAndManagementDiv').classList.remove('d-none')
                const btn = investigationAndManagementModal._element.querySelector('#addInvestigationAndManagementBtn')
                const [conId, visitId] = [updateResourceListBtn.dataset?.conid , updateResourceListBtn.dataset.id]
                populatePatientSponsor(investigationAndManagementModal, updateResourceListBtn)
                btn.setAttribute('data-conid', conId)
                btn.setAttribute('data-visitid', visitId)
                getPrescriptionTableByConsultation('prescriptionTableConReview', conId, null, investigationAndManagementModal._element)
                investigationAndManagementModal.show()
                setTimeout(()=> {updateResourceListBtn.removeAttribute('disabled')}, 1000)
            }

            if (vitalsignsBtn) {
                const isAnc = vitalsignsBtn.id == 'ancVitalSignsBtn'
                const [btn, modal, url, id, getTable, getChart] = isAnc ? [vitalsignsBtn, ancVitalsignsModal, 'ancvitalsigns', vitalsignsBtn.getAttribute('data-ancregid'),   getAncVitalSignsTable, getAncVitalsignsChart] : [vitalsignsBtn, vitalsignsModal, 'vitalsigns', vitalsignsBtn.getAttribute('data-id'), getVitalSignsTableByVisit, getVitalsignsChartByVisit]

                btn.setAttribute('disabled', 'disabled')
                const tableId = '#' + modal._element.querySelector('.vitalsTable').id
                populateVitalsignsModal(modal, btn, id)
                
                getTable(tableId, id, modal)
                http.get(`/${url}/load/chart`,{params: {  visitId: id, ancRegId : id }})
                .then((response) => {
                    getChart(modal._element.querySelector('#vitalsignsChart'), response, modal)
                })
                .catch((error) => {
                    console.log(error)
                })
                
                modal.show()
                setTimeout(()=>{btn.removeAttribute('disabled')}, 2000)
            }

            if (investigationsBtn) {
                investigationsBtn.setAttribute('disabled', 'disabled')
                const tableId = investigationsModal._element.querySelector('.investigationsTable').id
                const visitId = investigationsBtn.getAttribute('data-id')
                populatePatientSponsor(investigationsModal, investigationsBtn)
                getLabTableByConsultation(tableId, investigationsModal._element, viewer, null, visitId)
    
                investigationsModal.show()
                investigationsBtn.removeAttribute('disabled')
            }

            if (dischargedBtn){
                dischargedBtn.setAttribute('disabled', 'disabled')
                populateDischargeModal(dischargeModal, dischargedBtn)
                dischargeModal.show()
            }

            if (historyBtn){
                const patientId     = historyBtn.getAttribute('data-patientid')
                const isAnc         = historyBtn.getAttribute('data-patienttype') === 'ANC'
                populatePatientSponsor(investigationAndManagementModal, historyBtn)
                http.get(`/consultation/history/${patientId}`)
                .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    const visits        = response.data.visits.data
                    const patientBio    = response.data.bio
                    openDoctorModals(consultationHistoryModal, moreHistoryBtn, patientBio)
                    visits.forEach(line => {
                        iteration++
                        iteration > 1 ? count++ : ''
                        displayVisits(visitHistoryDiv, visitDetails, iteration, getOrdinal, line, viewer, isAnc)
                    })
                    consultationHistoryModal.show()
                }
                })
            }

            if (toggleVisitBtn){                
                const [visitId, string]  = [toggleVisitBtn.getAttribute('data-id'), toggleVisitBtn.id == 'closeVisitBtn' ? 'close' : 'open']
                if (confirm(`Are you sure you want to ${string} the Visit?`)) {
                    http.patch(`/visits/${string}/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            outPatientsVisitTable.draw()
                            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
                            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
                        }
                    })
                    .catch((error) => {
                        if (error.response.status === 403){
                            alert(error.response.data.message); 
                        }
                    })
                }
            }

            if (medicalReportBtn){
                const visitId = medicalReportBtn.getAttribute('data-id')
                createMedicalReportBtn.setAttribute('data-patientid', medicalReportBtn.getAttribute('data-patientid'))
                createMedicalReportBtn.setAttribute('data-visitid', medicalReportBtn.getAttribute('data-id'))
                medicalReportListModal._element.querySelector('#patient').value = medicalReportBtn.getAttribute('data-patient')
                medicalReportListModal._element.querySelector('#sponsorName').value = medicalReportBtn.getAttribute('data-sponsor')
                medicalReportListModal._element.querySelector('#age').value = medicalReportBtn.getAttribute('data-age')
                medicalReportListModal._element.querySelector('#sex').value = medicalReportBtn.getAttribute('data-sex')
                newMedicalReportTemplateModal._element.querySelector('#patient').value = medicalReportBtn.getAttribute('data-patient')
                newMedicalReportTemplateModal._element.querySelector('#sponsorName').value = medicalReportBtn.getAttribute('data-sponsor')
                newMedicalReportTemplateModal._element.querySelector('#age').value = medicalReportBtn.getAttribute('data-age')
                newMedicalReportTemplateModal._element.querySelector('#sex').value = medicalReportBtn.getAttribute('data-sex')
                editMedicalReportTemplateModal._element.querySelector('#sponsorName').value = medicalReportBtn.getAttribute('data-sponsor')
                editMedicalReportTemplateModal._element.querySelector('#patient').value = medicalReportBtn.getAttribute('data-patient')
                editMedicalReportTemplateModal._element.querySelector('#age').value = medicalReportBtn.getAttribute('data-age')
                editMedicalReportTemplateModal._element.querySelector('#sex').value = medicalReportBtn.getAttribute('data-sex')
                medicalReportTable = getMedicalReportTable('medicalReportTable', visitId, medicalReportListModal._element, true)
                medicalReportListModal.show()
            }
        })
    })

    emboldenBtn.addEventListener('click', function () {
        window.getSelection() ? document.execCommand("bold") : ''
    })

    italicsBtn.addEventListener('click', function () {
        window.getSelection() ? document.execCommand("italic") : ''
    })

    underlineBtn.addEventListener('click', function () {
        window.getSelection() ? document.execCommand("underline") : ''
    })

    newMedicalReportBtn.addEventListener('click', function() {
        newMedicalReportTemplateModal.show()
    })

    createMedicalReportBtn.addEventListener('click', function() {
        createMedicalReportBtn.setAttribute('disabled', 'disabled')
        const [visitId, patientId] = [createMedicalReportBtn.getAttribute('data-visitid'), createMedicalReportBtn.getAttribute('data-patientid')]
        let data = { ...getDivData(newMedicalReportDetailsDiv), recipientsAddress: newMedicalReportDetailsDiv.querySelector('#recipientsAddress').innerHTML, report: newMedicalReportDetailsDiv.querySelector('#report').innerHTML, visitId, patientId }
        http.post(`medicalreports`, {...data}, {'html': newMedicalReportDetailsDiv})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                newMedicalReportTemplateModal.hide()
                medicalReportTable ? medicalReportTable.draw() : ''
            }
            createMedicalReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            createMedicalReportBtn.removeAttribute('disabled')
        })
    })

    saveMedicalReportBtn.addEventListener('click', function() {
        saveMedicalReportBtn.setAttribute('disabled', 'disabled')
        const id = saveMedicalReportBtn.getAttribute('data-id')
        let data = { ...getDivData(editMedicalReportDetailsDiv), recipientsAddress: editMedicalReportDetailsDiv.querySelector('#recipientsAddress').innerHTML, report: editMedicalReportDetailsDiv.querySelector('#report').innerHTML }
        http.patch(`medicalreports/${id}`, {...data}, {'html': editMedicalReportDetailsDiv})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                editMedicalReportTemplateModal.hide()
                medicalReportTable ? medicalReportTable.draw() : ''
            }
            saveMedicalReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveMedicalReportBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#medicalReportTable').addEventListener('click', function (event) {
        const medicalReportBtn    = event.target.closest('.editMedicalReportBtn')
        const viewMedicalReportbtn = event.target.closest('.viewMedicalReportBtn')

        if (medicalReportBtn) {
            medicalReportBtn.setAttribute('disabled', 'disabled')
            saveMedicalReportBtn.setAttribute('data-table', medicalReportBtn.dataset.table)
            http.get(`/medicalreports/${medicalReportBtn.getAttribute('data-id')}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    openMedicalReportModal(editMedicalReportTemplateModal, saveMedicalReportBtn, response.data.data)
                }
            })
            .catch((error) => {
                console.log(error)
            })
            setTimeout(()=>{medicalReportBtn.removeAttribute('disabled')}, 2000)
        }

        if (viewMedicalReportbtn) {
            viewMedicalReportbtn.setAttribute('disabled', 'disabled')
            http.get(`/medicalreports/display/${viewMedicalReportbtn.getAttribute('data-id')}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    displayMedicalReportModal(viewMedicalReportModal, response.data.data)
                }
            })
            .catch((error) => {
                console.log(error)
            })
            setTimeout(()=>{viewMedicalReportbtn.removeAttribute('disabled')}, 2000)
        }
    })

    dischargeBtn.forEach(btn => {
        btn.addEventListener('click', function () {
            this.setAttribute('disabled', 'disabled')
            dischargeModal.show()
            this.removeAttribute('disabled')
        })
    })
    
    saveDischargeBtn.addEventListener('click', function () {
        const id = this.getAttribute('data-id')
        saveDischargeBtn.setAttribute('disabled', 'disabled')

        http.patch(`/visits/discharge/${id}`, getDivData(dischargeDetailsDiv), {html:dischargeDetailsDiv})
        .then((response) => {
            if (response) {
                clearDivValues(dischargeDetailsDiv)
                clearValidationErrors(dischargeDetailsDiv)
                dischargeModal.hide()
            }
            saveDischargeBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveDischargeBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const consultBtn    = event.target.closest('.consultBtn')
        const removeBtn     = event.target.closest('.closeVisitBtn, .deleteVisitBtn')
        const emergencyBtn  = event.target.closest('.emergencyBtn')

        if (consultBtn) {
            consultBtn.setAttribute('disabled', 'disabled')
            const [visitId, patientType, ancRegId] = [consultBtn.getAttribute('data-id'),consultBtn.getAttribute('data-patientType'),consultBtn.getAttribute('data-ancregid')]
            resourceInput.forEach(input => {input.setAttribute('data-sponsorcat', consultBtn.getAttribute('data-sponsorcat'))})

            http.post(`/doctors/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        if (patientType === 'ANC'){
                            openDoctorModals(ancConsultationModal, ancConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                            getAncVitalSignsTable('#vitalSignsTableAnc', ancRegId, ancConsultationModal)
                        } else{
                            openDoctorModals(newConsultationModal, newConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                            getVitalSignsTableByVisit('#vitalSignsTableNew', visitId, newConsultationModal)
                        }
                        waitingListOffcanvas.hide()
                    }
                    consultBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultBtn.removeAttribute('disabled')
                    console.log(error)
                })
        }

        if (emergencyBtn){
            waitingListOffcanvas.hide()
            emergencyTab.click()
        }

        if (removeBtn){                
            const [visitId, string]  = [removeBtn.getAttribute('data-id'), removeBtn.id == 'closeVisitBtn' ? 'close' : 'delete']
            if (confirm(`Are you sure you want to ${string} the Visit?`)) {
                http.patch(`/visits/${string}/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        waitingTable.draw()
                    }
                })
                .catch((error) => {
                    if (error.response.status === 403){
                        alert(error.response.data.message); 
                    }
                })
            }
        }
    })

    // Show waiting table
    waitingBtn.addEventListener('click', function () {waitingTable.draw()})

    waitingListOffcanvas._element.addEventListener('hide.bs.offcanvas', () => {
        outPatientsVisitTable.draw()
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
    })

    document.querySelectorAll('#dischargeModal, #vitalsignsModal, #ancVitalsignsModal, #investigationAndManagementModal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', () => {
            outPatientsVisitTable ? outPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        })
    })

    // manipulating all known clinical info div
    updateKnownClinicalInfoBtn.forEach(updateBtn => {
        updateBtn.addEventListener('click', function () {
            knownClinicalInfoDiv.forEach(div => {
                if (div.dataset.div === updateBtn.dataset.btn) {
                    console.log(div.dataset.div, updateBtn.dataset.btn, div)
                    toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled', '')        
                    updateBtn.textContent === "Done" ? updateBtn.innerHTML = `Update` : updateBtn.textContent = "Done"
                    if (updateBtn.textContent === 'Update'){
                        const patient = updateBtn.dataset.id
                        http.patch(`/patients/knownclinicalinfo/${patient}`, {...getDivData(div)}, {"html": div})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                new Toast(div.querySelector('#knownClinicalInfoToast'), {delay:2000}).show()
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                        })
                    }
                }
            })
        })
    })

     // manipulating all vital signs div
    addVitalsignsBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            addBtn.setAttribute('disabled', 'disabled')
            const div       = addBtn.parentElement.parentElement.querySelector('#addVitalsignsDiv')
            const isAnc     = addBtn.getAttribute('data-patienttype') == 'ANC'
            const [visitId, tableId, ancRegId] = [addBtn.dataset.id, div.parentNode.parentNode.querySelector('.vitalsTable').id, addBtn.getAttribute('data-ancregid')]
            let data = {...getDivData(div), visitId, ancRegId}
            const url = isAnc ? '/ancvitalsigns' : '/vitalsigns'
            
            isAnc && JSON.parse(ancRegId) == null ? alert('Patient not registered for ANC') :
            http.post(url, {...data}, {"html": div})
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    new Toast(div.querySelector('#vitalSignsToast'), {delay:2000}).show()
                    clearDivValues(div)
                }
                if ($.fn.DataTable.isDataTable( '#'+tableId )){
                    $('#'+tableId).dataTable().fnDraw()
                }
                addBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                console.log(error)
                addBtn.removeAttribute('disabled')
            })                   
            addBtn.removeAttribute('disabled')
        })
    })

    document.querySelectorAll('#vitalSignsTable, #ancVitalSignsTable, #vitalSignsTableNew, #vitalSignsTableSpecialist, #vitalSignsTableAnc, #vitalSignsTableAncReview, #vitalSignsConsultationConReview, #vitalSignsConsultationAncConReview').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn  = event.target.closest('.deleteBtn')
            if (deleteBtn){
                const url  = deleteBtn.dataset.patienttype == 'ANC' ? 'ancvitalsigns' : 'vitalsigns'
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/${url}/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            if ($.fn.DataTable.isDataTable( '#'+table.id )){
                            $('#'+table.id).dataTable().fnDraw()
                            }
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                        deleteBtn.removeAttribute('disabled')
                    })
                }  
            }
        })
    })

    document.querySelectorAll('#prescriptionTableNew, #prescriptionTableSpecialist, #prescriptionTableAnc, #prescriptionTableAncReview, #prescriptionTableConReview, #emergencyTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn  = event.target.closest('.deleteBtn')
            const confirmBtn  = event.target.closest('.confirmBtn')
            if (deleteBtn){
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this prescription?')) {
                    const [id, conId] = [deleteBtn.getAttribute('data-id'), deleteBtn.getAttribute('data-conid')]
                    http.delete(`/prescription/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                if ($.fn.DataTable.isDataTable('#'+table.id)){
                                $('#'+table.id).dataTable().fnDraw()
                                }
                                if ($.fn.DataTable.isDataTable('#investigationTable'+conId)){
                                $('#investigationTable'+conId).dataTable().fnDraw()
                                }
                                if ($.fn.DataTable.isDataTable('#treatmentTable'+conId)){
                                $('#treatmentTable'+conId).dataTable().fnDraw()
                                }
                            }
                            deleteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteBtn.removeAttribute('disabled')
                        })
                }      
            }

            if (confirmBtn){
                confirmBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to confirm this prescription?')) {
                    const id = confirmBtn.getAttribute('data-id')
                    http.patch(`/prescription/confirm/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                if ($.fn.DataTable.isDataTable('#'+table.id)){
                                $('#'+table.id).dataTable().fnDraw()
                                }
                            }
                            confirmBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            if (error.response.status === 403){
                                alert(error.response.data) 
                            }
                            console.log(error)
                            confirmBtn.removeAttribute('disabled')
                        })
                }
            }
        })
    })

    // getting data from all consultation divs
    saveConsultationBtn.forEach(saveBtn => {
        saveBtn.addEventListener('click', function () {
            const div = saveBtn.parentElement.parentElement
            saveBtn.setAttribute('disabled', 'disabled')
            const [investigationAndManagementDiv, investigationAndManagementBtn] = [div.parentElement.querySelector('.investigationAndManagementDiv'), div.parentElement.querySelector('#addInvestigationAndManagementBtn')]
            const modal = div.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement
            const [visitId, tableId] = [saveBtn.getAttribute('data-id'), investigationAndManagementDiv.querySelector('.prescriptionTable').id]
            const urlSuffix = modal.id == 'newReviewModal'  ? '/review' : ''
            let data = {...getDivData(div), visitId}
            http.post(`/consultation${urlSuffix}`, {...data}, {"html": div})
            .then((response) => {
                if (response.status >= 200 || response.status <= 300){
                    toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled')
                    clearValidationErrors(div)
                    saveBtn.textContent === 'Saved' ? saveBtn.textContent = `Save` : saveBtn.textContent = 'Saved'
                    investigationAndManagementDiv.classList.remove('d-none')
                    location.href = '#'+investigationAndManagementDiv.id
                    
                    investigationAndManagementBtn.setAttribute('data-conId', response.data.id)
                    investigationAndManagementBtn.setAttribute('data-visitId', visitId)
                    window.history.replaceState({}, document.title, "/" + "doctors" )

                    new Toast(div.querySelector('#saveConsultationToast'), {delay:2000}).show()
                    getPrescriptionTableByConsultation(tableId, response.data.id, null, modal)
                    waitingTable.draw()
                    outPatientsVisitTable.draw()
                    ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
                    inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
                }
            })
            .catch((error) => {
                console.log(error)
                saveBtn.removeAttribute('disabled')
            })
        })
    })

    // All consultation resource inputs
    resourceInput.forEach(input => {
        input.addEventListener('input', function () {
            const div = input.parentElement.parentElement.parentElement.parentElement.parentElement
            const datalistEl = div.querySelector(`#resourceList${div.dataset.div}`)
            if (input.value < 2) {
            datalistEl.innerHTML = ''
            }
            if (input.value.length > 2) {
                http.get(`/resources/list`, {params: {resource: input.value, sponsorCat: input.dataset.sponsorcat}}).then((response) => {
                    displayResourceList(datalistEl, response.data)
                })
            }
            const selectedOption = datalistEl.options.namedItem(input.value)
            if (selectedOption){
                input.setAttribute('data-resourcevalues', [selectedOption.getAttribute('data-id'), selectedOption.getAttribute('data-cat')])
                if (selectedOption.getAttribute('data-cat') == 'Medications'){
                    div.querySelector('.qty').classList.add('d-none')
                    div.querySelector('#quantity').value = ''
                    div.querySelector('.pres').classList.remove('d-none')
                    div.querySelector('.chartableDiv').classList.remove('d-none')
                } else {
                    div.querySelector('.qty').classList.remove('d-none')
                    div.querySelector('.chartableDiv').classList.remove('d-none')
                    div.querySelector('#quantity').value = 1
                    div.querySelector('.pres').classList.add('d-none')
                }
                input.value = selectedOption.getAttribute('data-plainname')
            }
        })        
    })

    //adding investigation and management on all divs
    addInvestigationAndManagmentBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            const div = addBtn.parentElement.parentElement.parentElement
            addBtn.setAttribute('disabled', 'disabled')
            const [resource, resourceCategory] = div.querySelector('.resource').getAttribute('data-resourcevalues').split(',')//getSelectedResourceValues(div, div.querySelector('.resource'), div.querySelector(`#resourceList${div.dataset.div}`))
            const [conId, visitId, divPrescriptionTableId, chartable] = [addBtn.dataset.conid, addBtn.dataset.visitid, '#'+div.querySelector('.prescriptionTable').id, div.querySelector('#chartable').checked]
            let data = {...getDivData(div), resource, resourceCategory, conId, visitId, chartable}
            
            http.post(`prescription/${resource}`, {...data}, {"html": div})
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    new Toast(div.querySelector('#saveInvestigationAndManagementToast'), {delay:2000}).show()
                    clearDivValues(div)
                    clearValidationErrors(div)
                }
                if ($.fn.DataTable.isDataTable( divPrescriptionTableId )){
                    $(divPrescriptionTableId).dataTable().fnDraw()
                }
                if ($.fn.DataTable.isDataTable( addBtn.dataset?.investigationtable )){
                    $(addBtn.dataset?.investigationtable).dataTable().fnDraw()
                }
                if ($.fn.DataTable.isDataTable( addBtn.dataset?.treatmenttable )){
                    $(addBtn.dataset?.treatmenttable).dataTable().fnDraw()
                }
                if ($.fn.DataTable.isDataTable( addBtn.dataset?.otherprescriptionstable )){
                    $(addBtn.dataset?.otherprescriptionstable).dataTable().fnDraw()
                }
                if ($.fn.DataTable.isDataTable( '#billingTableConReview' )){
                    $('#billingTableConReview').dataTable().fnDraw()
                }
                div.querySelector('#quantity').value = 1
                addBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                console.log(error)
                addBtn.removeAttribute('disabled')
            }) 
        })
    })

    // REVIEW CONSULTATION MODAL CODE
    //open, review,specialist review and anc review modals
    document.querySelectorAll('#reviewPatientBtn, #reviewAncPatientBtn, #specialistConsultationBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            btn.setAttribute('disabled', 'disabled')
            const [visitId, patientType, ancRegId] = [btn.getAttribute('data-id'), btn.getAttribute('data-patientType'), btn.getAttribute('data-ancregid')]
            const isAnc = btn.id == 'reviewAncPatientBtn'
            const modal = btn.id == 'reviewPatientBtn' ?  newReviewModal : isAnc ? ancReviewModal : specialistConsultationModal
            const [getVitalsigns, tableId, id] = isAnc ? [getAncVitalSignsTable, '#'+modal._element.querySelector('.vitalsTable').id, ancRegId] : [getVitalSignsTableByVisit, '#'+modal._element.querySelector('.vitalsTable').id, visitId]
            
            http.post(`/doctors/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openDoctorModals(modal, modal._element.querySelector('#saveConsultationBtn'), response.data)
                        getVitalsigns(tableId, id, modal)}
                    btn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        btn.removeAttribute('disabled')
                        console.log(error)
                    })
                isAnc ? ancConsultationReviewModal.hide() : consultationReviewModal.hide()
        })
    })

    document.querySelectorAll('#newConsultationModal, #ancConsultationModal, #ancReviewModal, #newReviewModal, #specialistConsultationModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            doctorsModalClosingTasks(event, modal, textareaHeight)
            outPatientsVisitTable.draw()
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
         })
    })

    // review consultation loops
    document.querySelectorAll('#consultationReviewDiv, #visitHistoryDiv').forEach(div =>{
        div.addEventListener('click', function (event) {
            const deleteConsultationBtn                 = event.target.closest('#deleteReviewConsultationBtn')
            const updateResourceListBtn                 = event.target.closest('#updateResourceListBtn')
            const newSurgeryBtn                         = event.target.closest('#newSurgeryBtn')
            const SurgeryNoteBtn                        = event.target.closest('.updateSurgeryNoteBtn, .viewSurgeryNoteBtn')
            const deleteSurgeryNoteBtn                  = event.target.closest('.deleteSurgeryNoteBtn')
            const fileBtn                               = event.target.closest('#fileBtn')
            const collapseConsultationBtn               = event.target.closest('.collapseConsultationBtn')
            const collapseVisitBtn                      = event.target.closest('.collapseVisitBtn')
            const resultBtn                             = event.target.closest('#addResultBtn, #updateResultBtn')
            const deleteResultBtn                       = event.target.closest('.deleteResultBtn')
            const discontinueBtn                        = event.target.closest('.discontinueBtn')
            const viewer                                = 'doctor'
            if (collapseConsultationBtn) {
                const gotoDiv = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
                const [investigationTableId, treatmentTableId, otherPrescriptionsTableId]  = [gotoDiv.querySelector('.investigationTable').id, gotoDiv.querySelector('.treatmentTable').id, gotoDiv.querySelector('.otherPrescriptionsTable').id] 
                const conId   = gotoDiv.querySelector('.investigationTable').dataset.id
                const isHistory = +collapseConsultationBtn.getAttribute('data-ishistory')
    
                if ($.fn.DataTable.isDataTable( '#'+investigationTableId )){
                    $('#'+investigationTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable( '#'+treatmentTableId )){
                    $('#'+treatmentTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#' + otherPrescriptionsTableId)) {
                    $('#' + otherPrescriptionsTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable( '#deliveryNoteTable'+conId )){
                    $('#deliveryNoteTable'+conId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable( '#surgeryNoteTable'+conId )){
                    $('#surgeryNoteTable'+conId).dataTable().fnDestroy()
                }

                const goto = () => {
                    location.href = collapseConsultationBtn.getAttribute('data-goto')
                    window.history.replaceState({}, document.title, "/" + "doctors" )
                    getLabTableByConsultation(investigationTableId, consultationReviewModal._element, 'lab', conId, '')
                    getMedicationsByFilter(treatmentTableId, conId, consultationReviewModal._element)
                    getOtherPrescriptionsByFilter(otherPrescriptionsTableId, conId, consultationReviewModal._element)
                    getDeliveryNoteTable('deliveryNoteTable'+conId, conId, false)
                    getSurgeryNoteTable('surgeryNoteTable'+conId, conId, true)
                }
                setTimeout(goto, 300)
            }

            if (collapseVisitBtn) {
                const visitId               = collapseVisitBtn.getAttribute('data-id')
                const ancRegId              = collapseVisitBtn.getAttribute('data-ancregid')
                const [getVitalsigns, id]   = collapseVisitBtn.getAttribute('data-anc') ? [getAncVitalSignsTable, ancRegId] : [getVitalSignsTableByVisit, visitId]

                if ($.fn.DataTable.isDataTable('#vitalSignsHistory'+visitId)){
                    $('#vitalSignsHistory'+visitId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#billingTableHistory'+visitId)){
                    $('#billingTableHistory'+visitId).dataTable().fnDestroy()
                }

                const goto = () => {
                    location.href = collapseVisitBtn.getAttribute('data-gotovisit')
                    window.history.replaceState({}, document.title, "/" + "doctors" )
                    getVitalsigns('#vitalSignsHistory'+visitId, id, consultationHistoryModal)
                    getbillingTableByVisit('billingTableHistory'+visitId, visitId, consultationHistoryModal._element)
                }
                setTimeout(goto, 300)
            }
    
            if (updateResourceListBtn){
                updateResourceListBtn.setAttribute('disabled', 'disabled')
                investigationAndManagementModal._element.querySelector('.investigationAndManagementDiv').classList.remove('d-none')
                const btn = investigationAndManagementModal._element.querySelector('#addInvestigationAndManagementBtn')
                const addDiv = investigationAndManagementModal._element.querySelector('.addDiv')
                const conId = updateResourceListBtn.dataset.conid
                updateResourceListBtn.dataset.last === 'last' ? addDiv.classList.remove('d-none') : addDiv.classList.add('d-none') 
                btn.setAttribute('data-conid', updateResourceListBtn.dataset.conid)
                btn.setAttribute('data-visitid', updateResourceListBtn.dataset.visitid)
                btn.setAttribute('data-last', updateResourceListBtn.dataset.last)
                btn.setAttribute('data-investigationtable', '#investigationTable'+conId)
                btn.setAttribute('data-treatmenttable', '#treatmentTable'+conId)
                btn.setAttribute('data-otherprescriptionstable', '#otherPrescriptionsTable'+conId)
                getPrescriptionTableByConsultation('prescriptionTableConReview', conId, null, investigationAndManagementModal._element)
                investigationAndManagementModal.show()
                setTimeout(()=> {updateResourceListBtn.removeAttribute('disabled')}, 1000)
            }
    
            if (deleteConsultationBtn) {
                deleteConsultationBtn.setAttribute('disabled', 'disabled')
                if (confirm('If you delete this consultation you cannot get it back! Are you sure you want to delete?')) {
                    const id = deleteConsultationBtn.getAttribute('data-id')
                    const anc = deleteConsultationBtn.getAttribute('data-patienttype') == 'ANC'
                    http.delete(`/consultation/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){   
                                anc ? ancConsultationReviewModal.hide() : consultationReviewModal.hide()
                            }
                            deleteConsultationBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteConsultationBtn.removeAttribute('disabled')
                        })
                }
                deleteConsultationBtn.removeAttribute('disabled')
            }

            if (discontinueBtn){
                if (confirm('Are you sure you want to discontinue prescription?')) {
                    const prescriptionId = discontinueBtn.getAttribute('data-id')
                    const treatmentTableId = discontinueBtn.getAttribute('data-table')
                    http.patch(`/prescription/${prescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                $('#' + treatmentTableId).dataTable().fnDraw()
                            }
                        }
                    })
                    .catch((error) => {
                        if (error.response.status === 403){
                            alert(error.response.data.message); 
                        }
                        console.log(error)
                    })
                }
            }
    
            if (newSurgeryBtn) {
                createSurgeryNoteBtn.setAttribute('data-conid', newSurgeryBtn.dataset.id)
                createSurgeryNoteBtn.setAttribute('data-visitid', newSurgeryBtn.dataset.visitid)
                newSurgeryModal.show()
            }

            if (SurgeryNoteBtn) {
                const isUpdate = SurgeryNoteBtn.id == 'updateSurgeryNoteBtn'
                const [btn, modalBtn, modal ] = isUpdate ? [SurgeryNoteBtn, saveSurgeryNoteBtn, updateSurgeryModal] : [SurgeryNoteBtn, saveSurgeryNoteBtn, viewSurgeryModal]
                btn.setAttribute('disabled', 'disabled')
                saveSurgeryNoteBtn.setAttribute('data-table', btn.dataset.table)
                http.get(`/surgerynote/${btn.getAttribute('data-id')}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openModals(modal, modalBtn, response.data.data)
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                    })
                setTimeout(()=>{btn.removeAttribute('disabled')}, 2000)
            }

            if (deleteSurgeryNoteBtn){
                deleteSurgeryNoteBtn.setAttribute('disabled', 'disabled')
                const id = deleteSurgeryNoteBtn.getAttribute('data-id')
                const tableId = deleteSurgeryNoteBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete Delivery Note?')) {
                    http.delete(`/surgerynote/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            if ($.fn.DataTable.isDataTable('#' + tableId)) {
                                $('#' + tableId).dataTable().fnDraw()
                            }
                        }
                        deleteSurgeryNoteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteSurgeryNoteBtn.removeAttribute('disabled')
                    })
                } deleteSurgeryNoteBtn.removeAttribute('disabled')
            }
    
            if (fileBtn) {
                fileModal.show()
            }
    
            if (resultBtn) {
                const update = resultBtn.id == 'updateResultBtn'
                const [btn, modalBtn, modal] = update ? [resultBtn, saveResultBtn, updateResultModal] : [resultBtn, createResultBtn, addResultModal]
                modalBtn.setAttribute('data-table', btn.getAttribute('data-table'))
                modal._element.querySelector('#diagnosis').value = btn.getAttribute('data-diagnosis')
                modal._element.querySelector('#investigation').value = btn.getAttribute('data-investigation')
                if (update) {
                    http.get(`/investigations/${btn.getAttribute('data-id')}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openModals(updateResultModal, saveResultBtn, response.data.data)
                        }
                    })
                    .catch((error) => {
                        alert(error)
                    })
                } else {
                    modalBtn.setAttribute('data-id', btn.getAttribute('data-id'))
                    modal.show()
                }
            }
    
            if (deleteResultBtn){
                deleteResultBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this result?')) {
                    const [prescriptionId, prescriptionTableId] = [deleteResultBtn.getAttribute('data-id'), deleteResultBtn.getAttribute('data-table')]
                    http.patch(`/investigations/remove/${prescriptionId}`)
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
                deleteResultBtn.removeAttribute('disabled')
            }
        })
    })

    createSurgeryNoteBtn.addEventListener('click', function () {
        createSurgeryNoteBtn.setAttribute('disabled', 'disabled')
        const conId = createSurgeryNoteBtn.dataset.conid
        const visitId = createSurgeryNoteBtn.dataset.visitid

        let data = { ...getDivData(newSurgeryModal._element), conId, visitId }
        http.post('/surgerynote', {...data}, {"html": newSurgeryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newSurgeryModal.hide()
                clearDivValues(newSurgeryModal._element)
                if ($.fn.DataTable.isDataTable('#surgeryNoteTable' + conId)) {
                    $('#surgeryNoteTable' + conId).dataTable().fnDraw()
                }
            }
            createSurgeryNoteBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createSurgeryNoteBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    saveSurgeryNoteBtn.addEventListener('click', function () {
        saveSurgeryNoteBtn.setAttribute('disabled', 'disabled')
        const id        = saveSurgeryNoteBtn.dataset.id
        const tableId   = '#'+saveSurgeryNoteBtn.dataset.table

        http.patch(`/deliverynote/${id}`, getDivData(updateSurgeryModal._element), {"html": updateSurgeryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateSurgeryModal.hide()
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).dataTable().fnDraw()
                }
            }
            saveSurgeryNoteBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveSurgeryNoteBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    document.querySelectorAll('#createResultBtn, #saveResultBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const [resultDiv, modal, url] = btn.id == 'createResultBtn' ? [addResultDiv, addResultModal, 'create'] : [updateResultDiv, updateResultModal, 'update']
            const [prescriptionId, investigationTableId] = [btn.getAttribute('data-id'), btn.getAttribute('data-table')]
            btn.setAttribute('disabled', 'disabled')
            let data = { ...getDivData(resultDiv), prescriptionId }
    
            http.patch(`/investigations/${url}/${prescriptionId}`, { ...data }, { "html": resultDiv })
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        clearDivValues(resultDiv)
                        clearValidationErrors(resultDiv)
    
                        if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                            $('#' + investigationTableId).dataTable().fnDraw()
                        }
                    }
                    btn.removeAttribute('disabled')
                    modal.hide()
                })
                .catch((error) => {
                    console.log(error)
                    btn.removeAttribute('disabled')
                })
        })
    })

    // tasks to run when closing review consultation modal
    document.querySelectorAll('#consultationReviewModal, #ancConsultationReviewModal, #consultationHistoryModal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function(event) {
            regularConsultationReviewDiv.innerHTML = ''
            ancConsultationReviewDiv.innerHTML = ''
            visitHistoryDiv.innerHTML = ''
            outPatientsVisitTable.draw()
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        })
    })

    patientsInfo.addEventListener('click', function(){patientsInfo.setAttribute('hidden', 'hidden'); patientsFullName.removeAttribute('hidden')})
    patientsFullName.addEventListener('click', function(){patientsFullName.setAttribute('hidden', 'hidden'); patientsInfo.removeAttribute('hidden')})

    downloadReportBtn.addEventListener('click', function () {
        const patientFullName = reportModalBody.querySelector('#patientsFullName').innerHTML
        const type = reportModalBody.querySelector('#type').innerHTML

        var opt = {
        margin:       0.5,
        filename:     patientFullName + `'s ${type}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(reportModalBody).save()
    })
})

function displayResourceList(datalistEl, data) {
    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', 'resourceOption')
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)
        option.setAttribute('data-cat', line.category)
        option.setAttribute('data-plainname', line.plainName)

        !datalistEl.options.namedItem(line.name) ? datalistEl.appendChild(option) : ''
    })
}

function openDoctorModals(modal, button, {id, visitId, ancRegId, patientType, cardNo, ...data}) {
    for (let name in data) {
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        nameInput.value = data[name]
    }
    
    if (modal._element.id !== 'consultationHistoryModal'){
        modal._element.querySelector('#updateKnownClinicalInfoBtn').setAttribute('data-id', id)
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-ancregid', ancRegId)
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-patienttype', patientType)
    
        if (modal._element.id !== 'consultationReviewModal') {
            button.setAttribute('data-id', visitId)
            modal.show()
        }
    } else {
        button.setAttribute('href', `https://portal.sandrahospitalmkd.com/Consultations/History?CardNumber=${cardNo}`)
    }
    
}

function getSelectedResourceValues(modal, inputEl, datalistEl) {  
    const selectedOption = datalistEl.options.namedItem(inputEl.value)
        if (selectedOption) {
            return {
                resource : selectedOption.getAttribute('data-id'),
                resourceCategory : selectedOption.getAttribute('data-cat'),              
            }
        } else {
            return ""
        }
}

export {openDoctorModals}
