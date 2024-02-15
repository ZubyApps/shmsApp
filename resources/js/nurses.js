import { Offcanvas, Modal, Toast } from "bootstrap";
import { clearDivValues, clearValidationErrors, getOrdinal, loadingSpinners, getDivData, bmiCalculator, openModals, lmpCalculator, populatePatientSponsor, populateVitalsignsModal, populateDischargeModal, lmpCurrentCalculator, displayItemsList, getDatalistOptionId, handleValidationErrors, displayVisits } from "./helpers"
import $ from 'jquery';
import http from "./http";
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import { getWaitingTable, getPatientsVisitsByFilterTable, getNurseMedicationsByFilter, getMedicationChartByPrescription, getUpcomingMedicationsTable, getDeliveryNoteTable, getAncVitalSignsTable, getUpcomingNursingChartsTable, getOtherPrescriptionsByFilterNurses, getPrescriptionChartByPrescription } from "./tables/nursesTables";
import { getVitalSignsTableByVisit, getLabTableByConsultation, getSurgeryNoteTable } from "./tables/doctorstables";
import { getbillingTableByVisit } from "./tables/billingTables";
import { getBulkRequestTable } from "./tables/pharmacyTables";
import { visitDetails } from "./dynamicHTMLfiles/visits";

window.addEventListener('DOMContentLoaded', function () {
    const upcomingMedicationsCanvas         = new Offcanvas(document.getElementById('upcomingMedicationsoffcanvas'))
    const upcomingNursingChartsCanvas       = new Offcanvas(document.getElementById('upcomingNursingChartsoffcanvas'))
    const waitingListCanvas                 = new Offcanvas(document.getElementById('waitingListOffcanvas2'))

    const treatmentDetailsModal         = new Modal(document.getElementById('treatmentDetailsModal'))
    const consultationHistoryModal      = new Modal(document.getElementById('consultationHistoryModal'))
    const ancTreatmentDetailsModal      = new Modal(document.getElementById('ancTreatmentDetailsModal'))
    const newDeliveryNoteModal          = new Modal(document.getElementById('newDeliveryNoteModal'))
    const updateDeliveryNoteModal       = new Modal(document.getElementById('updateDeliveryNoteModal'))
    const viewDeliveryNoteModal         = new Modal(document.getElementById('viewDeliveryNoteModal'))
    const medicationPrescriptionsModal  = new Modal(document.getElementById('medicationPrescriptionsModal'))
    const otherPrescriptionsModal       = new Modal(document.getElementById('otherPrescriptionsModal'))
    const chartMedicationModal          = new Modal(document.getElementById('chartMedicationModal'))
    const chartPrescriptionModal        = new Modal(document.getElementById('chartPrescriptionModal'))
    const vitalsignsModal               = new Modal(document.getElementById('vitalsignsModal'))
    const ancVitalsignsModal           = new Modal(document.getElementById('ancVitalsignsModal'))
    const giveMedicationModal          = new Modal(document.getElementById('giveMedicationModal'))
    const serviceDoneModal             = new Modal(document.getElementById('serviceDoneModal'))
    const newAncRegisterationModal     = new Modal(document.getElementById('newAncRegisterationModal'))
    const updateAncRegisterationModal  = new Modal(document.getElementById('updateAncRegisterationModal'))
    const viewAncRegisterationModal    = new Modal(document.getElementById('viewAncRegisterationModal'))
    const dischargeModal               = new Modal(document.getElementById('dischargeModal'))
    const bulkRequestModal             = new Modal(document.getElementById('bulkRequestModal'))

    const visitHistoryDiv           = consultationHistoryModal._element.querySelector('#visitHistoryDiv')
    const addVitalsignsDiv          = document.querySelectorAll('#addVitalsignsDiv')
    const medicationChartDiv        = chartMedicationModal._element.querySelector('#chartMedicationDiv')
    const prescriptionChartDiv      = chartPrescriptionModal._element.querySelector('#chartPrescriptionDiv')
    const medicationChartTable      = chartMedicationModal._element.querySelector('#medicationChartTable')
    const prescriptionChartTable    = chartPrescriptionModal._element.querySelector('#prescriptionChartTable')
    const giveMedicationDiv         = giveMedicationModal._element.querySelector('#giveMedicationDiv')
    const saveServiceDoneDiv        = serviceDoneModal._element.querySelector('#saveServiceDoneDiv')
    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')

    const waitingBtn                = document.querySelector('#waitingBtn')
    const addVitalsignsBtn          = document.querySelectorAll('#addVitalsignsBtn')
    const saveMedicationChartBtn    = chartMedicationModal._element.querySelector('#saveMedicationChartBtn')
    const saveGivenMedicationBtn    = giveMedicationModal._element.querySelector('.saveGivenMedicationBtn')
    const saveServiceDoneBtn        = serviceDoneModal._element.querySelector('.saveServiceDoneBtn')
    const savePrescriptionChartBtn  = chartPrescriptionModal._element.querySelector('#savePrescriptionChartBtn')
    const createDeliveryNoteBtn     = newDeliveryNoteModal._element.querySelector('#createBtn')
    const saveDeliveryNoteBtn       = updateDeliveryNoteModal._element.querySelector('#saveBtn')
    const registerAncBtn            = newAncRegisterationModal._element.querySelector('#registerAncBtn') 
    const saveAncBtn                = updateAncRegisterationModal._element.querySelector('#saveAncBtn') 
    const deleteAncBtn              = viewAncRegisterationModal._element.querySelector('#deleteAncBtn')
    const bulkRequestBtn            = document.querySelector('#newBulkRequestBtn')
    const requestBulkBtn            = bulkRequestModal._element.querySelector('#requestBulkBtn')
    const moreHistoryBtn            = consultationHistoryModal._element.querySelector('#moreHistoryBtn')

    const itemInput                 = bulkRequestModal._element.querySelector('#item')
    const [outPatientsTab, inPatientsTab, ancPatientsTab, bulkRequestsTab]  = [document.querySelector('#nav-outPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-ancPatients-tab'), document.querySelector('#nav-bulkRequests-tab')]
    
    bmiCalculator(document.querySelectorAll('#height, .weight'))
    lmpCalculator(document.querySelectorAll('#lmp'), document.querySelectorAll('#registerationDiv'))

    let inPatientsVisitTable, ancPatientsVisitTable, bulkRequestsTable

    const outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient')
    const waitingTable = getWaitingTable('waitingTable')
    const upcomingMedicationsTable = getUpcomingMedicationsTable('upcomingMedicationsTable', upcomingMedicationsCanvas._element, 'offcanvas')
    const upcomingNursingChartsTable     = getUpcomingNursingChartsTable('upcomingNursingChartsTable', upcomingNursingChartsCanvas._element, 'offcanvas')

    outPatientsTab.addEventListener('click', function() {outPatientsVisitTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitsByFilterTable('inPatientsVisitTable', 'Inpatient')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('ancPatientsVisitTable', 'ANC')
        }
    })

    bulkRequestsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#bulkRequestsTable' )){
            $('#bulkRequestsTable').dataTable().fnDraw()
        } else {
            bulkRequestsTable = getBulkRequestTable('bulkRequestsTable', 'nurses')
        }
    })

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        outPatientsVisitTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    document.querySelectorAll('#upcomingMedicationsoffcanvas, #upcomingNursingChartsoffcanvas').forEach(canvas => {
        canvas.addEventListener('show.bs.offcanvas', function () {
            upcomingMedicationsTable.draw()
            upcomingNursingChartsTable.draw()
        })

    })

    document.querySelectorAll('#upcomingMedicationsoffcanvas, #upcomingNursingChartsoffcanvas').forEach(canvas => {
        canvas.addEventListener('hide.bs.offcanvas', function () {
            outPatientsVisitTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        })
    })

    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #waitingTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
            const vitalsignsBtn             = event.target.closest('.vitalSignsBtn, .ancVitalSignsBtn')
            const ancRegisterationBtn       = event.target.closest('.ancRegisterationBtn')
            const ancBtn                    = event.target.closest('#viewRegisterationBtn, #editRegisterationBtn')
            const dischargedBtn             = event.target.closest('.dischargedBtn')
            const historyBtn                = event.target.closest('.historyBtn')
            const viewMedicationBtn         = event.target.closest('.viewMedicationBtn')
            const viewOtherPrescriptionsBtn = event.target.closest('.viewOtherPrescriptionsBtn')
            const viewer = 'nurse'
            let [iteration, count]        = [0, 0]
    
            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
                const [visitId, patientType, ancRegId, closed] = [consultationDetailsBtn.getAttribute('data-id'), consultationDetailsBtn.getAttribute('data-patientType'), consultationDetailsBtn.getAttribute('data-ancregid'), +consultationDetailsBtn.getAttribute('data-closed')] 
                const isAnc = patientType === 'ANC'
                const [modal, div, displayFunction, vitalSignsTable, id, suffixId] = isAnc ? [ancTreatmentDetailsModal, ancTreatmentDiv, AncPatientReviewDetails, getAncVitalSignsTable, ancRegId, 'AncConDetails'] : [treatmentDetailsModal, regularTreatmentDiv, regularReviewDetails, getVitalSignsTableByVisit, visitId, 'ConDetails']
                
                closed ? modal._element.querySelector('.addVitalsignsDiv').classList.add('d-none') : modal._element.querySelector('.addVitalsignsDiv').classList.remove('d-none')
                http.get(`/consultation/consultations/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            const consultations = response.data.consultations.data
                            const patientBio    = response.data.bio
                            const lmp           = response.data.latestLmp
    
                            openNurseModals(modal, div, patientBio)
                            isAnc ? lmpCurrentCalculator(lmp.lmp, modal._element.querySelector('.lmpDetailsDiv')) : ''

                            consultations.forEach(line => {
                                iteration++
    
                                iteration > 1 ? count++ : ''
    
                                div.innerHTML += displayFunction(iteration, getOrdinal, count, consultations.length, line, viewer, false, closed)
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
    
            if (vitalsignsBtn) {
                vitalsignsBtn.setAttribute('disabled', 'disabled')
                const isAnc = vitalsignsBtn.id == 'ancVitalSignsBtn'
                const [btn, modal, id, getTable] = isAnc ? [vitalsignsBtn, ancVitalsignsModal, vitalsignsBtn.getAttribute('data-ancregid'), getAncVitalSignsTable] : [vitalsignsBtn, vitalsignsModal, vitalsignsBtn.getAttribute('data-id'), getVitalSignsTableByVisit]

                const tableId = '#' + modal._element.querySelector('.vitalsTable').id
                populateVitalsignsModal(modal, btn, id)
   
                modal.show()
                getTable(tableId, id, modal)
                setTimeout(()=>{btn.removeAttribute('disabled')}, 2000)
            }

            if (ancRegisterationBtn){

                ancRegisterationBtn.parentElement.setAttribute('disabled', 'disabled')
                newAncRegisterationModal._element.querySelector('.patient').value = ancRegisterationBtn.parentElement.dataset.patient
                newAncRegisterationModal._element.querySelector('.age').value = ancRegisterationBtn.parentElement.dataset.age
                newAncRegisterationModal._element.querySelector('.sponsor').value = ancRegisterationBtn.parentElement.dataset.sponsor
                newAncRegisterationModal._element.querySelector('#registerAncBtn').setAttribute('data-patientid', ancRegisterationBtn.parentElement.dataset.patientid)
                newAncRegisterationModal.show()
                ancRegisterationBtn.parentElement.removeAttribute('disabled')
            }
            if (viewMedicationBtn){
                viewMedicationBtn.setAttribute('disabled', 'disabled')
                const tableId = medicationPrescriptionsModal._element.querySelector('.medicationsTable').id
                const visitId = viewMedicationBtn.getAttribute('data-id')
                populatePatientSponsor(medicationPrescriptionsModal, viewMedicationBtn)
                getNurseMedicationsByFilter(tableId, null, medicationPrescriptionsModal._element, visitId)
    
                medicationPrescriptionsModal.show()
                viewMedicationBtn.removeAttribute('disabled')
            }

            if (viewOtherPrescriptionsBtn){
                viewOtherPrescriptionsBtn.setAttribute('disabled', 'disabled')
                const tableId = otherPrescriptionsModal._element.querySelector('.otherPrescriptionsTable').id
                const visitId = viewOtherPrescriptionsBtn.getAttribute('data-id')
                populatePatientSponsor(otherPrescriptionsModal, viewOtherPrescriptionsBtn)
                getOtherPrescriptionsByFilterNurses(tableId, null, otherPrescriptionsModal._element, visitId)
    
                otherPrescriptionsModal.show()
                viewOtherPrescriptionsBtn.removeAttribute('disabled')
            }   

            if (ancBtn){
                const isEdit = ancBtn.id == 'editRegisterationBtn'
                const [btn, modalBtn, modal] = isEdit ? [ancBtn, saveAncBtn, updateAncRegisterationModal] : [ancBtn, saveAncBtn, viewAncRegisterationModal]
                btn.setAttribute('disabled', 'disabled')
                deleteAncBtn.setAttribute('data-id', btn.parentElement.getAttribute('data-ancregid'))
                http.get(`/ancregisteration/${btn.parentElement.getAttribute('data-ancregid')}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(modal, modalBtn, response.data.data)
                    }
                })
                .catch((error) => {
                    alert(error)
                })
                setTimeout(()=>{btn.removeAttribute('disabled')}, 2000)
            }

            if (dischargedBtn){
                dischargedBtn.setAttribute('disabled', 'disabled')
                populateDischargeModal(dischargeModal, dischargedBtn)
                dischargeModal.show()
                setTimeout(()=>{dischargedBtn.removeAttribute('disabled')}, 1500)
            }

            if (historyBtn){
                const patientId     = historyBtn.getAttribute('data-patientid')
                const isAnc         = historyBtn.getAttribute('data-patienttype') === 'ANC'
                // populatePatientSponsor(investigationAndManagementModal, historyBtn)
                http.get(`/consultation/history/${patientId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        const visits        = response.data.visits.data
                        const patientBio    = response.data.bio
                        openNurseModals1(consultationHistoryModal, moreHistoryBtn, patientBio)
                        visits.forEach(line => {
                            iteration++
                            iteration > 1 ? count++ : ''
                            displayVisits(visitHistoryDiv, visitDetails, iteration, getOrdinal, line, viewer, isAnc)
                        })

                        consultationHistoryModal.show()
                    }
                })
            }
        })
    })

    registerAncBtn.addEventListener('click', function () {
        registerAncBtn.setAttribute('disabled', 'disabled')
        const patientId = registerAncBtn.dataset.patientid

        let data = { ...getDivData(newAncRegisterationModal._element), patientId}
        http.post('/ancregisteration', { ...data }, { "html": newAncRegisterationModal._element })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newAncRegisterationModal.hide()
                clearDivValues(newAncRegisterationModal._element)
                ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            }
            registerAncBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            registerAncBtn.removeAttribute('disabled')
        })
    })

    saveAncBtn.addEventListener('click', function () {
        saveAncBtn.setAttribute('disabled', 'disabled')
        const id = saveAncBtn.dataset.id

        http.patch(`/ancregisteration/${id}`, getDivData(updateAncRegisterationModal._element), { "html": updateAncRegisterationModal._element })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateAncRegisterationModal.hide()
                ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            }
            saveAncBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            saveAncBtn.removeAttribute('disabled')
        })
    })

    deleteAncBtn.addEventListener('click', function () {
        deleteAncBtn.setAttribute('disabled', 'disabled')
        if (confirm('Are you sure you want to delete this Information?')) {
            const id = deleteAncBtn.getAttribute('data-id')
            http.delete(`/ancregisteration/${id}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        viewAncRegisterationModal.hide()
                        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
                    }
                    deleteAncBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                    deleteAncBtn.removeAttribute('disabled')
                })
        }
    })

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal, #medicationPrescriptionsModal, #consultationHistoryModal').forEach(modal => {
            modal.addEventListener('hide.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
            visitHistoryDiv.innerHTML = ''
            outPatientsVisitTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        })
    })

    // manipulating all vital signs div
    addVitalsignsBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            addVitalsignsDiv.forEach(div => {
                if (div.dataset.div === addBtn.dataset.btn) {
                    addBtn.setAttribute('disabled', 'disabled')
                    const isAnc     = addBtn.getAttribute('data-patienttype') == 'ANC'
                    const visitId   = addBtn.getAttribute('data-id')
                    const ancRegId  = addBtn.getAttribute('data-ancregid')
                    console.log(isAnc, ancRegId)
                    const tableId   = div.parentNode.parentNode.querySelector('.vitalsTable').id
                    let data = { ...getDivData(div), visitId, ancRegId }
                    const url = isAnc ? '/ancvitalsigns' : '/vitalsigns'

                    isAnc && JSON.parse(ancRegId) == null ? alert('Register patient for ANC first') : 
                    http.post(url, { ...data }, { "html": div })
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            new Toast(div.querySelector('#vitalSignsToast'), { delay: 2000 }).show()
                            clearDivValues(div)
                            clearValidationErrors(div)
                        }
                        if ($.fn.DataTable.isDataTable('#' + tableId)) {
                            $('#' + tableId).dataTable().fnDraw()
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

    document.querySelectorAll('#vitalSignsTableNursesAncConDetails, #vitalSignsTableNursesConDetails, #ancVitalSignsTable, #vitalSignsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn = event.target.closest('.deleteBtn')

            if (deleteBtn) {
                const url  = deleteBtn.dataset.patienttype == 'ANC' ? 'ancvitalsigns' : 'vitalsigns'
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/${url}/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable('#' + table.id)) {
                                    $('#' + table.id).dataTable().fnDraw()
                                }
                            }
                            deleteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteBtn.removeAttribute('disabled')
                        })
                } deleteBtn.removeAttribute('disabled')
            }
        })
    })

    document.querySelectorAll('#medicationChartTable #nursingChartTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn = event.target.closest('.deleteBtn')

            if (deleteBtn) {
                deleteBtn.setAttribute('disabled', 'disabled')
                const treatmentTableId = saveMedicationChartBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/medicationchart/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable('#' + table.id)) {
                                    $('#' + table.id).dataTable().fnDraw()
                                }
                                if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                    $('#' + treatmentTableId).dataTable().fnDraw()
                                }
                            }
                            deleteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteBtn.removeAttribute('disabled')
                        })
                } deleteBtn.removeAttribute('disabled')
            }
        })
    })

    document.querySelectorAll('#upcomingMedicationsTable, #upcomingNursingChartsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const giveMedicationBtn = event.target.closest('#giveMedicationBtn')
            const reportServiceBtn = event.target.closest('#reportServiceBtn')
            
            if (giveMedicationBtn) {
                saveGivenMedicationBtn.setAttribute('data-id', giveMedicationBtn.getAttribute('data-id'))
                saveGivenMedicationBtn.setAttribute('data-table', giveMedicationBtn.getAttribute('data-table'))
                giveMedicationModal._element.querySelector('#patient').value = giveMedicationBtn.getAttribute('data-patient')
                giveMedicationModal._element.querySelector('#treatment').value = giveMedicationBtn.getAttribute('data-treatment')
                giveMedicationModal._element.querySelector('#prescription').value = giveMedicationBtn.getAttribute('data-prescription')
                giveMedicationModal._element.querySelector('#dose').value = giveMedicationBtn.getAttribute('data-dose')
                giveMedicationModal.show()
            }
    
            if (reportServiceBtn) {
                saveServiceDoneBtn.setAttribute('data-id', reportServiceBtn.getAttribute('data-id'))
                saveServiceDoneBtn.setAttribute('data-table', reportServiceBtn.getAttribute('data-table'))
                serviceDoneModal._element.querySelector('#patient').value = reportServiceBtn.getAttribute('data-patient')
                serviceDoneModal._element.querySelector('#treatment').value = reportServiceBtn.getAttribute('data-care')
                serviceDoneModal._element.querySelector('#instruction').value = reportServiceBtn.getAttribute('data-instruction')
                serviceDoneModal.show()
            }
        })    
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
                http.get(`/resources/list/bulk`, {params: {resource: itemInput.value, dept: itemInput.dataset.dept}}).then((response) => {
                    displayItemsList(datalistEl, response.data, 'itemOption')
                })
            }
    })

    requestBulkBtn.addEventListener('click', function () {
        requestBulkBtn.setAttribute('disabled', 'disabled')
        const itemId =  getDatalistOptionId(bulkRequestModal._element, itemInput, bulkRequestModal._element.querySelector(`#itemList`))
        if (!itemId) {
            clearValidationErrors(bulkRequestModal._element)
            const message = {"item": ["Please pick an item from the list"]}               
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

    // review consultation loops
    document.querySelectorAll('#treatmentDiv, #medicationPrescriptionsModal, #otherPrescriptionsModal, #visitHistoryDiv').forEach(div => {
        div.addEventListener('click', function (event) {
            const collapseConsultationBtn   = event.target.closest('.collapseConsultationBtn')
            const collapseVisitBtn          = event.target.closest('.collapseVisitBtn')
            const giveMedicationBtn         = event.target.closest('#giveMedicationBtn')
            const chartMedicationBtn        = event.target.closest('#chartMedicationBtn')
            const chartPrescriptionBtn      = event.target.closest('#chartPrescriptionBtn')
            const newDeliveryNoteBtn        = event.target.closest('#newDeliveryNoteBtn')
            const deliveryNoteBtn           = event.target.closest('.updateDeliveryNoteBtn, .viewDeliveryNoteBtn')
            const saveWardAndBedBtn         = event.target.closest('#saveWardAndBedBtn')
            const wardAndBedDiv             = document.querySelectorAll('#wardAndBedDiv')
            const deleteGivenBtn            = event.target.closest('#deleteGivenBtn')
            const discontinueBtn            = event.target.closest('.discontinueBtn')
            const deleteDeliveryNoteBtn     = event.target.closest('.deleteDeliveryNoteBtn')
            const reportServiceBtn          = event.target.closest('#reportServiceBtn')
            const viewer                    = 'nurse'
    
            if (collapseConsultationBtn) {
                const gotoDiv              = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
                const investigationTableId = gotoDiv.querySelector('.investigationTable').id
                const medicationsTableId        = gotoDiv.querySelector('.nurseTreatmentTable').id
                const otherPrescriptionsTableId = gotoDiv.querySelector('.otherPrescriptionsNursesTable').id
                const conId     = gotoDiv.querySelector('.investigationTable').dataset.id
                const isHistory = +collapseConsultationBtn.getAttribute('data-ishistory')
                console.log(isHistory)
    
                if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                    $('#' + investigationTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#' + medicationsTableId)) {
                    $('#' + medicationsTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#' + otherPrescriptionsTableId)) {
                    $('#' + otherPrescriptionsTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#deliveryNoteTable'+conId)) {
                    $('#deliveryNoteTable'+conId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#surgeryNoteTable'+conId)) {
                    $('#surgeryNoteTable'+conId).dataTable().fnDestroy()
                }
                const goto = () => {
                    location.href = collapseConsultationBtn.getAttribute('data-goto')
                    window.history.replaceState({}, document.title, "/" + "nurses")
                    getLabTableByConsultation(investigationTableId, treatmentDetailsModal._element, viewer, conId, null)
                    getNurseMedicationsByFilter(medicationsTableId, conId, treatmentDetailsModal._element, null, isHistory)
                    getOtherPrescriptionsByFilterNurses(otherPrescriptionsTableId, conId, treatmentDetailsModal._element, null, isHistory)
                    getDeliveryNoteTable('deliveryNoteTable'+conId, conId, true, isHistory)
                    getSurgeryNoteTable('surgeryNoteTable'+conId, conId, false, isHistory)
                }
                setTimeout(goto, 300)
            }

            if (collapseVisitBtn) {
                const visitId               = collapseVisitBtn.getAttribute('data-id')
                const ancRegId              = collapseVisitBtn.getAttribute('data-ancregid')
                const [getVitalsigns, id]   = collapseVisitBtn.getAttribute('data-anc') ? [getAncVitalSignsTable, ancRegId] : [getVitalSignsTableByVisit, visitId]

                if ($.fn.DataTable.isDataTable('#vitalSignsHistory'+visitId)){
                    $('#vitalSignsHistory'+visitId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#billingTableHistory'+visitId)){
                    $('#billingTableHistory'+visitId).dataTable().fnDestroy()
                }

                const goto = () => {
                    location.href = collapseVisitBtn.getAttribute('data-gotovisit')
                    window.history.replaceState({}, document.title, "/" + "doctors" )
                    getVitalsigns('#vitalSignsHistory'+visitId, id, consultationHistoryModal)
                    getbillingTableByVisit('billingTableHistory'+visitId, visitId, consultationHistoryModal._element)
                }
                setTimeout(goto, 300)
            }
    
            if (chartMedicationBtn) {
                const prescriptionId = chartMedicationBtn.getAttribute('data-id')
                const tableId = chartMedicationBtn.getAttribute('data-table')
                const conId = chartMedicationBtn.getAttribute('data-consultation')
                const visitId = chartMedicationBtn.getAttribute('data-visit')
                chartMedicationModal._element.querySelector('#patient').value = chartMedicationBtn.getAttribute('data-patient')
                chartMedicationModal._element.querySelector('#sponsorName').value = chartMedicationBtn.getAttribute('data-sponsor')
                chartMedicationModal._element.querySelector('#treatment').value = chartMedicationBtn.getAttribute('data-resource')
                chartMedicationModal._element.querySelector('#prescription').value = chartMedicationBtn.getAttribute('data-prescription')
                chartMedicationModal._element.querySelector('#prescribedBy').value = chartMedicationBtn.getAttribute('data-prescribedBy')
                chartMedicationModal._element.querySelector('#prescribed').value = chartMedicationBtn.getAttribute('data-prescribed')
                saveMedicationChartBtn.setAttribute('data-id', prescriptionId)
                saveMedicationChartBtn.setAttribute('data-table', tableId)
                saveMedicationChartBtn.setAttribute('data-consultation', conId)
                saveMedicationChartBtn.setAttribute('data-visit', visitId)
    
                getMedicationChartByPrescription(medicationChartTable.id, prescriptionId, chartMedicationModal._element)
    
                chartMedicationModal.show()
            }

            if (chartPrescriptionBtn) {
                const prescriptionId = chartPrescriptionBtn.getAttribute('data-id')
                const tableId = chartPrescriptionBtn.getAttribute('data-table')
                const conId = chartPrescriptionBtn.getAttribute('data-consultation')
                const visitId = chartPrescriptionBtn.getAttribute('data-visit')
                chartPrescriptionModal._element.querySelector('#patient').value = chartPrescriptionBtn.getAttribute('data-patient')
                chartPrescriptionModal._element.querySelector('#sponsorName').value = chartPrescriptionBtn.getAttribute('data-sponsor')
                chartPrescriptionModal._element.querySelector('#service').value = chartPrescriptionBtn.getAttribute('data-resource')
                chartPrescriptionModal._element.querySelector('#instruction').value = chartPrescriptionBtn.getAttribute('data-note')
                chartPrescriptionModal._element.querySelector('#prescribedBy').value = chartPrescriptionBtn.getAttribute('data-prescribedBy')
                chartPrescriptionModal._element.querySelector('#prescribed').value = chartPrescriptionBtn.getAttribute('data-prescribed')
                savePrescriptionChartBtn.setAttribute('data-id', prescriptionId)
                savePrescriptionChartBtn.setAttribute('data-table', tableId)
                savePrescriptionChartBtn.setAttribute('data-consultation', conId)
                savePrescriptionChartBtn.setAttribute('data-visit', visitId)
    
                getPrescriptionChartByPrescription(prescriptionChartTable.id, prescriptionId, chartPrescriptionModal._element)
    
                chartPrescriptionModal.show()
            }
    
            if (giveMedicationBtn) {
                saveGivenMedicationBtn.setAttribute('data-id', giveMedicationBtn.getAttribute('data-id'))
                saveGivenMedicationBtn.setAttribute('data-table', giveMedicationBtn.getAttribute('data-table'))
                giveMedicationModal._element.querySelector('#patient').value = giveMedicationBtn.getAttribute('data-patient')
                giveMedicationModal._element.querySelector('#treatment').value = giveMedicationBtn.getAttribute('data-treatment')
                giveMedicationModal._element.querySelector('#prescription').value = giveMedicationBtn.getAttribute('data-prescription')
                giveMedicationModal._element.querySelector('#dose').value = giveMedicationBtn.getAttribute('data-dose')
                giveMedicationModal.show()
            }

            if (reportServiceBtn) {
                saveServiceDoneBtn.setAttribute('data-id', reportServiceBtn.getAttribute('data-id'))
                saveServiceDoneBtn.setAttribute('data-table', reportServiceBtn.getAttribute('data-table'))
                serviceDoneModal._element.querySelector('#patient').value = reportServiceBtn.getAttribute('data-patient')
                serviceDoneModal._element.querySelector('#treatment').value = reportServiceBtn.getAttribute('data-treatment')
                serviceDoneModal._element.querySelector('#instruction').value = reportServiceBtn.getAttribute('data-instruction')
                serviceDoneModal.show()
            }
    
            if (deleteGivenBtn){
                deleteGivenBtn.setAttribute('disabled', 'disabled')
                const treatmentTableId = deleteGivenBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete this Information?')) {
                    const id = deleteGivenBtn.getAttribute('data-id')
                    http.patch(`/medicationchart/removegiven/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                $('#' + treatmentTableId).dataTable().fnDraw()
                            }
                        }
                        deleteGivenBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteGivenBtn.removeAttribute('disabled')
                    })
                }
                deleteGivenBtn.removeAttribute('disabled')
            }

            if (discontinueBtn){
                if (confirm('Are you sure you want to discontinue prescription?')) {
                    const prescriptionId = discontinueBtn.getAttribute('data-id')
                    const treatmentTableId = discontinueBtn.getAttribute('data-table')
                    http.patch(`/prescription/${prescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                $('#' + treatmentTableId).dataTable().fnDraw()
                            }
                        }
                    })
                    .catch((error) => {
                        if (error.response.status === 403){
                            alert(error.response.data.message); 
                        }
                        console.log(error)
                    })
                }
            }
    
            if (newDeliveryNoteBtn) {
                createDeliveryNoteBtn.setAttribute('data-conid', newDeliveryNoteBtn.dataset.id)
                createDeliveryNoteBtn.setAttribute('data-visitid', newDeliveryNoteBtn.dataset.visitid)
                newDeliveryNoteModal.show()
            }
    
            if (deliveryNoteBtn) {
                const isUpdate = deliveryNoteBtn.id == 'updateDeliveryNoteBtn'
                const [btn, modalBtn, modal ] = isUpdate ? [deliveryNoteBtn, saveDeliveryNoteBtn, updateDeliveryNoteModal] : [deliveryNoteBtn, saveDeliveryNoteBtn, viewDeliveryNoteModal]
                btn.setAttribute('disabled', 'disabled')
                saveDeliveryNoteBtn.setAttribute('data-table', btn.dataset.table)
                http.get(`/deliverynote/${btn.getAttribute('data-id')}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openModals(modal, modalBtn, response.data.data)
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                    })
                setTimeout(()=>{btn.removeAttribute('disabled')}, 2000)
            }

            if (deleteDeliveryNoteBtn){
                deleteDeliveryNoteBtn.setAttribute('disabled', 'disabled')
                const id = deleteDeliveryNoteBtn.getAttribute('data-id')
                const tableId = deleteDeliveryNoteBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete Delivery Note?')) {
                    http.delete(`/deliverynote/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable('#' + tableId)) {
                                    $('#' + tableId).dataTable().fnDraw()
                                }
                            }
                            deleteDeliveryNoteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteDeliveryNoteBtn.removeAttribute('disabled')
                        })
                } deleteDeliveryNoteBtn.removeAttribute('disabled')
            }
    
            if (saveWardAndBedBtn) {
                wardAndBedDiv.forEach(div => {
                    if (div.dataset.div === saveWardAndBedBtn.dataset.btn) {
                        saveWardAndBedBtn.setAttribute('disabled', 'disabled')
                        const conId = saveWardAndBedBtn.dataset.id
    
                        http.post(`consultation/${conId}`, { ...getDivData(div) }, { "html": div })
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                new Toast(div.querySelector('#saveUpdateAdmissionStatusToast'), { delay: 2000 }).show()
                                clearDivValues(div)
                                clearValidationErrors(div)
                            }
                            saveWardAndBedBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            console.log(error)
                            saveWardAndBedBtn.removeAttribute('disabled')
                        })
                    }
                })
            }
        })
    })

    saveMedicationChartBtn.addEventListener('click', function () {
        const prescriptionId = saveMedicationChartBtn.getAttribute('data-id')
        const treatmentTableId = saveMedicationChartBtn.getAttribute('data-table')
        const conId = saveMedicationChartBtn.getAttribute('data-consultation')
        const visitId = saveMedicationChartBtn.getAttribute('data-visit')

        saveMedicationChartBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(medicationChartDiv), prescriptionId, conId, visitId }

        http.post('/medicationchart', { ...data }, { "html": medicationChartDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    new Toast(medicationChartDiv.querySelector('#saveMedicationChartToast'), { delay: 2000 }).show()

                    clearDivValues(medicationChartDiv)
                    clearValidationErrors(medicationChartDiv)

                    if ($.fn.DataTable.isDataTable('#' + medicationChartTable.id)) {
                        $('#' + medicationChartTable.id).dataTable().fnDraw()
                    }

                    if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                        $('#' + treatmentTableId).dataTable().fnDraw()
                    }

                }
                saveMedicationChartBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                console.log(error)
                saveMedicationChartBtn.removeAttribute('disabled')
            })
    })

    savePrescriptionChartBtn.addEventListener('click', function () {
        const prescriptionId = savePrescriptionChartBtn.getAttribute('data-id')
        const otherPrescriptionsTableId = savePrescriptionChartBtn.getAttribute('data-table')
        const conId = savePrescriptionChartBtn.getAttribute('data-consultation')
        const visitId = savePrescriptionChartBtn.getAttribute('data-visit')

        savePrescriptionChartBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(prescriptionChartDiv), prescriptionId, conId, visitId }

        http.post('/nursingchart', { ...data }, { "html": prescriptionChartDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    new Toast(prescriptionChartDiv.querySelector('#savePrescriptionChartToast'), { delay: 2000 }).show()

                    clearDivValues(prescriptionChartDiv)
                    clearValidationErrors(prescriptionChartDiv)

                    if ($.fn.DataTable.isDataTable('#' + prescriptionChartTable.id)) {
                        $('#' + prescriptionChartTable.id).dataTable().fnDraw()
                    }

                    if ($.fn.DataTable.isDataTable('#' + otherPrescriptionsTableId)) {
                        $('#' + otherPrescriptionsTableId).dataTable().fnDraw()
                    }

                }
                savePrescriptionChartBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                console.log(error)
                savePrescriptionChartBtn.removeAttribute('disabled')
            })
    })

    saveGivenMedicationBtn.addEventListener('click', function () {
        const medicationChartId = saveGivenMedicationBtn.getAttribute('data-id')
        const treatmentTableId = saveGivenMedicationBtn.getAttribute('data-table')
        saveGivenMedicationBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(giveMedicationDiv), medicationChartId }

        http.patch(`/medicationchart/${medicationChartId}`, { ...data }, { "html": giveMedicationDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {

                    clearDivValues(giveMedicationDiv)
                    clearValidationErrors(giveMedicationDiv)

                    if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                        $('#' + treatmentTableId).dataTable().fnDraw()
                    }
                }
                saveGivenMedicationBtn.removeAttribute('disabled')
                giveMedicationModal.hide()
            })
            .catch((error) => {
                console.log(error)
                saveGivenMedicationBtn.removeAttribute('disabled')
            })
    })

    saveServiceDoneBtn.addEventListener('click', function () {
        const nursingChartId = saveServiceDoneBtn.getAttribute('data-id')
        const otherPrescriptionTableId = saveServiceDoneBtn.getAttribute('data-table')
        saveServiceDoneBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(saveServiceDoneDiv), nursingChartId }

        http.patch(`/nursingchart/${nursingChartId}`, { ...data }, { "html": saveServiceDoneDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {

                    clearDivValues(saveServiceDoneDiv)
                    clearValidationErrors(saveServiceDoneDiv)

                    if ($.fn.DataTable.isDataTable('#' + otherPrescriptionTableId)) {
                        $('#' + otherPrescriptionTableId).dataTable().fnDraw()
                    }
                }
                saveServiceDoneBtn.removeAttribute('disabled')
                serviceDoneModal.hide()
            })
            .catch((error) => {
                saveGivenMedicationBtn.removeAttribute('disabled')
                console.log(error)
            })
            // saveGivenMedicationBtn.removeAttribute('disabled')
    })

    createDeliveryNoteBtn.addEventListener('click', function () {
        createDeliveryNoteBtn.setAttribute('disabled', 'disabled')
        const conId = createDeliveryNoteBtn.dataset.conid
        const visitId = createDeliveryNoteBtn.dataset.visitid

        let data = { ...getDivData(newDeliveryNoteModal._element), conId, visitId }
        http.post('/deliverynote', {...data}, {"html": newDeliveryNoteModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newDeliveryNoteModal.hide()
                clearDivValues(newDeliveryNoteModal._element)
                if ($.fn.DataTable.isDataTable('#deliveryNoteTable' + conId)) {
                    $('#deliveryNoteTable' + conId).dataTable().fnDraw()
                }
            }
            createDeliveryNoteBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createDeliveryNoteBtn.removeAttribute('disabled')
            alert(error)
        })
    })

    saveDeliveryNoteBtn.addEventListener('click', function () {
        saveDeliveryNoteBtn.setAttribute('disabled', 'disabled')
        const id        = saveDeliveryNoteBtn.dataset.id
        const tableId   = '#'+saveDeliveryNoteBtn.dataset.table

        http.patch(`/deliverynote/${id}`, getDivData(updateDeliveryNoteModal._element), {"html": updateDeliveryNoteModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateDeliveryNoteModal.hide()
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).dataTable().fnDraw()
                }
            }
            saveDeliveryNoteBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveDeliveryNoteBtn.removeAttribute('disabled')
            alert(error)
        })
    })

    bulkRequestModal._element.addEventListener('hide.bs.modal', function () {
        bulkRequestsTable ? bulkRequestsTable.draw() : ''
    })

    document.querySelectorAll('#vitalsignsModal, #ancVitalsignsModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            outPatientsVisitTable.draw()
            waitingTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        })
    })
})

function openNurseModals(modal, button, { id, visitId, ancRegId, patientType, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }
    

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-ancregid', ancRegId)
    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-patienttype', patientType)
}

function openNurseModals1(modal, button, { id, visitId, ancRegId, patientType, cardNo, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }
    
    if (modal._element.id !== 'consultationHistoryModal'){
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-ancregid', ancRegId)
        modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-patienttype', patientType)
    
        if (modal._element.id !== 'consultationReviewModal') {
            button.setAttribute('data-id', visitId)
            modal.show()
        }
    } else {
        button.setAttribute('href', `https://portal.sandrahospitalmkd.com/Consultations/History?CardNumber=${cardNo}`)
    }

    // modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    // modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-ancregid', ancRegId)
    // modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-patienttype', patientType)
}
