import { Modal, Collapse, Toast } from "bootstrap"
import * as ECT from "@whoicd/icd11ect"
import "@whoicd/icd11ect/style.css"
import { clearDivValues, getOrdinal, getDivData, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, clearValidationErrors, doctorsModalClosingTasks} from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import http from "./http";
import { getAllPatientsVisitTable, getWaitingTable, getVitalSignsTableByVisit, getPrescriptionTableByConsultation, getLabTableByConsultation, getTreatmentTableByConsultation } from "./tables/doctorstables"
import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

window.addEventListener('DOMContentLoaded', function () {
    const newConsultationModal              = new Modal(document.getElementById('newConsultationModal'))
    const ancConsultationModal           = new Modal(document.getElementById('ancConsultationModal'))
    const ancReviewModal                    = new Modal(document.getElementById('ancReviewModal'))
    const consultationReviewModal           = new Modal(document.getElementById('consultationReviewModal'))
    const surgeryModal                      = new Modal(document.getElementById('surgeryModal'))
    const fileModal                         = new Modal(document.getElementById('fileModal'))
    const newReviewModal                    = new Modal(document.getElementById('newReviewModal'))
    const specialistConsultationModal       = new Modal(document.getElementById('specialistConsultationModal'))    
    
    const consultationReviewDiv             = document.querySelector('#consultationReviewDiv')
    const investigationAndManagmentDiv      = document.querySelectorAll('.investigationAndManagementDiv')
    const knownClinicalInfoDiv              = document.querySelectorAll('#knownClinicalInfoDiv')
    const addVitalsignsDiv                  = document.querySelectorAll('#addVitalsignsDiv')
    const consultationDiv                   = document.querySelectorAll('#consultationDiv')
    
    const reviewPatientbtn                  = consultationReviewModal._element.querySelector('#reviewPatientBtn')
    const reviewAncPatientbtn               = consultationReviewModal._element.querySelector('#reviewAncPatientBtn')
    const specialistConsultationbtn         = consultationReviewModal._element.querySelector('#specialistConsultationBtn')
    const addInvestigationAndManagmentBtn   = document.querySelectorAll('#addInvestigationAndManagementBtn')
    const updateKnownClinicalInfoBtn        = document.querySelectorAll('#updateKnownClinicalInfoBtn')
    const addVitalsignsBtn                  = document.querySelectorAll('#addVitalsignsBtn')
    const saveConsultationBtn               = document.querySelectorAll('#saveConsultationBtn')
    const waitingBtn                        = document.querySelector('#waitingBtn')
    
    const resourceInput                     = document.querySelectorAll('#resource')

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

    //visit Table and consultations that are active
    const allPatientsVisitTable = getAllPatientsVisitTable('#allPatientsVisitTable')
    const waitingTable = getWaitingTable('#waitingTable')
    
    document.querySelector('#allPatientsVisitTable').addEventListener('click', function (event) {
        const consultationReviewBtn    = event.target.closest('.consultationReviewBtn')

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
                                consultationReviewDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations.length, line)
                            } else {
                                consultationReviewDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations.length, line)
                            }
                        })

                        getVitalSignsTableByVisit('#vitalSignsConsultationReview', visitId, consultationReviewModal)

                        consultationReviewModal.show()
                    }
                    consultationReviewBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultationReviewBtn.removeAttribute('disabled')
                    alert(error)
                    console.log(error)
                })
        }
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
                        waitingTable.draw()
                    }
                    consultBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultBtn.removeAttribute('disabled')
                    alert(error)
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
                        alert(error)
                    })
            }
            
        }
    })

    // Show waiting table
    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
        allPatientsVisitTable.draw()
    })

    // manipulating all known clinical info div
    updateKnownClinicalInfoBtn.forEach(updateBtn => {
        updateBtn.addEventListener('click', function () {
            knownClinicalInfoDiv.forEach(div => {
                console.log(updateBtn.dataset.btn, div.dataset.div)
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
                            alert(error)
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
                            
                            new Toast(div.querySelector('#saveConsultationToast'), {delay:2000}).show()
                            getPrescriptionTableByConsultation(tableId, response.data.id, modal)
                            waitingTable.draw()
                            allPatientsVisitTable.draw()
                        }
                    })
                    .catch((error) => {
                        alert(error)
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
                        console.log('it selected')
                        console.log(div)
                        if (selectedOption.getAttribute('data-cat') == 'Medication'){
                            console.log('this too')
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

    // open review patient modal
    reviewPatientbtn.addEventListener('click', function () {
        reviewPatientbtn.setAttribute('disabled', 'disabled')
            const visitId       = reviewPatientbtn.getAttribute('data-id')
            const patientType   = reviewPatientbtn.getAttribute('data-patientType')
    
            http.post(`/visits/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(newReviewModal, newReviewModal._element.querySelector('#saveConsultationBtn'), response.data)
                        getVitalSignsTableByVisit('#vitalSignsTableReview', visitId, newReviewModal)
                        allPatientsVisitTable.draw()
                    }
                    reviewPatientbtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    reviewPatientbtn.removeAttribute('disabled')
                    alert(error)
                })
        consultationReviewModal.hide()
    })

    // open specialist consultation modal
    specialistConsultationbtn.addEventListener('click', function () {
        specialistConsultationbtn.setAttribute('disabled', 'disabled')
            const visitId       = specialistConsultationbtn.getAttribute('data-id')
            const patientType   = specialistConsultationbtn.getAttribute('data-patientType')
    
            http.post(`/visits/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(specialistConsultationModal, specialistConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                        getVitalSignsTableByVisit('#vitalSignsTableSpecialist', visitId, specialistConsultationModal)
                        allPatientsVisitTable.draw()
                    }
                    specialistConsultationbtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    specialistConsultationbtn.removeAttribute('disabled')
                    alert(error)
                })
        consultationReviewModal.hide()
    })

     // open specialist consultation modal
     reviewAncPatientbtn.addEventListener('click', function () {
        reviewAncPatientbtn.setAttribute('disabled', 'disabled')
            const visitId       = reviewAncPatientbtn.getAttribute('data-id')
            const patientType   = reviewAncPatientbtn.getAttribute('data-patientType')
    
            http.post(`/visits/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(ancReviewModal, ancReviewModal._element.querySelector('#saveConsultationBtn'), response.data)
                        getVitalSignsTableByVisit('#vitalSignsTableAncReview', visitId, ancReviewModal)
                        allPatientsVisitTable.draw()
                    }
                    reviewAncPatientbtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    reviewAncPatientbtn.removeAttribute('disabled')
                    alert(error)
                })
        consultationReviewModal.hide()
    })

     // tasks to run when closing new consultation modal
     newConsultationModal._element.addEventListener('hide.bs.modal', function(event) {
        doctorsModalClosingTasks(event, newConsultationModal, textareaHeight)
     })

    // tasks to run when closing new review modal 
    newReviewModal._element.addEventListener('hide.bs.modal', function (event) {
        doctorsModalClosingTasks(event, newReviewModal, textareaHeight)
    })

    // tasks to run when closing specialist consultation modal
    specialistConsultationModal._element.addEventListener('hide.bs.modal', function (event) {
        doctorsModalClosingTasks(event, specialistConsultationModal, textareaHeight)
    })

    // tasks to run when closing Anc consultation modal consultation modal
    ancConsultationModal._element.addEventListener('hide.bs.modal', function (event) {
        doctorsModalClosingTasks(event, ancConsultationModal, textareaHeight)
    })

    // tasks to run when closing Anc review modal consultation modal
    ancReviewModal._element.addEventListener('hide.bs.modal', function (event) {
        doctorsModalClosingTasks(event, ancReviewModal, textareaHeight)
    })

    // review consultation loops
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteReviewConsultationBtn')
        const updateInvestigationAndManagmentDiv    = document.querySelectorAll('.investigationAndManagmentDiv')
        const addInvestigationAndManagmentBtn       = event.target.closest('#addInvestigationAndManagmentBtn')
        const updateResourceListBtn                 = event.target.closest('#updateResourceListBtn')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')
        const fileBtn                               = event.target.closest('#fileBtn')
        const deliveryBtn                           = event.target.closest('#deliveryBtn')
        const collapseBtn                           = event.target.closest('.collapseBtn')
        const deleteBtn                             = event.target.closest('.deleteBtn')
        const resourceInput                         = consultationReviewDiv.querySelector('.resource')

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
                getLabTableByConsultation(investigationTableId, conId, consultationReviewModal._element)
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

        if (deliveryBtn) {
            deliveryModal.show()
        }
    })

    // tasks to run when closing review consultation modal
    consultationReviewModal._element.addEventListener('hide.bs.modal', function(event) {
        consultationReviewDiv.innerHTML = ''
        
        document.querySelectorAll('#collapseReviewDiv').forEach(el => {
            let collapseable = new Collapse(el, {toggle: false})
            collapseable.hide()
        })
        allPatientsVisitTable.draw()
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

    // if (modal._element.id == 'newConsultationModal' || modal._element.id == 'ancConsultationModal') {
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    // }
    
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
