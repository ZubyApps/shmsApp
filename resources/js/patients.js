import { Modal } from "bootstrap"
import { MaskInput } from "maska"
import { getDivData, clearDivValues, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId, openModals } from "./helpers"
import http from "./http"
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


window.addEventListener('DOMContentLoaded', function(){
    const newSponsorModal           = new Modal(document.getElementById('newSponsorModal'))
    const updateSponsorModal        = new Modal(document.getElementById('updateSponsorModal'))

    const newPatientModal           = new Modal(document.getElementById('newPatientModal'))
    const updatePatientModal        = new Modal(document.getElementById('updatePatientModal'))

    const initiatePatientModal      = new Modal(document.getElementById('initiatePatientModal'))

    const newSponsorBtn             = document.getElementById('newSponsor')
    const createSponsorBtn          = document.querySelector('#createSponsorBtn')
    const saveSponsorBtn            = document.querySelector('#saveSponsorBtn')

    const newPatientBtn             = document.getElementById('newPatient')
    const registerPatientBtn        = document.querySelector('#registerPatientBtn')
    const savePatientBtn            = document.querySelector('#savePatientBtn')

    const newPatientSponsorInputEl          = document.querySelector('#newPatientSponsor')
    const updatePatientSponsorInputEl       = document.querySelector('#updatePatientSponsor')

    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')
    const updatePatientSponsorDatalistEl    = document.querySelector('#updateSponsorList')

    const confirmVisitBtn                   = document.querySelector('.confirmVisitBtn')

    newSponsorBtn.addEventListener('click', function() {
        newSponsorModal.show()
    })

    newPatientBtn.addEventListener('click', function() {

        newPatientModal.show()
    })

    updatePatientModal._element.addEventListener('show.bs.modal', function () {
        
    })

    const sponsorTable = new DataTable('#sponsorsTable', {
        serverSide: true,
        ajax:  '/sponsors/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "name"},
            {data: "phone"},
            {data: "email"},
            {data: "category"},
            {data: row => () =>{
                if (row.approval == 'false'){
                    return 'No'
                } else {
                    return 'Yes'
                }
                }},
            {data: "registrationBill"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary updateBtn" data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        </div>
                    `
                    }
                }}
        ]
    })


    document.querySelector('#sponsorsTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const sponsorId = editBtn.getAttribute('data-id')
            http.get(`/sponsors/${ sponsorId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateSponsorModal, saveSponsorBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error.response.data.data.message)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Sponsor?')) {
                const sponsorId = deleteBtn.getAttribute('data-id')
                http.delete(`/sponsors/${sponsorId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            sponsorTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
            
        }
    })

    createSponsorBtn.addEventListener('click', function () {
        createSponsorBtn.setAttribute('disabled', 'disabled')
        http.post('/sponsors', getDivData(newSponsorModal._element), {"html": newSponsorModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newSponsorModal.hide()
                clearDivValues(newSponsorModal._element)
                newSponsorModal._element.querySelector('.allSponsorInputsDiv').classList.add('d-none')
                newSponsorModal._element.querySelector('.registrationBillDiv1').classList.add('d-none')
                sponsorTable.draw()
            }
            createSponsorBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createSponsorBtn.removeAttribute('disabled')
            alert(error.response.data.data.message)
        })
    })

    saveSponsorBtn.addEventListener('click', function (event) {
        const sponsorCategoryId = event.currentTarget.getAttribute('data-id')
        saveSponsorBtn.setAttribute('disabled', 'disabled')
        http.post(`/sponsors/${sponsorCategoryId}`, getDivData(updateSponsorModal._element), {"html": updateSponsorModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateSponsorModal.hide()
                sponsorTable.draw()
            }
            saveSponsorBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveSponsorBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })

    const allPatientsTable = new DataTable('#allPatientsTable', {
        serverSide: true,
        ajax:  '/patients/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "card"},
            {data: "name"},
            {data: "phone"},
            {data: "sex"},
            {data: "age"},
            {data: "sponsor"},
            {data: "category"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary initiateVisitBtn tooltip-test" ${row.active > 0 ? 'hidden' : ''} title="initiate visit" data-id="${ row.id }">
                            <i class="bi bi-arrow-up-right-square-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary initiateVisitBtn tooltip-test" ${row.active > 0 ? 'hidden' : ''} title="initiate visit" data-id="${ row.id }">
                                <i class="bi bi-arrow-up-right-square-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    } 
                }}
        ]
    })

    document.querySelector('#allPatientsTable').addEventListener('click', function (event) {
        const updateBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')
        const initiateVisitBtn  = event.target.closest('.initiateVisitBtn')

        if (updateBtn) {
            updateBtn.setAttribute('disabled', 'disabled')
            const patientId = updateBtn.getAttribute('data-id')
            http.get(`/patients/${ patientId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openPatientModal(updatePatientModal, savePatientBtn, response.data.data)
                    }
                    updateBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    updateBtn.removeAttribute('disabled')
                })
        }

        if (initiateVisitBtn) {
            initiateVisitBtn.setAttribute('disabled', 'disabled')
            const patientId = initiateVisitBtn.getAttribute('data-id')
            http.get(`/patients/initiate/${patientId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openPatientModal(initiatePatientModal, confirmVisitBtn, response.data.data)
                        }
                        initiateVisitBtn.removeAttribute('disabled')
                    })
                    
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Patient?')) {
                const id = deleteBtn.getAttribute('data-id')
                http.delete(`/patients/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            allPatientsTable.draw()
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

    registerPatientBtn.addEventListener('click', function () {
        const sponsor = getPatientSponsorDatalistOptionId(newPatientModal, newPatientSponsorInputEl, newPatientSponsorDatalistEl)
        registerPatientBtn.setAttribute('disabled', 'disabled')
        let data = {...getDivData(newPatientModal._element), sponsor }

        http.post('/patients', {...data}, {"html": newPatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newPatientModal.hide()
                clearDivValues(newPatientModal._element)
                newPatientModal._element.querySelector('.allPatientInputsDiv').classList.add('d-none')
                newPatientModal._element.querySelector('.familyRegistrationBillOption')
                allPatientsTable.draw()
            }
            registerPatientBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            registerPatientBtn.removeAttribute('disabled')
        })
    })

    savePatientBtn.addEventListener('click', function (event) {
        const patient = event.currentTarget.getAttribute('data-id')
        saveSponsorBtn.setAttribute('disabled', 'disabled')

        let sponsor = getPatientSponsorDatalistOptionId(updatePatientModal, updatePatientSponsorInputEl, updatePatientSponsorDatalistEl)
        let data = {...getDivData(updatePatientModal._element), sponsor }

        http.post(`/patients/${patient}`, {...data}, {"html": updateSponsorModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updatePatientModal.hide()
                allPatientsTable.draw()
            }
            savePatientBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            savePatientBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })

    confirmVisitBtn.addEventListener('click', function () {

        confirmVisitBtn.setAttribute('disabled', 'disabled')
        const patientId = confirmVisitBtn.getAttribute('data-id')

        http.post('/visits', {patientId})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                initiatePatientModal.hide()
                allPatientsTable.draw()
            }
            confirmVisitBtn.removeAttribute('disabled')
        }).catch((error) => {
            confirmVisitBtn.removeAttribute('disabled')
        })
    })
    
    newSponsorModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorModal._element)
        createSponsorBtn.removeAttribute('disabled')
    })

    updateSponsorModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorModal._element)
        saveSponsorBtn.removeAttribute('disabled')
    })

    newPatientModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newPatientModal._element)
        registerPatientBtn.removeAttribute('disabled')
    })

    updatePatientModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(updatePatientModal._element)
        savePatientBtn.removeAttribute('disabled')
    })
})

function openPatientModal(modal, button, {id, sponsorId, sponsorCategoryId, ...data}) {
 
    for (let name in data) {
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)

        nameInput.value = data[name]
    }

    if (modal._element.id === 'updatePatientModal'){    
        modal._element.querySelector('#updatePatientSponsor').setAttribute('data-id', sponsorId)
        const dataListEl = modal._element.querySelector('#updateSponsorList')

        http.get(`/sponsorcategory/list_sponsors/${sponsorCategoryId}`).then((response) => {
            displayList(dataListEl, 'sponsorOption', response.data)
        })
    }

    button.setAttribute('data-id', id)
    modal.show()
}

function getPatientSponsorDatalistOptionId(modal, inputEl, datalistEl) {  
    //console.log(inputEl)  
    // if (modal._element.id === 'updatePatientModal'){
    //     return modal._element.querySelector('#updatePatientSponsor').dataset.id
    // }
    const selectedOption = datalistEl.options.namedItem(inputEl.value)
    
        if (selectedOption) {
            return selectedOption.getAttribute('data-id')
        } else {
            return ""
        }
    }