import { Modal, Collapse, Toast } from "bootstrap"
import * as ECT from "@whoicd/icd11ect"
import "@whoicd/icd11ect/style.css"
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, removeAttributeLoop, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, dispatchEvent, clearValidationErrors } from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import http from "./http";
import { getAllPatientsVisitTable, getWaitingTable } from "./tables"
import jQuery, { error } from "jquery";
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
    const newConsultationModal              = new Modal(document.getElementById('newConsultationModal'))
    const newAncConsultationModal           = new Modal(document.getElementById('newAncConsultationModal'))
    const consultationReviewModal           = new Modal(document.getElementById('consultationReviewModal'))
    const surgeryModal                      = new Modal(document.getElementById('surgeryModal'))
    const fileModal                         = new Modal(document.getElementById('fileModal'))
    const newReviewModal                    = new Modal(document.getElementById('newReviewModal'))
    const specialistConsultationModal       = new Modal(document.getElementById('specialistConsultationModal'))    

    const newConsultationBtn                = document.querySelectorAll('#newConsultationBtn')
    const reviewPatientbtn                  = consultationReviewModal._element.querySelector('#reviewPatientBtn')
    const specialistConsultationbtn         = consultationReviewModal._element.querySelector('#specialistConsultationBtn')
    
    const consultationReviewDiv             = document.querySelector('#consultationReviewDiv')

    const ItemInput                         = document.querySelectorAll('#item')

    const investigationAndManagmentDiv      = document.querySelectorAll('#investigationAndManagementDiv')
    const knownClinicalInfoDiv              = document.querySelectorAll('#knownClinicalInfoDiv')
    const addInvestigationAndManagmentBtn   = document.querySelectorAll('#addInvestigationAndManagmentBtn')
    const updateKnownClinicalInfoBtn        = document.querySelectorAll('#updateKnownClinicalInfoBtn')
    const addVitalsignsDiv                  = document.querySelectorAll('#addVitalsignsDiv')
    const addVitalsignsBtn                  = document.querySelectorAll('#addVitalsignsBtn')
    const consultationDiv                   = document.querySelectorAll('#consultationDiv')
    const saveConsultationBtn               = document.querySelectorAll('#saveConsultationBtn')

    const waitingBtn                    = document.querySelector('#waitingBtn')

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

            http.get(`/consultation/consultations/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        let iteration = 0
                        let count = 0

                        const consultations = response.data.consultations.data
                        const patientBio = response.data.bio

                        openModals(consultationReviewModal, consultationReviewDiv, patientBio)

                        consultations.forEach(line => {
                            iteration++
                            
                            iteration > 1 ? count++ : ''
                
                            if (patientType === 'ANC') {
                                consultationReviewDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations, line)
                            } else {
                
                                consultationReviewDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations, line)
                            }
                             
                        })

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

    // Show waiting table
    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
        allPatientsVisitTable.draw()
    })

    // manipulating all known clinical info div
    updateKnownClinicalInfoBtn.forEach(updateBtn => {
        updateBtn.addEventListener('click', function () {
            knownClinicalInfoDiv.forEach(div => {
                if (div.dataset.div === updateBtn.dataset.btn) {
                    console.log(div)
                    toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled', '')

                    updateBtn.textContent === "Done" ? updateBtn.innerHTML = `Update` : updateBtn.textContent = "Done"

                    if (updateBtn.textContent === 'Update'){
                        const patient = updateBtn.dataset.id
                        http.patch(`/patients/knownclinicalinfo/${patient}`, {...getDivData(div)}, {"html": div})
                        .then((response) => {
                            new Toast(div.querySelector('#knownClinicalInfoToast')).show()
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
                   console.log(getDivData(div))
                }
            })
        })
    })

    // getting data from all consultation divs
    saveConsultationBtn.forEach(saveBtn => {
        saveBtn.addEventListener('click', function () {
            consultationDiv.forEach(div => {
                if (div.dataset.div === saveBtn.dataset.btn) {
                    const visitId = saveBtn.getAttribute('data-id')

                    const investigationDiv = div.parentElement.querySelector('.investigationAndManagementDiv')

                    saveBtn.setAttribute('disabled', 'disabled')
                    let data = {...getDivData(div), visitId}

                    http.post('/consultation', {...data}, {"html": div})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled')
                    
                            saveBtn.textContent === 'Saved' ? saveBtn.textContent = `Save` : saveBtn.textContent = 'Saved'
                            investigationDiv.classList.remove('d-none')
                            location.href = '#'+investigationDiv.id

                            new Toast(div.querySelector('#saveConsultationToast')).show()
                            
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

    //adding investigation and management on all divs
    addInvestigationAndManagmentBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            investigationAndManagmentDiv.forEach(div => {
                if (addBtn.dataset.btn === div.dataset.div) {
                    console.log(getDivData(div))
                    clearDivValues(div)
                }
            })
        })
    })


    // tasks to run when closing new consultation modal
    newConsultationModal._element.addEventListener('hide.bs.modal', function(event) {
        clearDivValues(newConsultationModal._element.querySelector('.investigationAndManagementDiv'))
        clearDivValues(newConsultationModal._element.querySelector('#consultationDiv'))
        clearValidationErrors(newConsultationModal._element.querySelector('#consultationDiv'))
        newConsultationModal._element.querySelector('#saveConsultationBtn').innerHTML = `Save`
        newConsultationModal._element.querySelector('#saveConsultationBtn').removeAttribute('disabled')
        newConsultationModal._element.querySelector('.investigationAndManagementDiv').classList.add('d-none')
        newConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(newConsultationModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < newConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            newConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
        waitingTable.draw()
    })

    // REVIEW CONSULTATION MODAL CODE

    // open review patient modal
    reviewPatientbtn.addEventListener('click', function () {
        reviewPatientbtn.setAttribute('disabled', 'disabled')
            const visitId       = reviewPatientbtn.getAttribute('data-id')
            //const patientId     = reviewPatientbtn.getAttribute('data-patientId')
            const patientType   = reviewPatientbtn.getAttribute('data-patientType')
    
            http.post(`/visits/consult/${ visitId }`, {patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        if (patientType === 'ANC'){
                            openModals(newAncConsultationModal, newAncConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                        } else{
                            openModals(newReviewModal, newReviewModal._element.querySelector('#saveConsultationBtn'), response.data)
                        }
                        allPatientsVisitTable.draw()
                    }
                    reviewPatientbtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    reviewPatientbtn.removeAttribute('disabled')
                    alert(error)
                })
        consultationReviewModal.hide()
        // newReviewModal.show()
    })

    // tasks to run when closing new review modal 
    newReviewModal._element.addEventListener('hide.bs.modal', function (event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        clearDivValues(newReviewModal._element.querySelector('.investigationAndManagementDiv'))
        clearDivValues(newReviewModal._element.querySelector('#consultationDiv'))
        newReviewModal._element.querySelector('#saveConsultationBtn').innerHTML = `Save`
        newReviewModal._element.querySelector('#saveConsultationBtn').removeAttribute('disabled')
        newReviewModal._element.querySelector('.investigationAndManagementDiv').classList.add('d-none')
        newReviewModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(newReviewModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < newReviewModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            newReviewModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // open specialist consultation modal
    specialistConsultationbtn.addEventListener('click', function () {
        consultationReviewModal.hide()
        specialistConsultationModal.show()
    })

    // tasks to run when closing specialist consultation modal
    specialistConsultationModal._element.addEventListener('hide.bs.modal', function (event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        clearDivValues(specialistConsultationModal._element.querySelector('.investigationAndManagementDiv'))
        clearDivValues(specialistConsultationModal._element.querySelector('#consultationDiv'))
        specialistConsultationModal._element.querySelector('#saveConsultationBtn').innerHTML = `Save`
        specialistConsultationModal._element.querySelector('#saveConsultationBtn').removeAttribute('disabled')
        specialistConsultationModal._element.querySelector('.investigationAndManagementDiv').classList.add('d-none')
        specialistConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(specialistConsultationModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < specialistConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            specialistConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // tasks to run when closing Anc consultation modal consultation modal
    newAncConsultationModal._element.addEventListener('hide.bs.modal', function (event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        clearDivValues(newAncConsultationModal._element.querySelector('.investigationAndManagementDiv'))
        clearDivValues(newAncConsultationModal._element.querySelector('#consultationDiv'))
        newAncConsultationModal._element.querySelector('#saveConsultationBtn').innerHTML = `Save`
        newAncConsultationModal._element.querySelector('#saveConsultationBtn').removeAttribute('disabled')
        newAncConsultationModal._element.querySelector('.investigationAndManagementDiv').classList.add('d-none')
        newAncConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(newAncConsultationModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < newAncConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            newAncConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // All consultation item inputs
    ItemInput.forEach(input => {
        getItemsFromInput(input, items)
    })

    // review consultation loops
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteConsultationBtn')
        const updateInvestigationAndManagmentBtn    = event.target.closest('.updateInvestigationAndManagmentBtn')
        const updateInvestigationAndManagmentDiv    = document.querySelectorAll('.investigationAndManagmentDiv')
        const addInvestigationAndManagmentBtn       = event.target.closest('#addInvestigationAndManagmentBtn')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')
        const fileBtn                               = event.target.closest('#fileBtn')
        const deliveryBtn                           = event.target.closest('#deliveryBtn')
        const collapseBtn                           = event.target.closest('.collapseBtn')

        if (collapseBtn) {
            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
            }
            setTimeout(goto, 300)
        }

        if (deleteConsultationBtn) {
            if (confirm('If you delete this consultation you cannot get it back! Are you sure you want to delete?')) {

            }
        }

        if (updateInvestigationAndManagmentBtn) {
            updateInvestigationAndManagmentDiv.forEach(div => {

                if (div.dataset.div === updateInvestigationAndManagmentBtn.dataset.btn) {
                    div.classList.toggle('d-none')
                    getItemsFromInput(div.querySelector('#item'), items)
                }
                
            })
        }

        if (addInvestigationAndManagmentBtn) {
            updateInvestigationAndManagmentDiv.forEach(div => {
                if (div.dataset.div === addInvestigationAndManagmentBtn.dataset.btn) {
                console.log(getDivData(div))
                clearDivValues(div)
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
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        consultationReviewDiv.innerHTML = ''
        
        document.querySelectorAll('#collapseReviewDiv').forEach(el => {
            let collapseable = new Collapse(el, {toggle: false})
            collapseable.hide()
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
                            openModals(newAncConsultationModal, newAncConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                        } else{
                            openModals(newConsultationModal, newConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                        }
                        waitingTable.draw()
                    }
                    consultBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultBtn.removeAttribute('disabled')
                    alert(error)
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
})



function getItemsFromInput(input, data) {
    input.addEventListener('keyup', function() {
        let records = data.filter(d => d.name.toLocaleLowerCase().includes(input.value.toLocaleLowerCase()) ? d : '')
        displayItemsList(input.parentNode, records)
    })
}

function displayItemsList(div, data) {

    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', 'itemsOption')
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)

        let previousItems = div.querySelectorAll('#itemsOption')
            let optionsList = []
            previousItems.forEach(node => {
               optionsList.push(node.dataset.id)
            })
            div.querySelector('#item').setAttribute('list', 'itemsList')
            div.querySelector('datalist').setAttribute('id', 'itemsList')
            !optionsList.includes(option.dataset.id) ? div.querySelector('#itemsList').appendChild(option) : ''
        })
}

function openModals(modal, button, {id, visitId, ...data}) {
    for (let name in data) {
        console.log(data[name])
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        
        nameInput.value = data[name]
    }

    modal._element.querySelector('#updateKnownClinicalInfoBtn').setAttribute('data-id', id)

    if (modal._element.id == 'newConsultationModal') {
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    }
    
    if (modal._element.id !== 'consultationReviewModal') {
        button.setAttribute('data-id', visitId)
        modal.show()
    }
}
