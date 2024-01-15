import { Modal, Collapse, Toast, Offcanvas } from "bootstrap"
import * as ECT from "@whoicd/icd11ect"
import "@whoicd/icd11ect/style.css"
import { clearDivValues, getOrdinal, getDivData, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, clearValidationErrors, doctorsModalClosingTasks, bmiCalculator, lmpCalculator, filterPatients, openModals} from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import http from "./http";
import { getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getTreatmentTableByConsultation, getInpatientsVisitTable, getOutpatientsVisitTable, getAncPatientsVisitTable} from "./tables/doctorstables"
import { getVitalsignsChartByVisit } from "./charts/vitalsignsCharts"
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { getbillingTableByVisit } from "./tables/billingTables"
import { getDeliveryNoteTable } from "./tables/nursesTables"

window.addEventListener('DOMContentLoaded', function () {
    const waitingListOffcanvas              = new Offcanvas(document.getElementById('waitingListOffcanvas1'))
    const newConsultationModal              = new Modal(document.getElementById('newConsultationModal'))
    const ancConsultationModal              = new Modal(document.getElementById('ancConsultationModal'))
    const ancReviewModal                    = new Modal(document.getElementById('ancReviewModal'))
    const consultationReviewModal           = new Modal(document.getElementById('consultationReviewModal'))
    const surgeryModal                      = new Modal(document.getElementById('surgeryModal'))
    const fileModal                         = new Modal(document.getElementById('fileModal'))
    const newReviewModal                    = new Modal(document.getElementById('newReviewModal'))
    const specialistConsultationModal       = new Modal(document.getElementById('specialistConsultationModal'))
    const vitalsignsModal                   = new Modal(document.getElementById('vitalsignsModal'))
    const addResultModal                    = new Modal(document.getElementById('addResultModal'))
    const updateResultModal                 = new Modal(document.getElementById('updateResultModal'))
    const investigationsModal               = new Modal(document.getElementById('investigationsModal'))
    const investigationAndManagementModal   = new Modal(document.getElementById('investigationAndManagementModal'))
    
    const consultationReviewDiv             = document.querySelector('#consultationReviewDiv')
    const knownClinicalInfoDiv              = document.querySelectorAll('#knownClinicalInfoDiv')
    const consultationDiv                   = document.querySelectorAll('#consultationDiv')
    const addResultDiv                      = addResultModal._element.querySelector('#resultDiv')
    const updateResultDiv                   = updateResultModal._element.querySelector('#resultDiv')
    
    const reviewPatientbtn                  = consultationReviewModal._element.querySelector('#reviewPatientBtn')
    const reviewAncPatientbtn               = consultationReviewModal._element.querySelector('#reviewAncPatientBtn')
    const specialistConsultationbtn         = consultationReviewModal._element.querySelector('#specialistConsultationBtn')
    const vitalsignsChartReview             = consultationReviewModal._element.querySelector('#vitalsignsChart')
    const vitalsignsChart                   = vitalsignsModal._element.querySelector('#vitalsignsChart')
    const createResultBtn                   = addResultModal._element.querySelector('#createResultBtn')
    const saveResultBtn                     = updateResultModal._element.querySelector('#saveResultBtn')
    const addInvestigationAndManagmentBtn   = document.querySelectorAll('#addInvestigationAndManagementBtn')
    const updateKnownClinicalInfoBtn        = document.querySelectorAll('#updateKnownClinicalInfoBtn')
    const addVitalsignsBtn                  = document.querySelectorAll('#addVitalsignsBtn')
    const saveConsultationBtn               = document.querySelectorAll('#saveConsultationBtn')
    const waitingBtn                        = document.querySelector('#waitingBtn')
    const clearDiagnosisBtns                = document.querySelectorAll('.clearDiagnosis')

    const [outPatientsTab, ancPatientsTab, inPatientsTab]  = [document.querySelector('#nav-outPatients-tab'), document.querySelector('#nav-ancPatients-tab'), document.querySelector('#nav-inPatients-tab')]
    
    const [resourceInput]  = [document.querySelectorAll('#resource')]

    bmiCalculator(document.querySelectorAll('#height, #weight'))

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
    let inPatientsVisitTable, ancPatientsVisitTable 

    const outPatientsVisitTable = getOutpatientsVisitTable('#outPatientsVisitTable', 'My Patients')
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
    
    filterPatients(document.querySelectorAll('#filterListOutPatients, #filterListInPatients, #filterListAncPatients'))
    
    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationReviewBtn    = event.target.closest('.consultationReviewBtn')
            const vitalsignsBtn            = event.target.closest('.vitalSignsBtn')
            const investigationsBtn        = event.target.closest('.investigationsBtn')
    
            if (consultationReviewBtn) {
                consultationReviewBtn.setAttribute('disabled', 'disabled')
                const visitId       = consultationReviewBtn.getAttribute('data-id')
                const patientType   = consultationReviewBtn.getAttribute('data-patientType')
                resourceInput.forEach(input => {input.setAttribute('data-sponsorcat', consultationReviewBtn.getAttribute('data-sponsorcat'))})
                reviewPatientbtn.setAttribute('data-id', visitId)
                reviewPatientbtn.setAttribute('data-patientType', patientType)
                newReviewModal._element.querySelector('#saveConsultationBtn').setAttribute('data-id', visitId)
                
                specialistConsultationbtn.setAttribute('data-id', visitId)
                specialistConsultationbtn.setAttribute('data-patientType', patientType)
                specialistConsultationModal._element.querySelector('#saveConsultationBtn').setAttribute('data-patientType', patientType)
    
                reviewAncPatientbtn.setAttribute('data-id', visitId)
                reviewAncPatientbtn.setAttribute('data-patientType', patientType)
                ancReviewModal._element.querySelector('#saveConsultationBtn').setAttribute('data-patientType', patientType)
    
                http.get(`/consultation/consultations/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        let iteration = 0
                        let count = 0
    
                        const consultations = response.data.consultations.data
                        const patientBio = response.data.bio
    
                        openDoctorModals(consultationReviewModal, consultationReviewDiv, patientBio)
                        addResultModal._element.querySelector('#patient').value = patientBio.patientId
                        updateResultModal._element.querySelector('#patient').value = patientBio.patientId
                        investigationAndManagementModal._element.querySelector('#patient').value = patientBio.patientId
                        addResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
                        updateResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
                        investigationAndManagementModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
    
                        if (patientType === 'ANC') {
                            reviewAncPatientbtn.classList.remove('d-none')
                            reviewPatientbtn.classList.add('d-none') 
                            specialistConsultationbtn.classList.add('d-none')
                        } else {
                            reviewAncPatientbtn.classList.add('d-none')
                            reviewPatientbtn.classList.remove('d-none')
                            specialistConsultationbtn.classList.remove('d-none')
                        }
                            
                        consultations.forEach(line => {
                            iteration++
                            
                            iteration > 1 ? count++ : ''
                
                            if (patientType === 'ANC') {
                                consultationReviewDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations.length, line, '')
                            } else {
                                consultationReviewDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations.length, line, '')
                            }
                        })
    
                        getVitalSignsTableByVisit('#vitalSignsConsultationReview', visitId, consultationReviewModal)
                        http.get('/vitalsigns/load/visit_vitalsigns_chart',{params: {  visitId: visitId }})
                        .then((response) => {
                            getVitalsignsChartByVisit(vitalsignsChartReview, response, consultationReviewModal)
                        })
                        .catch((error) => {
                            console.log(error)
                        })
                        getbillingTableByVisit('billingTable', visitId, consultationReviewModal._element)
                        
                        consultationReviewModal.show()
                    }
                    consultationReviewBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultationReviewBtn.removeAttribute('disabled')
                    console.log(error)
                })
            }
    
            if (vitalsignsBtn) {
                vitalsignsBtn.setAttribute('disabled', 'disabled')
                const tableId = '#'+vitalsignsModal._element.querySelector('.vitalsTable').id
                const visitId = vitalsignsBtn.getAttribute('data-id')
                vitalsignsModal._element.querySelector('#patient').value = vitalsignsBtn.getAttribute('data-patient')
                vitalsignsModal._element.querySelector('#sponsor').value = vitalsignsBtn.getAttribute('data-sponsor')
                vitalsignsModal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    
                getVitalSignsTableByVisit(tableId, visitId, vitalsignsModal)
    
                http.get('/vitalsigns/load/visit_vitalsigns_chart',{params: {  visitId: visitId }})
                .then((response) => {
                    getVitalsignsChartByVisit(vitalsignsChart, response, vitalsignsModal)
                    vitalsignsBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    vitalsignsBtn.removeAttribute('disabled')
                    console.log(error)
                })
    
                vitalsignsModal.show()
            }

            if (investigationsBtn) {
                investigationsBtn.setAttribute('disabled', 'disabled')
                const tableId = investigationsModal._element.querySelector('.investigationsTable').id
                const visitId = investigationsBtn.getAttribute('data-id')
                investigationsModal._element.querySelector('#patient').value = investigationsBtn.getAttribute('data-patient')
                investigationsModal._element.querySelector('#sponsor').value = investigationsBtn.getAttribute('data-sponsor')
    
                getLabTableByConsultation(tableId, investigationsModal._element, '', null, visitId)
    
                investigationsModal.show()
                investigationsBtn.removeAttribute('disabled')
            }
        })
    })

    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const consultBtn    = event.target.closest('.consultBtn')
        const removeBtn     = event.target.closest('.removeBtn')

        if (consultBtn) {
            consultBtn.setAttribute('disabled', 'disabled')
            const [visitId, patientType]     = [consultBtn.getAttribute('data-id'),consultBtn.getAttribute('data-patientType')]
            resourceInput.forEach(input => {input.setAttribute('data-sponsorcat', consultBtn.getAttribute('data-sponsorcat'))})

            http.post(`/doctors/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        if (patientType === 'ANC'){
                            openDoctorModals(ancConsultationModal, ancConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                            getVitalSignsTableByVisit('#vitalSignsTableAnc', visitId, ancConsultationModal)
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

    vitalsignsModal._element.addEventListener('hide.bs.modal', () => {
        outPatientsVisitTable.draw()
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
    })

    // manipulating all known clinical info div
    updateKnownClinicalInfoBtn.forEach(updateBtn => {
        updateBtn.addEventListener('click', function () {
            knownClinicalInfoDiv.forEach(div => {
                if (div.dataset.div === updateBtn.dataset.btn) {
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
            const div = addBtn.parentElement.parentElement.querySelector('#addVitalsignsDiv')
            addBtn.setAttribute('disabled', 'disabled')
            const [visitId, tableId] = [addBtn.getAttribute('data-id'), div.parentNode.parentNode.querySelector('.vitalsTable').id]
            let data = {...getDivData(div), visitId}

            http.post('/vitalsigns', {...data}, {"html": div})
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
        })
    })

    document.querySelectorAll('#vitalSignsTableNew, #vitalSignsTableSpecialist, #vitalSignsTableAnc, #vitalSignsTableAncReview, #vitalSignsTableReview').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn  = event.target.closest('.deleteBtn')
            if (deleteBtn){
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/vitalsigns/${id}`)
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

    document.querySelectorAll('#prescriptionTablenew, #prescriptionTablespecialist, #prescriptionTableanc, #prescriptionTableancReview, #prescriptionTableconReview').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn  = event.target.closest('.deleteBtn')
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
        })
    })

    // getting data from all consultation divs
    saveConsultationBtn.forEach(saveBtn => {
        saveBtn.addEventListener('click', function () {
            const div = saveBtn.parentElement.parentElement
            saveBtn.setAttribute('disabled', 'disabled')
            const [investigationAndManagementDiv, investigationAndManagementBtn] = [div.parentElement.querySelector('.investigationAndManagementDiv'), div.parentElement.querySelector('#addInvestigationAndManagementBtn')]
            const modal = div.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode
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
                    getPrescriptionTableByConsultation(tableId, response.data.id, modal)
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
                if (selectedOption.getAttribute('data-cat') == 'Medications'){
                    div.querySelector('.qty').classList.add('d-none')
                    div.querySelector('#quantity').value = ''
                    div.querySelector('.pres').classList.remove('d-none')
                } else {
                    div.querySelector('.qty').classList.remove('d-none')
                    div.querySelector('#quantity').value = 1
                    div.querySelector('.pres').classList.add('d-none')
                }
            }
        })        
    })

    //adding investigation and management on all divs
    addInvestigationAndManagmentBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            const div = addBtn.parentElement.parentElement.parentElement
            addBtn.setAttribute('disabled', 'disabled')
            const resourceValues = getSelectedResourceValues(div, div.querySelector('.resource'), div.querySelector(`#resourceList${div.dataset.div}`))
            const [conId, visitId, divPrescriptionTableId] = [addBtn.dataset.conid, addBtn.dataset.visitid, '#'+div.querySelector('.prescriptionTable').id]
            
            let data = {...getDivData(div), ...resourceValues, conId, visitId}

            http.post('prescription', {...data}, {"html": div})
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
                if ($.fn.DataTable.isDataTable( '#billingTable' )){
                    $('#billingTable').dataTable().fnDraw()
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
    consultationReviewModal._element.querySelectorAll('#reviewPatientBtn, #reviewAncPatientBtn, #specialistConsultationBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            btn.setAttribute('disabled', 'disabled')
            const [visitId, patientType] = [btn.getAttribute('data-id'), btn.getAttribute('data-patientType')]
            const modal = btn.id == 'reviewPatientBtn' ?  newReviewModal : btn.id == 'reviewAncPatientBtn' ? ancReviewModal : specialistConsultationModal
            
                http.post(`/doctors/consult/${ visitId }`, {patientType})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openDoctorModals(modal, modal._element.querySelector('#saveConsultationBtn'), response.data)
                            getVitalSignsTableByVisit('#'+modal._element.querySelector('.vitalsTable').id, visitId, modal)
                            outPatientsVisitTable.draw()
                            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
                            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
                        }
                        btn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        btn.removeAttribute('disabled')
                        alert(error)
                    })
            consultationReviewModal.hide()
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
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteReviewConsultationBtn')
        const updateResourceListBtn                 = event.target.closest('#updateResourceListBtn')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')
        const fileBtn                               = event.target.closest('#fileBtn')
        const collapseBtn                           = event.target.closest('.collapseBtn')
        const resultBtn                             = event.target.closest('#addResultBtn, #updateResultBtn')
        const deleteResultBtn                       = event.target.closest('.deleteResultBtn')

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const [investigationTableId, treatmentTableId]  = [gotoDiv.querySelector('.investigationTable').id, gotoDiv.querySelector('.treatmentTable').id] 
            const conId                                     = gotoDiv.querySelector('.investigationTable').dataset.id

            if ($.fn.DataTable.isDataTable( '#'+investigationTableId )){
                $('#'+investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable( '#'+treatmentTableId )){
                $('#'+treatmentTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "doctors" )
                getLabTableByConsultation(investigationTableId, consultationReviewModal._element, '', conId, '')
                getTreatmentTableByConsultation(treatmentTableId, conId, consultationReviewModal._element)
                getDeliveryNoteTable('deliveryNoteTable'+conId, conId)
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
            getPrescriptionTableByConsultation('prescriptionTableconReview', conId, investigationAndManagementModal)
            investigationAndManagementModal.show()
            setTimeout(()=> {updateResourceListBtn.removeAttribute('disabled')}, 1000)

        }

        if (deleteConsultationBtn) {
            deleteConsultationBtn.setAttribute('disabled', 'disabled')
            if (confirm('If you delete this consultation you cannot get it back! Are you sure you want to delete?')) {
                const id = deleteConsultationBtn.getAttribute('data-id')
                http.delete(`/consultation/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){   
                            consultationReviewModal.hide()
                        }
                        deleteConsultationBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteConsultationBtn.removeAttribute('disabled')
                    })
            }
        }

        if (surgeryBtn) {
            surgeryModal.show()
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
        }
    })

    document.querySelectorAll('#createResultBtn, #saveResultBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const [resultDiv, modal] = btn.id == 'createResultBtn' ? [addResultDiv, addResultModal] : [updateResultDiv, updateResultModal]
            const [prescriptionId, investigationTableId] = [btn.getAttribute('data-id'), btn.getAttribute('data-table')]
            btn.setAttribute('disabled', 'disabled')
            let data = { ...getDivData(resultDiv), prescriptionId }
    
            http.patch(`/investigations/${prescriptionId}`, { ...data }, { "html": resultDiv })
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
    consultationReviewModal._element.addEventListener('hide.bs.modal', function(event) {
        consultationReviewDiv.innerHTML = ''
        outPatientsVisitTable.draw()
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
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

        !datalistEl.options.namedItem(line.name) ? datalistEl.appendChild(option) : ''
    })
}

function openDoctorModals(modal, button, {id, visitId, ...data}) {
    for (let name in data) {
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        nameInput.value = data[name]
    }
    modal._element.querySelector('#updateKnownClinicalInfoBtn').setAttribute('data-id', id)
    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    
    if (modal._element.id !== 'consultationReviewModal') {
        button.setAttribute('data-id', visitId)
        modal.show()
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
