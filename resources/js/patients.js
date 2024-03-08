import { Modal } from "bootstrap"
import { getDivData, clearDivValues, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId, openModals, getPatientSponsorDatalistOptionId } from "./helpers"
import http from "./http"
import $ from 'jquery';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { getAgeAggregateTable, getAllPatientsTable, getPatientsBySponsorTable, getSexAggregateTable, getSponsorsTable, getTotalPatientsTable, getVisitsSummaryTable } from "./tables/patientsTables";


window.addEventListener('DOMContentLoaded', function(){
    const newSponsorModal                   = new Modal(document.getElementById('newSponsorModal'))
    const updateSponsorModal                = new Modal(document.getElementById('updateSponsorModal'))
    const newPatientModal                   = new Modal(document.getElementById('newPatientModal'))
    const updatePatientModal                = new Modal(document.getElementById('updatePatientModal'))
    const initiatePatientModal              = new Modal(document.getElementById('initiatePatientModal'))
    const patientsBySponsorModal              = new Modal(document.getElementById('patientsBySponsorModal'))

    const newSponsorBtn                     = document.getElementById('newSponsor')
    const createSponsorBtn                  = document.querySelector('#createSponsorBtn')
    const saveSponsorBtn                    = document.querySelector('#saveSponsorBtn')
    const newPatientBtn                     = document.getElementById('newPatient')
    const registerPatientBtn                = document.querySelector('#registerPatientBtn')
    const savePatientBtn                    = document.querySelector('#savePatientBtn')
    const confirmVisitBtn                   = document.querySelector('#confirmVisitBtn')

    const newPatientSponsorInputEl          = document.querySelector('#newPatientSponsor')
    const updatePatientSponsorInputEl       = document.querySelector('#updatePatientSponsor')

    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')
    const updatePatientSponsorDatalistEl    = document.querySelector('#updateSponsorList')

    const patientsTab                       = document.querySelector('#nav-patients-tab')
    const sponsorsTab                       = document.querySelector('#nav-sponsors-tab')
    const summariesTab                      = document.querySelector('#nav-summaries-tab')

    let sponsorsTable, totalPatientsTable, sexAggregateTable, visitsSummaryTable, patientsBySponsorTable

    const allPatientsTable = getAllPatientsTable('allPatientsTable')

    newSponsorBtn.addEventListener('click', function() {
        newSponsorModal.show()
    })

    newPatientBtn.addEventListener('click', function() {
        let date = new Date().toISOString().split('T')[0]
        newPatientModal._element.querySelector('[name="dateOfBirth"]').setAttribute('max', date)
        newPatientModal.show()
    })

    sponsorsTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#sponsorsTable' )){
            $('#sponsorsTable').dataTable().fnDraw()
        } else {
            sponsorsTable = getSponsorsTable('sponsorsTable')
        }
    })

    summariesTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#totalPatientsTable' )){
            $('#totalPatientsTable').dataTable().fnDraw()
        } else {
            totalPatientsTable = getTotalPatientsTable('totalPatientsTable')
        }
        if ($.fn.DataTable.isDataTable( '#sexAggregateTable' )){
            $('#sexAggregateTable').dataTable().fnDraw()
        } else {
            sexAggregateTable = getSexAggregateTable('sexAggregateTable')
        }
        if ($.fn.DataTable.isDataTable( '#ageAggregateTable' )){
            $('#ageAggregateTable').dataTable().fnDraw()
        } else {
            sexAggregateTable = getAgeAggregateTable('ageAggregateTable')
        }
        if ($.fn.DataTable.isDataTable( '#visitsSummaryTable' )){
            $('#visitsSummaryTable').dataTable().fnDraw()
        } else {
            visitsSummaryTable = getVisitsSummaryTable('visitsSummaryTable')
        }
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
                            sponsorsTable.draw()
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
                sponsorsTable.draw()
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
                sponsorsTable.draw()
            }
            saveSponsorBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveSponsorBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
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
            initiatePatientModal._element.querySelector('#patientId').value = initiateVisitBtn.getAttribute('data-patient')
            initiatePatientModal._element.querySelector('#confirmVisitBtn').setAttribute('data-id', initiateVisitBtn.getAttribute('data-id'))
            initiatePatientModal.show()
            initiateVisitBtn.removeAttribute('disabled')    
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

    patientsTab.addEventListener('click', function() {
        allPatientsTable.draw()
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
            // alert(error.response.data.message)
            registerPatientBtn.removeAttribute('disabled')
        })
    })

    savePatientBtn.addEventListener('click', function (event) {
        const patient = event.currentTarget.getAttribute('data-id')
        savePatientBtn.setAttribute('disabled', 'disabled')

        let sponsor = getPatientSponsorDatalistOptionId(updatePatientModal, updatePatientSponsorInputEl, updatePatientSponsorDatalistEl)
        let data = {...getDivData(updatePatientModal._element), sponsor }

        http.post(`/patients/${patient}`, {...data}, {"html": updatePatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updatePatientModal.hide()
                allPatientsTable.draw()
            }
            savePatientBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            savePatientBtn.removeAttribute('disabled')
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

    document.querySelector('#totalPatientsTable').addEventListener('click', function (event) {
        const showPatientsBtn   = event.target.closest('.showPatientsBtn')
        // const from              = reportDatesDiv.querySelector('#startDate').value
        // const to                = reportDatesDiv.querySelector('#endDate').value

        if (showPatientsBtn){
            const id = showPatientsBtn.getAttribute('data-id')
            patientsBySponsorModal._element.querySelector('#sponsor').value = showPatientsBtn.getAttribute('data-sponsor') 
            patientsBySponsorModal._element.querySelector('#category').value = showPatientsBtn.getAttribute('data-category')
            patientsBySponsorTable = getPatientsBySponsorTable('patientsBySponsorTable', id, patientsBySponsorModal)
            patientsBySponsorModal.show()
        }
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
        let date = new Date().toISOString().split('T')[0]
        modal._element.querySelector('[name="dateOfBirth"]').setAttribute('max', date)

        http.get(`/sponsorcategory/list_sponsors/${sponsorCategoryId}`).then((response) => {
            displayList(dataListEl, 'sponsorOption', response.data)
        })
    }

    button.setAttribute('data-id', id)
    modal.show()
}
