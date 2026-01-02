import { Modal, Toast } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, clearValidationErrors, displayItemsList, getDatalistOptionId, getDivData, handleValidationErrors, openModals, populatePatientSponsor } from "./helpers"
import { getWalkInsTable } from "./tables/walkInTables";
import html2pdf from "html2pdf.js";
import { getDeceasedTable } from "./tables/mortuaryServicesTables";

window.addEventListener('DOMContentLoaded', function () {
    const addDeceasedModal            = new Modal(document.getElementById('addDeceasedModal'))
    const updateDeceasedModal         = new Modal(document.getElementById('updateDeceasedModal'))
    const addBillModal                = new Modal(document.getElementById('addBillModal'))

    const payBillModal              = new Modal(document.getElementById('payBillModal'))

    const listOfDeceasedTab          = document.querySelector('#nav-listOfDeceased-tab')

    const addDeceasedBtn              = document.getElementById('addDeceasedBtn')
    const createDeceasedRecordBtn   = document.getElementById('createDeceasedRecordBtn')
    const saveDeceasedRecordBtn     = document.getElementById('saveDeceasedRecordBtn')
    const requestInput              = addBillModal._element.querySelector('#request')
    const requestBillBtn            = addBillModal._element.querySelector('#requestBillBtn')

    const payBtn                    = payBillModal._element.querySelector('.payBtn')

    const deceasedTable          = getDeceasedTable('deceasedTable')

    listOfDeceasedTab.addEventListener('click', function() {deceasedTable.draw()})

    addDeceasedBtn.addEventListener('click', function() {
        addDeceasedModal.show()
    })

    document.querySelector('#deceasedTable').addEventListener('click', function (event) {
        const editBtn           = event.target.closest('.updateBtn')
        const deleteBtn         = event.target.closest('.deleteBtn')
        const dateCollectedSpan = event.target.closest('.dateCollectedSpan')
        const deleteBillBtn     = event.target.closest('.deleteBillBtn')
        const payBillBtn        = event.target.closest('.payBillBtn')
        const addBillBtn        = event.target.closest('.addBillBtn')
        const deletePaymentBtn = event.target.closest('.deletePaymentBtn')
        const softDeletePaymentBtn = event.target.closest('.softDeletePaymentBtn')

        if (dateCollectedSpan) {
            dateCollectedSpan.classList.add('d-none')
            const dateCollectedInput = dateCollectedSpan.parentElement.querySelector('.dateCollectedInput')
            dateCollectedInput.classList.remove('d-none')
            dateCollectedInput.focus()
            const mortuaryServiceId = dateCollectedInput.getAttribute('data-id')
            dateCollectedInput.addEventListener('blur', function() {
                http.patch(`/mortuaryservices/filldate/${mortuaryServiceId}`, {dateCollected: dateCollectedInput.value})
                .then((response) => {
                    if (response.status == 200) {
                        deceasedTable.draw(false)
                    }
                })
                    .catch((error) => {
                        deceasedTable.draw(false)
                        console.log(error.response.data)                        
                    })
                })          
            }

        if (addBillBtn){
            requestBillBtn.setAttribute('data-mortuaryServiceId', addBillBtn.dataset.id)
            addBillModal.show()
        }

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const mortuaryServicesId = editBtn.getAttribute('data-id')
            http.get(`/mortuaryservices/${ mortuaryServicesId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateDeceasedModal, saveDeceasedRecordBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    editBtn.removeAttribute('disabled')
                    console.log(error.response.data.data.message)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm("Are you sure you want to delete this Deceased's Record?")) {
                const mortuaryserviceid = deleteBtn.getAttribute('data-id')
                http.delete(`/mortuaryservices/${mortuaryserviceid}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            deceasedTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
            
        }

        if (deleteBillBtn){
            deleteBillBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Item?')) {
                const id = deleteBillBtn.getAttribute('data-id')
                http.delete(`/prescription/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            deceasedTable.draw()
                        }
                        deleteBillBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteBillBtn.removeAttribute('disabled')
                    })
            }      
        } 

        if (payBillBtn){
            payBillModal._element.querySelector('.payBtn').setAttribute('data-id', payBillBtn.getAttribute('data-id'))
            payBillModal.show()
        }

        if (deletePaymentBtn){
            const id = deletePaymentBtn.getAttribute('data-id')
            
            if (confirm('Are you sure you want to delete this payment?')) {
                deletePaymentBtn.setAttribute('disabled', 'disabled')
                http.delete(`/billing/payment/delete/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            deceasedTable   .draw()
                        }
                        deletePaymentBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        if (error.response.status === 403){
                            alert(error.response.data.message); 
                        }
                        console.log(error)
                        deletePaymentBtn.removeAttribute('disabled')
                    })
            }
            
        }

        if (softDeletePaymentBtn){
            const id = softDeletePaymentBtn.getAttribute('data-id')
            const deleteReason = prompt("What's the reason for removing this payment?", '')
            if (deleteReason === null) {
                return
            }
            if (deleteReason.trim() === ''){
                alert('You must provide a reason for removing this payment!')
                return
            }
            http.delete(`/billing/payment/delete/${id}`, {params: {deleteReason}})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                           walkInsTable.draw()
                        }
                        softDeletePaymentBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                        if (error.response.status === 433){
                            alert(error.response.data.message); 
                        }
                        softDeletePaymentBtn.removeAttribute('disabled')
                    })
        }
        
    })

    createDeceasedRecordBtn.addEventListener('click', function () {
        createDeceasedRecordBtn.setAttribute('disabled', 'disabled')
        http.post('/mortuaryservices', getDivData(addDeceasedModal._element), {"html": addDeceasedModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                addDeceasedModal.hide()
                clearDivValues(addDeceasedModal._element)
                deceasedTable.draw()
            }
            createDeceasedRecordBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createDeceasedRecordBtn.removeAttribute('disabled')
            alert(error.response.data.data.message)
        })
    })

    saveDeceasedRecordBtn.addEventListener('click', function (event) {
        const mortuaryServiceId = event.currentTarget.getAttribute('data-id')
        saveDeceasedRecordBtn.setAttribute('disabled', 'disabled')
        http.patch(`/mortuaryservices/${mortuaryServiceId}`, getDivData(updateDeceasedModal._element), {"html": updateDeceasedModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateDeceasedModal.hide()
                deceasedTable.draw()
            }
            saveDeceasedRecordBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveDeceasedRecordBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })

    requestInput.addEventListener('input', function () {
            const datalistEl    = addBillModal._element.querySelector(`#requestList`)
                if (requestInput.value < 2) {
                datalistEl.innerHTML = ''
                }
                if (requestInput.value.length > 2) {
                    http.get(`/mortuaryservices/list/requests`, {params: {resource: requestInput.value}}).then((response) => {
                        displayItemsList(datalistEl, response.data, 'requestOption')
                    })
                }
        })
    
    requestBillBtn.addEventListener('click', function () {
        requestBillBtn.setAttribute('disabled', 'disabled')
        const requestId             =  getDatalistOptionId(addBillModal._element, requestInput, addBillModal._element.querySelector(`#requestList`))
        const mortuaryServiceId     =  requestBillBtn.dataset?.mortuaryserviceid
        let data = {...getDivData(addBillModal._element), mortuaryServiceId, resource:requestId}
        if (!requestId) {
            clearValidationErrors(addBillModal._element)
            const message = {"request": ["Please pick a request from the list"]}               
            handleValidationErrors(message, addBillModal._element)
            requestPrescriptionBtn.removeAttribute('disabled')
            return
        } else {clearValidationErrors(addBillModal._element)}
        http.post(`/prescription/${requestId}`, data, {"html": addBillModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(addBillModal._element.querySelector('.valuesDiv'))
                clearValidationErrors(addBillModal._element)
                deceasedTable.draw()
            }
            requestBillBtn.removeAttribute('disabled')
            addBillModal.hide()
        })
        .catch((error) => {
            console.log(error)
            requestBillBtn.removeAttribute('disabled')
        })
    })

    payBtn.addEventListener('click', function () {
        payBtn.setAttribute('disabled', 'disabled')

        const mortuaryServiceId          = payBtn.getAttribute('data-id')
        const paymentDetailsDiv = document.querySelector('.paymentDetailsDiv')
        let data = {...getDivData(paymentDetailsDiv), mortuaryServiceId}

        http.post('/billing/pay', {...data}, {'html': paymentDetailsDiv})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                new Toast(paymentDetailsDiv.querySelector('#savePaymentToast'), {delay:2000}).show()
                deceasedTable.draw()
                payBillModal.hide()
                clearDivValues(paymentDetailsDiv)
                clearValidationErrors(paymentDetailsDiv)
            }

            payBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            console.log(error)
            payBtn.removeAttribute('disabled')
        })
    })

})
