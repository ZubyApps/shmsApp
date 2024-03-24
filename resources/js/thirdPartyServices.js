import { Modal } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, loadingSpinners, clearValidationErrors, openModals, populatePatientSponsor, displayItemsList, getDatalistOptionId, handleValidationErrors} from "./helpers"
import html2pdf  from "html2pdf.js"
import { getThirdPartiesTable, getlistOfServicesTable } from "./tables/thirdPartyTables";

window.addEventListener('DOMContentLoaded', function () {
    const newthirdPartyModal       = new Modal(document.getElementById('newthirdPartyModal'))
    const updatethirdPartyModal    = new Modal(document.getElementById('updatethirdPartyModal'))

    const listOfServicesTab        = document.querySelector('#nav-listOfServices-tab')
    const thirdPartiesTab          = document.querySelector('#nav-thirdParties-tab')

    const newThirdPartyBtn         = document.getElementById('newThirdPartyBtn')
    const createThirdPartyBtn      = document.getElementById('createThirdPartyBtn')
    const saveThirdPartyBtn        = document.getElementById('saveThirdPartyBtn')

    const listOfServicesTable      = getlistOfServicesTable('listOfServicesTable')

    let thirdPartiesTable
    listOfServicesTab.addEventListener('click', function() {listOfServicesTable.draw()})

    thirdPartiesTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#thirdPartiesTable' )){
            $('#thirdPartiesTable').dataTable().fnDraw()
        } else {
            thirdPartiesTable = getThirdPartiesTable('thirdPartiesTable')
        }
    })

    newThirdPartyBtn.addEventListener('click', function() {
        newthirdPartyModal.show()
    })

    document.querySelector('#listOfServicesTable').addEventListener('click', function (event) {
        const deleteThirPartyServiceBtn    = event.target.closest('.deleteThirPartyServiceBtn')

        if (deleteThirPartyServiceBtn){
            deleteThirPartyServiceBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Third Party Service?')) {
                const thridpartyServiceId = deleteThirPartyServiceBtn.getAttribute('data-id')
                http.delete(`/thirdpartyservices/${thridpartyServiceId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            listOfServicesTable.draw()
                        }
                        deleteThirPartyServiceBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteThirPartyServiceBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
            
        }
    })
    document.querySelector('#thirdPartiesTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const delistBtn  = event.target.closest('.delistBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const thridpartyId = editBtn.getAttribute('data-id')
            http.get(`/thirdparties/${ thridpartyId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updatethirdPartyModal, saveThirdPartyBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    editBtn.removeAttribute('disabled')
                    console.log(error.response.data.data.message)
                })
        }

        if (delistBtn) {
            delistBtn.setAttribute('disabled', 'disabled')
            const thridpartyId = delistBtn.getAttribute('data-id')
            http.post(`/thirdparties/toggle/${ thridpartyId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        thirdPartiesTable ? thirdPartiesTable.draw() : ''
                    }
                    delistBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    delistBtn.removeAttribute('disabled')
                    console.log(error.response.data.data.message)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Third Party?')) {
                const thridpartyId = deleteBtn.getAttribute('data-id')
                http.delete(`/thirdparties/${thridpartyId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            thirdPartiesTable ? thirdPartiesTable.draw() : ''
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
            
        }
    })

    createThirdPartyBtn.addEventListener('click', function () {
        console.log('this')
        createThirdPartyBtn.setAttribute('disabled', 'disabled')
        http.post('/thirdparties/', getDivData(newthirdPartyModal._element), {"html": newthirdPartyModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newthirdPartyModal.hide()
                clearDivValues(newthirdPartyModal._element)
                thirdPartiesTable ? thirdPartiesTable.draw() : ''
            }
            createThirdPartyBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createThirdPartyBtn.removeAttribute('disabled')
            alert(error.response.data.data.message)
        })
    })

    saveThirdPartyBtn.addEventListener('click', function (event) {
        const thirdPartyId = event.currentTarget.getAttribute('data-id')
        saveThirdPartyBtn.setAttribute('disabled', 'disabled')
        http.post(`/thirdparties/${thirdPartyId}`, getDivData(updatethirdPartyModal._element), {"html": updatethirdPartyModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updatethirdPartyModal.hide()
                thirdPartiesTable ? thirdPartiesTable.draw() : ''
            }
            saveThirdPartyBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveThirdPartyBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })
})