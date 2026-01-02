import { Modal, Toast } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, clearValidationErrors, displayItemsList, getDatalistOptionId, getDivData, handleValidationErrors, openModals, populatePatientSponsor } from "./helpers"
import { getLinkToVisitsTable, getWalkinsBillPos, getWalkInsTable } from "./tables/walkInTables";
import html2pdf from "html2pdf.js";

window.addEventListener('DOMContentLoaded', function () {
    const newWalkInModal            = new Modal(document.getElementById('newWalkInModal'))
    const updateWalkInModal         = new Modal(document.getElementById('updateWalkInModal'))
    const walkInPrescriptionsModal  = new Modal(document.getElementById('walkInPrescriptionsModal'))
    const addResultModal            = new Modal(document.getElementById('addResultModal'))
    const updateResultModal         = new Modal(document.getElementById('updateResultModal'))
    const payWalkInModal            = new Modal(document.getElementById('payWalkInModal'))
    const labResultModal            = new Modal(document.getElementById('labResultModal'))
    const posBillModal              = new Modal(document.getElementById('posBillModal'))
    const linkToVisitModal          = new Modal(document.getElementById('linkToVisitModal'))

    const listOfWalkInsTab          = document.querySelector('#nav-listOfWalkIns-tab')
    const addResultDiv              = addResultModal._element.querySelector('#resultDiv')
    const updateResultDiv           = updateResultModal._element.querySelector('#resultDiv')

    const newWalkInBtn              = document.getElementById('newWalkInBtn')
    const createWalkInBtn           = document.getElementById('createWalkInBtn')
    const saveWalkInBtn             = document.getElementById('saveWalkInBtn')
    const requestInput              = walkInPrescriptionsModal._element.querySelector('#request')
    const requestPrescriptionBtn    = walkInPrescriptionsModal._element.querySelector('#requestPrescriptionBtn')
    const createResultBtn           = addResultModal._element.querySelector('#createResultBtn')
    const saveResultBtn             = updateResultModal._element.querySelector('#saveResultBtn')
    const payBtn                    = payWalkInModal._element.querySelector('.payBtn')
    const downloadResultBtn         = labResultModal._element.querySelector('#downloadResultBtn')
    const resultDate                = labResultModal._element.querySelector('#resultDate')
    const addressDiv                = labResultModal._element.querySelector('.addressDiv')
    const testListDiv               = labResultModal._element.querySelector('.testListDiv')
    const multipleTestsListDiv      = labResultModal._element.querySelector('.multipleTestsListDiv')
    const signedByDiv               = labResultModal._element.querySelector('.signedByDiv')

    const changePosBillSpan             = posBillModal._element.querySelector('.changePosBill')
    const downloadPosBillSummaryBtn     = posBillModal._element.querySelector('#downloadPosBillSummaryBtn')
    const posBillSummaryBody            = posBillModal._element.querySelector('.posBillSummaryBody')

    const walkInsTable          = getWalkInsTable('walkInsTable')

    listOfWalkInsTab.addEventListener('click', function() {walkInsTable.draw()})

    newWalkInBtn.addEventListener('click', function() {
        newWalkInModal.show()
    })

    document.querySelector('#walkInsTable').addEventListener('click', function (event) {
        const addPrescriptionBtn  = event.target.closest('.addPrescriptionBtn')
        const editBtn           = event.target.closest('.updateBtn')
        const deleteBtn         = event.target.closest('.deleteBtn')
        const addResultBtn      = event.target.closest('#addResultBtn')
        const updateResultBtn   = event.target.closest('#updateResultBtn')
        const printThisBtn      = event.target.closest('#printThisBtn')
        const printAllBtn       = event.target.closest('#printAllBtn')
        const deleteResultBtn   = event.target.closest('.deleteResultBtn')
        const deleteRequestBtn  = event.target.closest('.deleteRequestBtn')
        const payWalkInBtn      = event.target.closest('.payWalkInBtn')
        const linkBtn           = event.target.closest('.linkBtn')
        const unLinkBtn         = event.target.closest('.unLinkBtn')
        const deletePaymentBtn = event.target.closest('.deletePaymentBtn')
        const softDeletePaymentBtn = event.target.closest('.softDeletePaymentBtn')
        const posWalkinBillBtn= event.target.closest('.posWalkinBillBtn')

        if (addPrescriptionBtn){
            requestPrescriptionBtn.setAttribute('data-walkinid', addPrescriptionBtn.dataset.id)
            walkInPrescriptionsModal.show()
        }

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const walkInId = editBtn.getAttribute('data-id')
            http.get(`/walkins/${ walkInId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateWalkInModal, saveWalkInBtn, response.data.data)
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
            if (confirm('Are you sure you want to delete this WalkIn?')) {
                const walkinId = deleteBtn.getAttribute('data-id')
                http.delete(`/walkins/${walkinId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            walkInsTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
            
        }

        if (addResultBtn) {
            createResultBtn.setAttribute('data-id', addResultBtn.getAttribute('data-id'))
            addResultModal._element.querySelector('#name').value = addResultBtn.getAttribute('data-name')
            addResultModal._element.querySelector('#request').value = addResultBtn.getAttribute('data-request')
            addResultModal.show()
        }

        if (updateResultBtn) {
            const prescriptionId = updateResultBtn.getAttribute('data-id')
            saveResultBtn.setAttribute('data-table', updateResultBtn.getAttribute('data-table'))
            updateResultModal._element.querySelector('#name').value = updateResultBtn.getAttribute('data-name')
            updateResultModal._element.querySelector('#request').value = updateResultBtn.getAttribute('data-request')
            http.get(`/investigations/${prescriptionId}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    openModals(updateResultModal, saveResultBtn, response.data.data)
                    updateResultModal._element.querySelector('#result').innerHTML = response.data.data?.result ?? ''
                }
            })
            .catch((error) => {
                alert(error)
            })
        }

        if (printThisBtn) {
            const prescriptionId = printThisBtn.getAttribute('data-id')
            labResultModal._element.querySelector('#test').innerHTML = printThisBtn.getAttribute('data-request')
            labResultModal._element.querySelector('#patientsId').innerHTML = printThisBtn.getAttribute('data-name')
            http.get(`/investigations/${prescriptionId}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    labResultModal._element.querySelector('#result').innerHTML = response.data.data?.result ?? ''
                }
            })
            resultDate.innerHTML = printThisBtn.getAttribute('data-resultdate')
            labResultModal._element.querySelector('#StaffFullName').innerHTML = printThisBtn.getAttribute('data-resultby')
            labResultModal.show()
        }

        if (printAllBtn){
            const id = printAllBtn.getAttribute('data-id')
            labResultModal._element.querySelector('#patientsId').innerHTML = printAllBtn.getAttribute('data-name')
            resultDate.innerHTML = new Date().toLocaleDateString('en-GB')
            http.get(`/walkins/printall/${id}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    openLabResultModal(labResultModal, labResultModal._element.querySelector('.multipleTestsListDiv'), response.data.tests.data)
                }
                printAllBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                alert(error)
                printAllBtn.removeAttribute('disabled')
            })

        }

        if (deleteResultBtn){
            deleteResultBtn.setAttribute('disabled', 'disabled')
            const prescriptionTableId = deleteResultBtn.getAttribute('data-table')
            if (confirm('Are you sure you want to delete this result?')) {
                const prescriptionId = deleteResultBtn.getAttribute('data-id')
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

        if (deleteRequestBtn){
            deleteRequestBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Request?')) {
                const id = deleteRequestBtn.getAttribute('data-id')
                http.delete(`/prescription/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            walkInsTable.draw()
                        }
                        deleteRequestBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteRequestBtn.removeAttribute('disabled')
                    })
            }      
        } 

        if (payWalkInBtn){
            payWalkInModal._element.querySelector('.payBtn').setAttribute('data-id', payWalkInBtn.getAttribute('data-id'))
            payWalkInModal.show()
        }

        if (deletePaymentBtn){
            const id = deletePaymentBtn.getAttribute('data-id')
            
            if (confirm('Are you sure you want to delete this payment?')) {
                deletePaymentBtn.setAttribute('disabled', 'disabled')
                http.delete(`/billing/payment/delete/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            walkInsTable.draw()
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

        if (posWalkinBillBtn){
                posBillModal.show()
                setTimeout(()=>{
                const walkInId   = posWalkinBillBtn.getAttribute('data-id')
                posBillModal._element.querySelector('.patient').innerHTML      = posWalkinBillBtn.getAttribute('data-name')
                posBillModal._element.querySelector('.billingStaff').innerHTML = posWalkinBillBtn.getAttribute('data-staff')
                changePosBillSpan.setAttribute('data-walkinid', walkInId)
                getWalkinsBillPos('posBillTable', walkInId, posBillModal._element, 'category', true)
                }
                , 2000)
            }
        
        if (linkBtn){
                const walkInId = linkBtn.getAttribute('data-id');
                linkToVisitModal._element.querySelector('#name').value = linkBtn.getAttribute('data-name');
                linkToVisitModal._element.querySelector('#phone').value = linkBtn.getAttribute('data-phone');
                linkToVisitModal._element.querySelector('#walkinId').setAttribute('data-walkinid', walkInId);
                linkToVisitModal.show()
                setTimeout(() => {
                    getLinkToVisitsTable('#linkToVisitTable');
                }, 500)
            }

        if (unLinkBtn){
            if (confirm('Are you sure you want to unlink Walkin requests with a visit?')){

                unLinkBtn.setAttribute('disabled', 'disabled');

                const walkinId = unLinkBtn.getAttribute('data-id');
                http.post(`/walkins/unlink/${walkinId}`, {}, {"html": unLinkBtn})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            walkInsTable.draw()
                        }
                        unLinkBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        unLinkBtn.removeAttribute('disabled')
                        alert(error.response.data.data.message)
                    })
            }
        }
        
    })

    document.querySelector('#linkToVisitTable').addEventListener('click', function (event) {
        const linkWalkinToVisitBtn  = event.target.closest('.linkWalkinToVisitBtn');

        if (linkWalkinToVisitBtn){
            if (+linkWalkinToVisitBtn.getAttribute('data-closed')){
                alert('Cannot link to a closed visit');
                return;
            }
            if (confirm('Have you cross-checked the names to be sure that they match before linking to this visit?')){


                linkWalkinToVisitBtn.setAttribute('disabled', 'disabled');

                const visitId = linkWalkinToVisitBtn.getAttribute('data-id');
                const walkinId = linkToVisitModal._element.querySelector('#walkinId').getAttribute('data-walkinid')
                http.post(`/walkins/link/${walkinId}/${visitId}`, {}, {"html": newWalkInModal._element})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        linkToVisitModal.hide()
                        walkInsTable.draw()
                    }
                    linkWalkinToVisitBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    linkWalkinToVisitBtn.removeAttribute('disabled')
                    alert(error.response.data.data.message)
                })

            }
        }
    });

    linkToVisitModal._element.addEventListener('hide.bs.modal', function () {
            if ($.fn.DataTable.isDataTable('#linkToVisitTable')){
                $('#linkToVisitTable').dataTable().fnDestroy()
                }
        })

    changePosBillSpan.addEventListener('click', function () {
        const walkInId = changePosBillSpan.getAttribute('data-walkinid')

        if ($.fn.DataTable.isDataTable('#posBillTable')) {
            $('#posBillTable').dataTable().fnDestroy()
        }

        if (changePosBillSpan.innerHTML == 'Summary'){
            changePosBillSpan.innerHTML = 'Details'
            getWalkinsBillPos('posBillTable', walkInId, posBillModal._element, 'sub_category', true)
        } else {
            changePosBillSpan.innerHTML = 'Summary'
            getWalkinsBillPos('posBillTable', walkInId, posBillModal._element, 'category', true)
        }
    })

    createWalkInBtn.addEventListener('click', function () {
        createWalkInBtn.setAttribute('disabled', 'disabled')
        http.post('/walkins', getDivData(newWalkInModal._element), {"html": newWalkInModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newWalkInModal.hide()
                clearDivValues(newWalkInModal._element)
                walkInsTable.draw()
            }
            createWalkInBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createWalkInBtn.removeAttribute('disabled')
            alert(error.response.data.data.message)
        })
    })

    saveWalkInBtn.addEventListener('click', function (event) {
        const thirdPartyId = event.currentTarget.getAttribute('data-id')
        saveWalkInBtn.setAttribute('disabled', 'disabled')
        http.patch(`/walkins/${thirdPartyId}`, getDivData(updateWalkInModal._element), {"html": updateWalkInModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateWalkInModal.hide()
                walkInsTable.draw()
            }
            saveWalkInBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveWalkInBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })

    requestInput.addEventListener('input', function () {
            const datalistEl    = walkInPrescriptionsModal._element.querySelector(`#requestList`)
                if (requestInput.value < 2) {
                datalistEl.innerHTML = ''
                }
                if (requestInput.value.length > 2) {
                    http.get(`/walkins/list/requests`, {params: {resource: requestInput.value}}).then((response) => {
                        displayItemsList(datalistEl, response.data, 'requestOption')
                    })
                }
        })
    
    requestPrescriptionBtn.addEventListener('click', function () {
        requestPrescriptionBtn.setAttribute('disabled', 'disabled')
        const requestId    =  getDatalistOptionId(walkInPrescriptionsModal._element, requestInput, walkInPrescriptionsModal._element.querySelector(`#requestList`))
        const walkInId     =  requestPrescriptionBtn.dataset?.walkinid
        let data = {...getDivData(walkInPrescriptionsModal._element), walkInId, resource:requestId}
        if (!requestId) {
            clearValidationErrors(walkInPrescriptionsModal._element)
            const message = {"request": ["Please pick a request from the list"]}               
            handleValidationErrors(message, walkInPrescriptionsModal._element)
            requestPrescriptionBtn.removeAttribute('disabled')
            return
        } else {clearValidationErrors(walkInPrescriptionsModal._element)}
        http.post(`/prescription/${requestId}`, data, {"html": walkInPrescriptionsModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(walkInPrescriptionsModal._element.querySelector('.valuesDiv'))
                clearValidationErrors(walkInPrescriptionsModal._element)
                walkInsTable ? walkInsTable.draw() : ''
            }
            requestPrescriptionBtn.removeAttribute('disabled')
            walkInPrescriptionsModal.hide()
        })
        .catch((error) => {
            console.log(error)
            requestPrescriptionBtn.removeAttribute('disabled')
        })
    })

    createResultBtn.addEventListener('click', function () {
        const prescriptionId = createResultBtn.getAttribute('data-id')
        const investigationTableId = createResultBtn.getAttribute('data-table')
        createResultBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(addResultDiv), prescriptionId, result: addResultDiv.querySelector('#result').innerHTML }

        http.patch(`/investigations/create/${prescriptionId}`, { ...data }, { "html": addResultDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(addResultDiv)
                addResultDiv.querySelector('#result').innerHTML = ''
                clearValidationErrors(addResultDiv)
                addResultModal.hide()
                walkInsTable.draw()
            }
            createResultBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            console.log(error)
            createResultBtn.removeAttribute('disabled')
        })
    })

    saveResultBtn.addEventListener('click', function () {
        const prescriptionId = saveResultBtn.getAttribute('data-id')
        const investigationTableId = saveResultBtn.getAttribute('data-table')
        saveResultBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(updateResultDiv), prescriptionId, result: updateResultDiv.querySelector('#result').innerHTML }

        http.patch(`/investigations/update/${prescriptionId}`, { ...data }, { "html": updateResultDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(updateResultDiv)
                updateResultDiv.querySelector('#result').innerHTML = ''
                walkInsTable.draw()
                clearValidationErrors(updateResultDiv)
            }
            saveResultBtn.removeAttribute('disabled')
            updateResultModal.hide()
        })
        .catch((error) => {
            console.log(error)
            saveResultBtn.removeAttribute('disabled')
        })
    })

    payBtn.addEventListener('click', function () {
        payBtn.setAttribute('disabled', 'disabled')

        const walkInId          = payBtn.getAttribute('data-id')
        const paymentDetailsDiv = document.querySelector('.paymentDetailsDiv')
        let data = {...getDivData(paymentDetailsDiv), walkInId}

        http.post('/billing/pay', {...data}, {'html': paymentDetailsDiv})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                new Toast(paymentDetailsDiv.querySelector('#savePaymentToast'), {delay:2000}).show()
                walkInsTable.draw()
                payWalkInModal.hide()
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

    resultDate.addEventListener('click', () => {
            addressDiv.classList.toggle('d-none');
        })
    
    downloadResultBtn.addEventListener('click', function () {
        const patientFullName = labResultModal._element.querySelector('#patientsId').innerHTML
        const resultModalBody = labResultModal._element.querySelector('.resultModalBody')

        var opt = {
        margin:       0.5,
        filename:     patientFullName + `'s result.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(resultModalBody).save()
    })

    downloadPosBillSummaryBtn.addEventListener('click', function () {
        const walkIn = posBillSummaryBody.querySelector('.patient').innerHTML

        var opt = {
        margin:       0.3,
        filename:     walkIn + "'s Bill Posformat.pdf",
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3 , height: null},
        jsPDF:        { unit: 'mm', format: [70, 160], orientation: 'portrait' }
        };
        html2pdf().set(opt).from(posBillSummaryBody).save()
        })

    testListDiv.addEventListener('click', function () {
        testListDiv.setAttribute('contentEditable', 'true')
    })

    multipleTestsListDiv.addEventListener('click', function () {
        multipleTestsListDiv.setAttribute('contentEditable', 'true')
    })

    signedByDiv.addEventListener('click', function () {
        signedByDiv.setAttribute('contentEditable', 'true')
    })

    labResultModal._element.addEventListener('hide.bs.modal', function (){
        testListDiv.querySelector('#test').innerHTML = ''
        testListDiv.querySelector('#result').innerHTML = ''
        multipleTestsListDiv.innerHTML = ''
    })


})

function openLabResultModal(modal, div, data) {

    data.forEach(test => {
        div.innerHTML += `<div class="fw-semibold" name="test" id="test">${test.test}</div> <p class="" id="result" name="result">${test.result}</p>`
    })

    modal.show() 
}