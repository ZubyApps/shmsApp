
import {Modal } from "bootstrap";
import {getOrdinal, textareaHeightAdjustment, loadingSpinners, removeDisabled, resetFocusEndofLine, getDatalistOptionId, clearValidationErrors, handleValidationErrors, clearDivValues, getDivData, displayItemsList, openModals, getDatalistOptionStock} from "./helpers"
import { getBulkRequestTable, getExpirationStockTable , getPatientsVisitByFilterTable, getPrescriptionsByConsultation, getShiftReportTable } from "./tables/pharmacyTables";
import http from "./http";
import $ from 'jquery';
import { getLabTableByConsultation, getMedicationsByFilter, getOtherPrescriptionsByFilter, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getbillingTableByVisit } from "./tables/billingTables";
import { getEmergencyTable } from "./tables/nursesTables";
$.fn.dataTable.ext.errMode = 'throw';


window.addEventListener('DOMContentLoaded', function () {
    const treatmentDetailsModal         = new Modal(document.getElementById('treatmentDetailsModal'))
    const ancTreatmentDetailsModal      = new Modal(document.getElementById('ancTreatmentDetailsModal'))
    const billingDispenseModal          = new Modal(document.getElementById('billingDispenseModal'))
    const bulkRequestModal              = new Modal(document.getElementById('bulkRequestModal'))
    // const shiftReportModal              = new Modal(document.getElementById('shiftReportModal'))
    const newShiftReportTemplateModal   = new Modal(document.getElementById('newShiftReportTemplateModal'))
    const editShiftReportTemplateModal  = new Modal(document.getElementById('editShiftReportTemplateModal'))
    const viewShiftReportTemplateModal  = new Modal(document.getElementById('viewShiftReportTemplateModal'))

    const bulkRequestBtn        = document.querySelector('#newBulkRequestBtn')
    const requestBulkBtn        = bulkRequestModal._element.querySelector('#requestBulkBtn')
    const markDoneBtn           = billingDispenseModal._element.querySelector('#markDoneBtn')
    const emergencyListBtn      = document.querySelector('#emergencyListBtn')
    const shiftReportBtn        = document.querySelector('#shiftReportBtn')
    const newPharmacyReportBtn  = document.querySelector('#newPharmacyReportBtn')
    const createShiftReportBtn  = newShiftReportTemplateModal._element.querySelector('#createShiftReportBtn')
    const saveShiftReportBtn    = editShiftReportTemplateModal._element.querySelector('#saveShiftReportBtn')

    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')

    const filterListOption          = document.querySelector('#filterList')

    const itemInput                 = bulkRequestModal._element.querySelector('#item')
    const emergencyListCount        = document.querySelector('#emergencyListCount')
    const shiftBadgeSpan            = document.querySelector('#shiftBadgeSpan')

    const [outPatientsTab, inPatientsTab, ancPatientsTab, expirationStockTab, bulkRequestsTab]  = [document.querySelector('#nav-outPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-ancPatients-tab'), document.querySelector('#nav-expirationStock-tab'), document.querySelector('#nav-bulkRequests-tab')]

    let inPatientsVisitTable, ancPatientsVisitTable, visitPrescriptionsTable, billingTable, expirationStockTable, bulkRequestsTable

    const outPatientsTable  = getPatientsVisitByFilterTable('outPatientsTable', 'Outpatient')
    const emergencyTable    = getEmergencyTable('emergencyTable', 'pharmacy')
    const pharmacyShiftReportTable = getShiftReportTable('pharmacyShiftReportTable', 'pharmacy', shiftBadgeSpan)

    emergencyListBtn.addEventListener('click', function () {emergencyTable.draw()})
    outPatientsTab.addEventListener('click', function() {outPatientsTable.draw()})
    shiftReportBtn.addEventListener('click', function () {pharmacyShiftReportTable.draw()})

    newPharmacyReportBtn.addEventListener('click', function () {
        newShiftReportTemplateModal.show()
    })

    emergencyTable.on('draw.init', function() {
        const count = emergencyTable.rows().count()
        if (count > 0 ){
            emergencyListCount.innerHTML = count
        } else {
            emergencyListCount.innerHTML = ''
        }
    })

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsTable' )){
            $('#inPatientsTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitByFilterTable('inPatientsTable', 'Inpatient')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsTable' )){
            $('#ancPatientsTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitByFilterTable('ancPatientsTable', 'ANC')
        }
    })

    expirationStockTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#expirationStockTable' )){
            $('#expirationStockTable').dataTable().fnDraw()
        } else {
            expirationStockTable = getExpirationStockTable('expirationStockTable', 'expiration')
        }
    })

    bulkRequestsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#bulkRequestsTable' )){
            $('#bulkRequestsTable').dataTable().fnDraw()
        } else {
            bulkRequestsTable = getBulkRequestTable('bulkRequestsTable', 'pharmacy')
        }
    })

    document.querySelectorAll('#outPatientsTable, #inPatientsTable, #ancPatientsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
            const billingDispenseBtn         = event.target.closest('.billingDispenseBtn')
    
            const viewer = 'hmo'

            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
    
                const [visitId, patientType, ancRegId] = [consultationDetailsBtn.getAttribute('data-id'), consultationDetailsBtn.getAttribute('data-patientType'), consultationDetailsBtn.getAttribute('data-ancregid')] 
                const isAnc = patientType === 'ANC'
                const [modal, div, displayFunction, vitalSignsTable, id, suffixId] = isAnc ? [ancTreatmentDetailsModal, ancTreatmentDiv, AncPatientReviewDetails, getAncVitalSignsTable, ancRegId, 'AncConDetails'] : [treatmentDetailsModal, regularTreatmentDiv, regularReviewDetails, getVitalSignsTableByVisit, visitId, 'ConDetails']
    
                http.get(`/consultation/consultations/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            let iteration = 0
                            let count = 0
    
                            const consultations = response.data.consultations.data
                            const patientBio = response.data.bio
    
                            openPharmacyModals(modal, div, patientBio)
    
                            consultations.forEach(line => {
                                iteration++
    
                                iteration > 1 ? count++ : ''
    
                                div.innerHTML += displayFunction(iteration, getOrdinal, count, consultations.length, line, viewer)
                            })
    
                            vitalSignsTable(`#vitalSignsTableNurses${suffixId}`, id, modal)
                            getbillingTableByVisit(`billingTable${suffixId}`, visitId, modal._element)
                            modal.show()
                        }
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        consultationDetailsBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }

            if (billingDispenseBtn) {
                const tableId = '#' + billingDispenseModal._element.querySelector('.visitPrescriptionsTable').id
                const visitId = billingDispenseBtn.getAttribute('data-id')
                billingDispenseModal._element.querySelector('#patient').value = billingDispenseBtn.getAttribute('data-patient')
                billingDispenseModal._element.querySelector('#sponsor').value = billingDispenseBtn.getAttribute('data-sponsor') +' - '+ billingDispenseBtn.getAttribute('data-sponsorcat')
                markDoneBtn.setAttribute('data-id', visitId)
    
                visitPrescriptionsTable = getPrescriptionsByConsultation(tableId, visitId, billingDispenseModal)
                billingTable = getbillingTableByVisit('billingTable1', visitId, billingDispenseModal._element)
                billingDispenseModal.show()
            }
        })
    })

    markDoneBtn.addEventListener('click', function(){
        if (confirm("Are you sure you are done with this Patient's Prescriptions?")){
            markDoneBtn.setAttribute('disabled', 'disabled')
            const visitId = markDoneBtn.getAttribute('data-id')
            http.patch(`/pharmacy/done/${visitId}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    billingDispenseModal.hide()
                }
                markDoneBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                console.log(error)
                markDoneBtn.removeAttribute('disabled')
            })
        }
    })

    filterListOption.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable( '#expirationStockTable' )){
            $('#expirationStockTable').dataTable().fnDestroy()
        }
        getExpirationStockTable('expirationStockTable', filterListOption.value)
    })

    document.querySelectorAll('#visitPrescriptionsTable, #emergencyTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const billQtySpan               = event.target.closest('.billQtySpan')
            const dispenseQtySpan           = event.target.closest('.dispenseQtySpan')
            const dispenseCommentSpan       = event.target.closest('.dispenseCommentSpan')
            const billingDispenseFieldset   = document.querySelector('#billingDispenseFieldset')
    
            if (billQtySpan){
                const prescriptionId    = billQtySpan.getAttribute('data-id')
                const stock             = +billQtySpan.getAttribute('data-stock')
                const div               = billQtySpan.parentElement
                const billQtyInput      = div.querySelector('.billQtyInput')

                if (!stock){
                    alert('Resource is out of stock, please add to stock before billing')
                } else {
                    billQtySpan.classList.add('d-none')
                    billQtyInput.classList.remove('d-none')
    
                    resetFocusEndofLine(billQtyInput)
                
                    billQtyInput.addEventListener('blur', function () {
                        if(stock - billQtyInput.value < 0) {
                            alert('This quantity is more than the available stock, please add to stock or reduce the quantity before billing')
                            resetFocusEndofLine(billQtyInput)
                            return
                        }
                        billingDispenseFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/pharmacy/bill/${prescriptionId}`, {quantity: billQtyInput.value}, {'html' : div})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                visitPrescriptionsTable ? visitPrescriptionsTable.draw() : ''
                                visitPrescriptionsTable.on('draw', removeDisabled(billingDispenseFieldset))
                                billingTable ? billingTable.draw() : ''
                            }
                        })
                        .catch((error) => {
                            if (error.response.status == 422){
                                removeDisabled(billingDispenseFieldset)
                                console.log(error)
                            } else{
                                console.log(error)
                                visitPrescriptionsTable.draw()
                                visitPrescriptionsTable.on('draw', removeDisabled(billingDispenseFieldset))
                            }
                        })
                    })
                }                
            }
    
            if (dispenseQtySpan){
                const prescriptionId    = dispenseQtySpan.getAttribute('data-id')
                const qtyBilled         = +dispenseQtySpan.getAttribute('data-qtybilled')
                const stock             = +dispenseQtySpan.getAttribute('data-stock') ; 
                const div               = dispenseQtySpan.parentElement
                const dispenseQtyInput  = div.querySelector('.dispenseQtyInput')
                dispenseQtySpan.classList.add('d-none')
                dispenseQtyInput.classList.remove('d-none')
                resetFocusEndofLine(dispenseQtyInput)
                
                dispenseQtyInput.addEventListener('blur', function () {
                    console.log( +dispenseQtyInput.value, stock)
                    if (dispenseQtyInput.value > +qtyBilled){
                        alert('Quantity to be dispensed should not be more than Quantity billed')
                        resetFocusEndofLine(dispenseQtyInput)
                    } else {
                        if(stock - dispenseQtyInput.value < 0) {
                            alert('This quantity is more than the available stock, please rebill quantity and dispense according the quantity of stock')
                            resetFocusEndofLine(dispenseQtyInput)
                            return
                        }
                        billingDispenseFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/pharmacy/dispense/${prescriptionId}`, {quantity: dispenseQtyInput.value}, {'html' : div})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if (visitPrescriptionsTable){
                                    visitPrescriptionsTable.draw()
                                    visitPrescriptionsTable.on('draw', removeDisabled(billingDispenseFieldset))
                                } 
                                emergencyTable.draw(false)
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                            if (error.response.status == 422){
                                removeDisabled(billingDispenseFieldset)
                                console.log(error)
                            } else{
                                console.log(error)
                                visitPrescriptionsTable ? visitPrescriptionsTable.draw() : ''
                                visitPrescriptionsTable.on('draw', removeDisabled(billingDispenseFieldset))
                                emergencyTable.draw(false)
                            }
                        })
                    }               
                })
            }
    
            if (dispenseCommentSpan){
                const prescriptionId        = dispenseCommentSpan.getAttribute('data-id')
                const dispenseCommentInput  = dispenseCommentSpan.parentElement.querySelector('.dispenseCommentInput')
                dispenseCommentSpan.classList.add('d-none')
                dispenseCommentInput.classList.remove('d-none')
                resetFocusEndofLine(dispenseCommentInput)
                
                dispenseCommentInput.addEventListener('blur', function () {
                    billingDispenseFieldset.setAttribute('disabled', 'disabled')
                    http.patch(`/pharmacy/dispense/comment/${prescriptionId}`, {comment: dispenseCommentInput.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            visitPrescriptionsTable ? visitPrescriptionsTable.draw(false) : ''
                            visitPrescriptionsTable ? visitPrescriptionsTable.on('draw', removeDisabled(billingDispenseFieldset)) : ''
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        visitPrescriptionsTable ? visitPrescriptionsTable.draw(false) : ''
                        visitPrescriptionsTable ? visitPrescriptionsTable.on('draw', removeDisabled(billingDispenseFieldset)) : ''
                    })
                           
                })
            }
        })
    })

    document.querySelector('#bulkRequestsTable').addEventListener('click', function (event) {
        const approveRequestBtn    = event.target.closest('.approveRequestBtn')
        const dispenseQtyBtn       = event.target.closest('.dispenseQtyBtn')
        const deleteRequestBtn     = event.target.closest('.deleteRequestBtn')

        if (approveRequestBtn){
            const bulkRequestId     = approveRequestBtn.getAttribute('data-id')
            const div               = approveRequestBtn.parentElement
            const qtyApprovedInput  = div.querySelector('.qtyApprovedInput')
            approveRequestBtn.classList.add('d-none')
            qtyApprovedInput.classList.remove('d-none')
            resetFocusEndofLine(qtyApprovedInput)
            
            qtyApprovedInput.addEventListener('blur', function () {
                http.patch(`/bulkrequests/approve/${bulkRequestId}`, {qty: qtyApprovedInput.value}, {'html' : div})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        bulkRequestsTable ?  bulkRequestsTable.draw() : ''
                    }
                })
                .catch((error) => {
                    if (error.response.status === 403){
                        alert(error.response.data.message); 
                    }
                    bulkRequestsTable ?  bulkRequestsTable.draw() : ''
                    console.log(error)
                })                
            })
        }

        if (dispenseQtyBtn){
            const bulkRequestId     = dispenseQtyBtn.getAttribute('data-id')
            const div               = dispenseQtyBtn.parentElement
            const qtyDispensedInput = div.querySelector('.qtyDispensedInput')
            const stock             = dispenseQtyBtn.getAttribute('data-stock')
            dispenseQtyBtn.classList.add('d-none')
            qtyDispensedInput.classList.remove('d-none')
            resetFocusEndofLine(qtyDispensedInput)
            
            qtyDispensedInput.addEventListener('blur', function () {
                if (stock - qtyDispensedInput.value < 0){
                    alert('This quantity is more than the available stock, please add to stock or reduce the quantity before dispensing')
                    resetFocusEndofLine(qtyDispensedInput)
                    return
                }
                http.patch(`/bulkrequests/dispense/${bulkRequestId}`, {qty: qtyDispensedInput.value}, {'html' : div})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        bulkRequestsTable ?  bulkRequestsTable.draw() : ''
                    }
                })
                .catch((error) => {
                    console.log(error)
                    bulkRequestsTable ?  bulkRequestsTable.draw() : ''
                })                
            })
        }

        if (deleteRequestBtn){
            const bulkRequestId = deleteRequestBtn.getAttribute('data-id')
            deleteRequestBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this request?')){
                http.delete(`/bulkrequests/${bulkRequestId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        bulkRequestsTable ?  bulkRequestsTable.draw() : ''
                    }
                })
                .catch((error) => {
                    if (error.response.status === 403){
                        alert(error.response.data.message); 
                    }
                    console.log(error)
                    bulkRequestsTable ?  bulkRequestsTable.draw() : ''
                })
            }
        }
    })

    billingDispenseModal._element.addEventListener('hide.bs.modal', function () {
        outPatientsTable.draw(false)
        inPatientsVisitTable ? inPatientsVisitTable.draw(false) : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
        emergencyTable.draw(false)
    })

    bulkRequestBtn.addEventListener('click', function () {
        bulkRequestModal.show()
    })

    itemInput.addEventListener('input', function () {
        const datalistEl = bulkRequestModal._element.querySelector(`#itemList`)
            if (itemInput.value < 2) {
            datalistEl.innerHTML = ''
            }
            if (itemInput.value.length > 2) {
                http.get(`/bulkrequests/list/bulk`, {params: {resource: itemInput.value, dept: itemInput.dataset.dept}}).then((response) => {
                    displayItemsList(datalistEl, response.data, 'itemOption')
                })
            }
    })

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseConsultationBtn = event.target.closest('.collapseConsultationBtn')
        const approvalBtn = event.target.closest('#approvalBtn')
        const viewer = 'hmo'

        if (collapseConsultationBtn) {
            const gotoDiv = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
            const investigationTableId      = gotoDiv.querySelector('.investigationTable').id
            const treatmentTableId          = gotoDiv.querySelector('.treatmentTable').id
            const otherPrescriptionsTableId = gotoDiv.querySelector('.otherPrescriptionsTable').id
            const conId = gotoDiv.querySelector('.investigationTable').dataset.id

            if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                $('#' + investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                $('#' + treatmentTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable('#' + otherPrescriptionsTableId)) {
                $('#' + otherPrescriptionsTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseConsultationBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "pharmacy")
                getLabTableByConsultation(investigationTableId, treatmentDetailsModal._element, viewer, conId, null)
                getMedicationsByFilter(treatmentTableId, conId, treatmentDetailsModal._element)
                getOtherPrescriptionsByFilter(otherPrescriptionsTableId, conId, treatmentDetailsModal._element, null)
            }
            setTimeout(goto, 300)
        }
    })

    requestBulkBtn.addEventListener('click', function () {
        requestBulkBtn.setAttribute('disabled', 'disabled')
        const itemId =  getDatalistOptionId(bulkRequestModal._element, itemInput, bulkRequestModal._element.querySelector(`#itemList`))
        const itemStock =  getDatalistOptionStock(bulkRequestModal._element, itemInput, bulkRequestModal._element.querySelector(`#itemList`))
        const quantity  = bulkRequestModal._element.querySelector('#quantity').value
        if (!itemId) {
            clearValidationErrors(bulkRequestModal._element)
            const message = {"item": ["Please pick an item from the list"]}               
            handleValidationErrors(message, bulkRequestModal._element)
            requestBulkBtn.removeAttribute('disabled')
            return
        } else if (itemStock - quantity < 0){
            clearValidationErrors(bulkRequestModal._element)
            const message = {"quantity": ["This quantity is more than the available stock, please reduce the quantity or add to the stock"]}               
            handleValidationErrors(message, bulkRequestModal._element)
            requestBulkBtn.removeAttribute('disabled')
            return
        } else {clearValidationErrors(bulkRequestModal._element)}
        
        http.post(`/bulkrequests/${itemId}`, getDivData(bulkRequestModal._element), {"html": bulkRequestModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(bulkRequestModal._element)
                clearValidationErrors(bulkRequestModal._element)
            }
            requestBulkBtn.removeAttribute('disabled')
            bulkRequestModal.hide()
        })
        .catch((error) => {
            console.log(error)
            requestBulkBtn.removeAttribute('disabled')
        })
    })

    bulkRequestModal._element.addEventListener('hide.bs.modal', function () {
        bulkRequestsTable ? bulkRequestsTable.draw() : ''
    })


    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal, #viewShiftReportTemplateModal ,#newShiftReportTemplateModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
            outPatientsTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            emergencyTable.draw()
            pharmacyShiftReportTable.draw()
        })
    })

    createShiftReportBtn.addEventListener('click', function() {
        createShiftReportBtn.setAttribute('disabled', 'disabled')
        http.post(`shiftreport`, {
            report: newShiftReportTemplateModal._element.querySelector('#report').value, 
            department: newShiftReportTemplateModal._element.querySelector('#department').value,
            shift:  newShiftReportTemplateModal._element.querySelector('#shift').value,
        }, 
            {'html': newShiftReportTemplateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                newShiftReportTemplateModal.hide()
                clearDivValues(newShiftReportTemplateModal._element)
                clearValidationErrors(newShiftReportTemplateModal._element)
                pharmacyShiftReportTable.draw(false)
            }
            createShiftReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            createShiftReportBtn.removeAttribute('disabled')
        })
    })

    saveShiftReportBtn.addEventListener('click', function() {
        saveShiftReportBtn.setAttribute('disabled', 'disabled')
        const id = saveShiftReportBtn.getAttribute('data-id')
        http.patch(`shiftreport/${id}`, {
            report: editShiftReportTemplateModal._element.querySelector('#report').value,
            shift:  editShiftReportTemplateModal._element.querySelector('#shift').value,
    }, {'html': editShiftReportTemplateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                editShiftReportTemplateModal.hide()
                clearValidationErrors(editShiftReportTemplateModal._element)
                pharmacyShiftReportTable.draw(false)
            }
            saveShiftReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveShiftReportBtn.removeAttribute('disabled')
        })
    })

    document.querySelectorAll('#pharmacyShiftReportTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const editShiftReportBtn   = event.target.closest('.editShiftReportBtn')
            const viewShiftReportBtn   = event.target.closest('.viewShiftReportBtn')
            const deleteShiftReportBtn = event.target.closest('.deleteShiftReportBtn')
    
            if (editShiftReportBtn) {
                editShiftReportBtn.setAttribute('disabled', 'disabled')
                http.get(`/shiftreport/${editShiftReportBtn.getAttribute('data-id')}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editShiftReportTemplateModal, saveShiftReportBtn, response.data.data)
                    }
                })
                .catch((error) => {
                    console.log(error)
                })
                setTimeout(()=>{editShiftReportBtn.removeAttribute('disabled')}, 2000)
            }

            if (viewShiftReportBtn) {
                viewShiftReportBtn.setAttribute('disabled', 'disabled')
                http.get(`/shiftreport/view/${viewShiftReportBtn.getAttribute('data-id')}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(viewShiftReportTemplateModal, saveShiftReportBtn, response.data.data)
                    }
                })
                .catch((error) => {
                    console.log(error)
                })
                setTimeout(()=>{viewShiftReportBtn.removeAttribute('disabled')}, 2000)
            }
    
            if (deleteShiftReportBtn) {
                deleteShiftReportBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this report?')) {
                    const id = deleteShiftReportBtn.getAttribute('data-id')
                    http.delete(`/shiftreport/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                pharmacyShiftReportTable.draw(false)
                            }
                            deleteShiftReportBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteShiftReportBtn.removeAttribute('disabled')
                        })
                }
            }
    
        })
    })


})

function openPharmacyModals(modal, button, { id, visitId, ancRegId, patientType, ...data }) {
    for (let name in data) {
        
        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}