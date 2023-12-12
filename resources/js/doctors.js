import { Modal, Collapse, Toast, Offcanvas } from "bootstrap"
import * as ECT from "@whoicd/icd11ect"
import "@whoicd/icd11ect/style.css"
import { clearDivValues, getOrdinal, getDivData, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, clearValidationErrors, doctorsModalClosingTasks, addDays, getWeeksDiff, getWeeksModulus} from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import http from "./http";
import { getAllPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getTreatmentTableByConsultation, getInpatientsVisitTable, getUserRegularPatientsVisitTable, getUserAncPatientsVisitTable} from "./tables/doctorstables"
import { getVitalsignsChartByVisit } from "./charts/vitalsignsCharts"
import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

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
    const investigationsModal               = new Modal(document.getElementById('investigationsModal'))
    
    const consultationReviewDiv             = document.querySelector('#consultationReviewDiv')
    const investigationAndManagmentDiv      = document.querySelectorAll('.investigationAndManagementDiv')
    const knownClinicalInfoDiv              = document.querySelectorAll('#knownClinicalInfoDiv')
    const addVitalsignsDiv                  = document.querySelectorAll('#addVitalsignsDiv')
    const consultationDiv                   = document.querySelectorAll('#consultationDiv')
    const resultDiv                         = addResultModal._element.querySelector('#resultDiv')
    
    const reviewPatientbtn                  = consultationReviewModal._element.querySelector('#reviewPatientBtn')
    const reviewAncPatientbtn               = consultationReviewModal._element.querySelector('#reviewAncPatientBtn')
    const specialistConsultationbtn         = consultationReviewModal._element.querySelector('#specialistConsultationBtn')
    const vitalsignsChartReview             = consultationReviewModal._element.querySelector('#vitalsignsChart')
    const vitalsignsChart                   = vitalsignsModal._element.querySelector('#vitalsignsChart')
    const saveResultBtn                     = addResultModal._element.querySelector('#saveResultBtn')
    const addInvestigationAndManagmentBtn   = document.querySelectorAll('#addInvestigationAndManagementBtn')
    const updateKnownClinicalInfoBtn        = document.querySelectorAll('#updateKnownClinicalInfoBtn')
    const addVitalsignsBtn                  = document.querySelectorAll('#addVitalsignsBtn')
    const saveConsultationBtn               = document.querySelectorAll('#saveConsultationBtn')
    const waitingBtn                        = document.querySelector('#waitingBtn')


    const [userRegularPatientsTab, userAncPatientsTab, allPatientsTab, inPatientsTab]  = [document.querySelector('#nav-yourRegularPatients-tab'), document.querySelector('#nav-yourAncPatients-tab'), document.querySelector('#nav-allPatients-tab'), document.querySelector('#nav-inPatients-tab')]
    
    const [resourceInput, heightEl, lmpEl]  = [document.querySelectorAll('#resource'), document.querySelectorAll('#height'), document.querySelectorAll('#lmp')]

    heightEl.forEach(heightInput => {
        heightInput.addEventListener('input',  function (e){
            const div = heightInput.parentElement.parentElement.parentElement
            if (heightInput.dataset.id == div.id){
                div.querySelector('#bmi').value = (div.querySelector('#weight').value.split('k')[0]/div.querySelector('#height').value.split('m')[0]**2).toFixed(2)
            }
        })
    })

    lmpEl.forEach(lmp => {
        lmp.addEventListener('change', function () {
            consultationDiv.forEach(div => {
                if (lmp.dataset.lmp == div.dataset.div){
                    if (lmp.value){
                        const lmpDate = new Date(lmp.value) 
                        div.querySelector('#edd').value = addDays(lmpDate, 280).toISOString().split('T')[0]
                        div.querySelector('#ega').value = String(getWeeksDiff(new Date(), lmpDate)).split('.')[0] + 'W' + ' ' + getWeeksModulus(new Date, lmpDate)%7 + 'D'
                    }                    
                }
            })
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
    let allPatientsVisitTable, inPatientsVisitTable, userAncPatientsVisitTable 

    const userRegularPatientsVisitTable = getUserRegularPatientsVisitTable('#userRegularPatientsVisitTable')
    const waitingTable = getWaitingTable('#waitingTable')

    userRegularPatientsTab.addEventListener('click', function() {userRegularPatientsVisitTable.draw()})

    userAncPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#userAncPatientsVisitTable' )){
            $('#userAncPatientsVisitTable').dataTable().fnDraw()
        } else {
            userAncPatientsVisitTable = getUserAncPatientsVisitTable('#userAncPatientsVisitTable')
        }
    })

    allPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#allPatientsVisitTable' )){
            $('#allPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getAllPatientsVisitTable('#allPatientsVisitTable')
        }
    })    

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getInpatientsVisitTable('#inPatientsVisitTable')
        }
    })
    
    
    document.querySelectorAll('#allPatientsVisitTable, #userRegularPatientsVisitTable, #inPatientsVisitTable, #userAncPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationReviewBtn    = event.target.closest('.consultationReviewBtn')
            const vitalsignsBtn             = event.target.closest('.vitalSignsBtn')
            const investigationsBtn             = event.target.closest('.investigationsBtn')
    
            if (consultationReviewBtn) {
                consultationReviewBtn.setAttribute('disabled', 'disabled')
                const visitId       = consultationReviewBtn.getAttribute('data-id')
                const patientType   = consultationReviewBtn.getAttribute('data-patientType')
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
    
                        openModals(consultationReviewModal, consultationReviewDiv, patientBio)
                        addResultModal._element.querySelector('#patient').value = patientBio.patientId
                        addResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
    
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
        const removeBtn  = event.target.closest('.removeBtn')

        if (consultBtn) {
            consultBtn.setAttribute('disabled', 'disabled')
            const visitId       = consultBtn.getAttribute('data-id')
            const patientType   = consultBtn.getAttribute('data-patientType')

            http.post(`/visits/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        if (patientType === 'ANC'){
                            openModals(ancConsultationModal, ancConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                            getVitalSignsTableByVisit('#vitalSignsTableAnc', visitId, ancConsultationModal)
                        } else{
                            openModals(newConsultationModal, newConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
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
        userRegularPatientsVisitTable.draw()
        userAncPatientsVisitTable ? userAncPatientsVisitTable.draw() : ''
        allPatientsVisitTable ? allPatientsVisitTable.draw() : ''
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
    })

    vitalsignsModal._element.addEventListener('hide.bs.modal', () => {
        userRegularPatientsVisitTable.draw()
        allPatientsVisitTable ? allPatientsVisitTable.draw() : ''
        userAncPatientsVisitTable ? userAncPatientsVisitTable.draw() : ''
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
            addVitalsignsDiv.forEach(div => {
                if (div.dataset.div === addBtn.dataset.btn) {
                    addBtn.setAttribute('disabled', 'disabled')
                    const visitId = addBtn.getAttribute('data-id')
                    const tableId = div.parentNode.parentNode.querySelector('.vitalsTable').id
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
                }
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

    document.querySelectorAll('#prescriptionTablenew, #prescriptionTablespecialist, #prescriptionTableanc, #prescriptionTableancReview').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn  = event.target.closest('.deleteBtn')
            if (deleteBtn){
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this prescription?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/prescription/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                if ($.fn.DataTable.isDataTable('#'+table.id)){
                                $('#'+table.id).dataTable().fnDraw()
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
            consultationDiv.forEach(div => {
                if (saveBtn.dataset.btn === div.dataset.div) {
                    const visitId = saveBtn.getAttribute('data-id')
                    const investigationDiv = div.parentElement.querySelector('.investigationAndManagementDiv')
                    const investigationBtn = div.parentElement.querySelector('#addInvestigationAndManagementBtn')
                    const modal = div.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode
                    
                    saveBtn.setAttribute('disabled', 'disabled')
                    const tableId = investigationDiv.querySelector('.prescriptionTable').id
                    let data = {...getDivData(div), visitId}

                    http.post('/consultation', {...data}, {"html": div})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled')
                    
                            saveBtn.textContent === 'Saved' ? saveBtn.textContent = `Save` : saveBtn.textContent = 'Saved'
                            investigationDiv.classList.remove('d-none')
                            location.href = '#'+investigationDiv.id
                            
                            investigationBtn.setAttribute('data-conId', response.data.id)
                            investigationBtn.setAttribute('data-visitId', visitId)
                            window.history.replaceState({}, document.title, "/" + "doctors" )
                            
                            new Toast(div.querySelector('#saveConsultationToast'), {delay:2000}).show()
                            getPrescriptionTableByConsultation(tableId, response.data.id, modal)
                            waitingTable.draw()
                            userRegularPatientsVisitTable.draw()
                            userAncPatientsVisitTable ? userAncPatientsVisitTable.draw() : ''
                            allPatientsVisitTable ? allPatientsVisitTable.draw() : ''
                            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        saveBtn.removeAttribute('disabled')
                    })
                }
            })
        })
    })

     // All consultation resource inputs
     resourceInput.forEach(input => {
        input.addEventListener('input', function () {
            investigationAndManagmentDiv.forEach(div => {
                if (input.dataset.input === div.dataset.div) {
                    const datalistEl = div.querySelector(`#resourceList${div.dataset.div}`)
                     if (input.value < 2) {
                        datalistEl.innerHTML = ''
                    }
                    if (input.value.length > 2) {
                        http.get(`/resources/list`, {params: {resource: input.value}}).then((response) => {
                            displayResourceList(datalistEl, response.data)
                        })
                    }
                    const selectedOption = datalistEl.options.namedItem(input.value)
                    if (selectedOption){
                        if (selectedOption.getAttribute('data-cat') == 'Medication'){
                            div.querySelector('.qty').classList.add('d-none')
                            div.querySelector('.pres').classList.remove('d-none')
                        } else {
                            div.querySelector('.qty').classList.remove('d-none')
                            div.querySelector('.pres').classList.add('d-none')
                    }
                    }
                }
            })
        })        
    })

    //adding investigation and management on all divs
    addInvestigationAndManagmentBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            investigationAndManagmentDiv.forEach(div => {
                
                if (addBtn.dataset.btn === div.dataset.div) {
                    addBtn.setAttribute('disabled', 'disabled')
                    const resourceValues = getSelectedResourceValues(div, div.querySelector('.resource'), div.querySelector(`#resourceList${div.dataset.div}`))
                    const conId = addBtn.dataset.conid
                    const visitId = addBtn.dataset.visitid
                    const divPrescriptionTableId = '#'+div.querySelector('.prescriptionTable').id
                    
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
                        addBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                        addBtn.removeAttribute('disabled')
                    }) 
                }
            })
        })
    })

    // REVIEW CONSULTATION MODAL CODE
    //open, review,specialist review and anc review modals
    consultationReviewModal._element.querySelectorAll('#reviewPatientBtn, #reviewAncPatientBtn, #specialistConsultationBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            btn.setAttribute('disabled', 'disabled')
            let modal
                const [visitId, patientType] = [btn.getAttribute('data-id'), btn.getAttribute('data-patientType')]
                btn.id == 'reviewPatientBtn' ? modal = newReviewModal :
                btn.id == 'specialistConsultationBtn' ? modal = specialistConsultationModal :
                btn.id == 'reviewAncPatientBtn' ? modal = ancReviewModal : ''
            
                http.post(`/visits/consult/${ visitId }`, {patientType})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openModals(modal, modal._element.querySelector('#saveConsultationBtn'), response.data)
                            getVitalSignsTableByVisit('#'+modal._element.querySelector('.vitalsTable').id, visitId, modal)
                            userRegularPatientsVisitTable.draw()
                            userAncPatientsVisitTable ? userAncPatientsVisitTable.draw() : ''
                            allPatientsVisitTable ? allPatientsVisitTable.draw() : ''
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
            userRegularPatientsVisitTable.draw()
            userAncPatientsVisitTable ? userAncPatientsVisitTable.draw() : ''
            allPatientsVisitTable ? allPatientsVisitTable.draw() : ''
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
         })
    })

    // review consultation loops
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteReviewConsultationBtn')
        const updateInvestigationAndManagmentDiv    = document.querySelectorAll('.investigationAndManagmentDiv')
        const addInvestigationAndManagmentBtn       = event.target.closest('#addInvestigationAndManagmentBtn')
        const updateResourceListBtn                 = event.target.closest('#updateResourceListBtn')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')
        const fileBtn                               = event.target.closest('#fileBtn')
        const collapseBtn                           = event.target.closest('.collapseBtn')
        const deleteBtn                             = event.target.closest('.deleteBtn')
        const resourceInput                         = consultationReviewDiv.querySelector('.resource')
        const addResultBtn                          = event.target.closest('#addResultBtn')
        const deleteResultBtn                       = event.target.closest('.deleteResultBtn')

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const investigationTableId  = gotoDiv.querySelector('.investigationTable').id
            const treatmentTableId      = gotoDiv.querySelector('.treatmentTable').id
            const conId                 = gotoDiv.querySelector('.investigationTable').dataset.id

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
            }
            setTimeout(goto, 300)
        }

        if (updateResourceListBtn){
            const div = updateResourceListBtn.parentElement.parentElement
            div.querySelector('.resourceDiv').classList.toggle('d-none')
            const tableId = div.querySelector('.prescriptionTable').id
            const conId   = div.querySelector('.prescriptionTable').dataset.id

            if ($.fn.DataTable.isDataTable( '#'+tableId )){
                $('#'+tableId).dataTable().fnDestroy()
            }
            getPrescriptionTableByConsultation(tableId, conId, consultationReviewModal._element)
            location.href = div.getAttribute('data-goto')
            window.history.replaceState({}, document.title, "/" + "doctors" )
        }

        if (deleteBtn){
            const id = deleteBtn.getAttribute('data-id')
            const tableId = deleteBtn.getAttribute('data-table')
            
            if (confirm('Are you sure you want to delete this prescription?')) {
                deleteBtn.setAttribute('disabled', 'disabled')
                http.delete(`/prescription/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            if ($.fn.DataTable.isDataTable('#'+tableId)){
                            $('#'+tableId).dataTable().fnDraw()
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

        resourceInput.addEventListener('input', function () {
            updateInvestigationAndManagmentDiv.forEach(div => {
                if (resourceInput.dataset.input === div.dataset.div) {
                    const datalistEl = div.querySelector(`#resourceList${div.dataset.div}`)

                     if (resourceInput.value < 2) {
                        datalistEl.innerHTML = ''
                    }
                    if (resourceInput.value.length > 2) {
                        http.get(`/resources/list`, {params: {resource: resourceInput.value}}).then((response) => {
                            displayResourceList(datalistEl, response.data)
                        })
                    }
                    const selectedOption = datalistEl.options.namedItem(resourceInput.value)
                    if (selectedOption){
                        if (selectedOption.getAttribute('data-cat') == 'Medication'){
                            console.log('it is')
                            div.querySelector('#qty').classList.add('d-none')
                            div.querySelector('#pres').classList.remove('d-none')
                        } else {
                            div.querySelector('#qty').classList.remove('d-none')
                            div.querySelector('#pres').classList.add('d-none')
                    }
                    }
                }
            })
        })

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

        if (addInvestigationAndManagmentBtn) {
            updateInvestigationAndManagmentDiv.forEach(div => {
                if (addInvestigationAndManagmentBtn.dataset.btn === div.dataset.div) {
                    addInvestigationAndManagmentBtn.setAttribute('disabled', 'disabled')

                    const resourceValues = getSelectedResourceValues(div, div.querySelector('.resource'), div.querySelector(`#resourceList${div.dataset.div}`))
                    const conId = addInvestigationAndManagmentBtn.dataset.conid
                    const visitId = addInvestigationAndManagmentBtn.dataset.visitid
                    const divPrescriptionTableId = '#'+div.querySelector('.prescriptionTable').id
                    const investigationTableId = '#investigationTable'+conId
                    const medicationTableId = '#treatmentTable'+conId
                    let data = {...getDivData(div), ...resourceValues, conId, visitId}

                    http.post('prescription', {...data}, {"html": div})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            new Toast(div.querySelector('#saveUpdateInvestigationAndManagementToast'), {delay:2000}).show()
                            clearDivValues(div)
                            clearValidationErrors(div)
                        }
                        if ($.fn.DataTable.isDataTable( divPrescriptionTableId )){
                            $(divPrescriptionTableId).dataTable().fnDraw()
                        }
                        if ($.fn.DataTable.isDataTable( investigationTableId )){
                            $(investigationTableId).dataTable().fnDraw()
                        }
                        if ($.fn.DataTable.isDataTable( medicationTableId )){
                            $(medicationTableId).dataTable().fnDraw()
                        }
                        addInvestigationAndManagmentBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                        addInvestigationAndManagmentBtn.removeAttribute('disabled')
                    }) 
                }
            })  
        }

        if (surgeryBtn) {
            surgeryModal.show()
        }

        if (fileBtn) {
            fileModal.show()
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

    // tasks to run when closing review consultation modal
    consultationReviewModal._element.addEventListener('hide.bs.modal', function(event) {
        consultationReviewDiv.innerHTML = ''
        userRegularPatientsVisitTable.draw()
        userAncPatientsVisitTable ? userAncPatientsVisitTable.draw() : ''
        allPatientsVisitTable ? allPatientsVisitTable.draw() : ''
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

function openModals(modal, button, {id, visitId, ...data}) {
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
