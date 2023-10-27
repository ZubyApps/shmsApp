import { Modal, Collapse } from "bootstrap"
import * as ECT from "@whoicd/icd11ect"
import "@whoicd/icd11ect/style.css"
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, removeAttributeLoop, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, dispatchEvent, openModals } from "./helpers"
import { doctorsReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
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
    const newConsultationModal              = new Modal(document.getElementById('newConsultationModal'))
    const newAncConsultationModal           = new Modal(document.getElementById('newAncConsultationModal'))
    const consultationReviewModal           = new Modal(document.getElementById('consultationReviewModal'))
    const surgeryModal                      = new Modal(document.getElementById('surgeryModal'))
    const fileModal                         = new Modal(document.getElementById('fileModal'))
    const newReviewModal                    = new Modal(document.getElementById('newReviewModal'))
    const specialistConsultationModal       = new Modal(document.getElementById('specialistConsultationModal'))

    const newConsultationBtn                = document.querySelectorAll('#newConsultationBtn')
    const consultationReviewBtn             = document.querySelector('#reviewConsultationBtn')
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

    const waitingListBtn                    = document.querySelector('#waitingListBtn')

    // Auto textarea adjustment
    const textareaHeight = 90;
    textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))

    // ICD11settings
    const mySettings = { apiServerUrl: "https://icd11restapi-developer-test.azurewebsites.net" }

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

    // NEW CONSULTATION MODAL CODE 

    // show new consultation modal
    newConsultationBtn.forEach(btn => {
        btn.addEventListener('click', function () {
            console.log(btn.dataset.patienttype)
            if (btn.dataset.patienttype === 'ANC') {
                newAncConsultationModal.show() 
            } else {

                newConsultationModal.show()
            }
        })
    })

    waitingListBtn.addEventListener('click', function () {
        waitingListTable.draw()
    })

    // manipulating all known clinical info div
    updateKnownClinicalInfoBtn.forEach(updateBtn => {
        updateBtn.addEventListener('click', function () {
            knownClinicalInfoDiv.forEach(div => {
                console.log(this.dataset.btn)
                if (div.dataset.div === updateBtn.dataset.btn) {
                    toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled', '')

                    updateBtn.textContent === "Done" ?
                    updateBtn.innerHTML = `<i class="bi bi-arrow-up-circle"></i> Update` :
                    updateBtn.textContent = "Done"

                    console.log(getDivData(div))
                }
            })
        })
    })

     // manipulating all vital signs div
    addVitalsignsBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            addVitalsignsDiv.forEach(div => {
                if (div.dataset.div === addBtn.dataset.btn) {
                    if (div.classList.contains('d-none')){
                        div.classList.remove('d-none')
                    } else {
                        console.log(getDivData(div))
                        div.classList.add('d-none')
                        clearDivValues(div)
                    }
                }
            })
        })
    })

    // getting data from all consultation divs
    saveConsultationBtn.forEach(saveBtn => {
        saveBtn.addEventListener('click', function () {
            consultationDiv.forEach(div => {
                if (div.dataset.div === saveBtn.dataset.btn) {
                    console.log(getDivData(div))

                    toggleAttributeLoop(querySelectAllTags(div, ['input, select, textarea']), 'disabled')
                    
                    saveBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? 
                    saveBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : 
                    saveBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'

                    div.parentElement.querySelector('#investigationAndManagementDiv').classList.remove('d-none')
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
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        clearDivValues(newConsultationModal._element.querySelector('#investigationAndManagementDiv'))
        clearDivValues(newConsultationModal._element.querySelector('#consultationDiv'))
        newConsultationModal._element.querySelector('#saveConsultationBtn').innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        newConsultationModal._element.querySelector('#investigationAndManagementDiv').classList.add('d-none')
        newConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(newConsultationModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < newConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            newConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // REVIEW CONSULTATION MODAL CODE

    // Open review consultation modal and returning a loop of all consultations for the given pattient
    consultationReviewBtn.addEventListener('click', function () {
        let iteration = 0
        let count = 0
        consultationDetails.data.forEach(line => {
            iteration++
            
            iteration > 1 ? count++ : ''

            if (line.patientType === 'ANC') {
                consultationReviewDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultationDetails, line)
            } else {

                consultationReviewDiv.innerHTML += doctorsReviewDetails(iteration, getOrdinal, count, consultationDetails, line)
            }
             
        })

        consultationReviewModal.show()

    })

    // open review patient modal
    reviewPatientbtn.addEventListener('click', function () {
        newReviewModal.show()
    })

    // tasks to run when closing new review modal 
    newReviewModal._element.addEventListener('hide.bs.modal', function (event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        clearDivValues(newReviewModal._element.querySelector('#investigationAndManagementDiv'))
        clearDivValues(newReviewModal._element.querySelector('#consultationDiv'))
        newReviewModal._element.querySelector('#saveConsultationBtn').innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        newReviewModal._element.querySelector('#investigationAndManagementDiv').classList.add('d-none')
        newReviewModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(newReviewModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < newReviewModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            newReviewModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // open specialist consultation modal
    specialistConsultationbtn.addEventListener('click', function () {
        specialistConsultationModal.show()
    })

    // tasks to run when closing specialist consultation modal
    specialistConsultationModal._element.addEventListener('hide.bs.modal', function (event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        clearDivValues(specialistConsultationModal._element.querySelector('#investigationAndManagementDiv'))
        clearDivValues(specialistConsultationModal._element.querySelector('#consultationDiv'))
        specialistConsultationModal._element.querySelector('#saveConsultationBtn').innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        specialistConsultationModal._element.querySelector('#investigationAndManagementDiv').classList.add('d-none')
        specialistConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))

        removeAttributeLoop(querySelectAllTags(specialistConsultationModal._element.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
        for (let t = 0; t < specialistConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
            specialistConsultationModal._element.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
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
                                <button class=" btn btn-outline-primary consultBtn tooltip-test" title="consult" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                                    <i class="bi bi-clipboard2-plus-fill"></i>
                                </button>
                                <button class="ms-1 btn btn-outline-primary removeBtn tooltip-test" title="remove" data-id="${ row.id }">
                                <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-white text-primary consultBtn tooltip-test" title="consult" data-id="${ row.id }" data-patientId="${ row.patientId }" data-patientType="${ row.patientType }">
                                    ${row.doctor}
                                </button>
                            </div>`
                        }
            }},
        ]
    });

    document.querySelector('#waitingListTable').addEventListener('click', function (event) {
        const consultBtn    = event.target.closest('.consultBtn')
        const removeBtn  = event.target.closest('.removeBtn')

        if (consultBtn) {
            consultBtn.setAttribute('disabled', 'disabled')
            const visitId       = consultBtn.getAttribute('data-id')
            const patientId     = consultBtn.getAttribute('data-patientId')
            const patientType   = consultBtn.getAttribute('data-patientType')
            console.log(visitId, patientId, patientType)

            http.post(`/visits/consult/${ visitId }`, {patientId, patientType})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        if (patientType === 'ANC'){
                            openModals(newAncConsultationModal, newAncConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                        } else{
                            openModals(newConsultationModal, newConsultationModal._element.querySelector('#saveConsultationBtn'), response.data)
                        }
                        waitingListTable.draw()
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
