import { Offcanvas, Modal, Toast } from "bootstrap";
import { clearDivValues, clearValidationErrors, getOrdinal, loadingSpinners, getDivData, bmiCalculator, openModals, lmpCalculator, populatePatientSponsor, populateVitalsignsModal, populateDischargeModal, lmpCurrentCalculator, displayItemsList, getDatalistOptionId, handleValidationErrors, displayVisits, populateWardAndBedModal, clearItemsList, getSelectedResourceValues, getDatalistOptionStock, getShiftPerformance } from "./helpers"
import $ from 'jquery';
import http from "./http";
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import { getWaitingTable, getPatientsVisitsByFilterTable, getNurseMedicationsByFilter, getMedicationChartByPrescription, getUpcomingMedicationsTable, getDeliveryNoteTable, getAncVitalSignsTable, getUpcomingNursingChartsTable, getOtherPrescriptionsByFilterNurses, getPrescriptionChartByPrescription, getEmergencyTable, getNursesReportTable, getShiftReportTable } from "./tables/nursesTables";
import { getVitalSignsTableByVisit, getLabTableByConsultation, getSurgeryNoteTable, getPrescriptionTableByConsultation, getPatientsFileTable } from "./tables/doctorstables";
import { getbillingTableByVisit } from "./tables/billingTables";
import { getBulkRequestTable } from "./tables/pharmacyTables";
import { visitDetails } from "./dynamicHTMLfiles/visits";
$.fn.dataTable.ext.errMode = 'throw';

window.addEventListener('DOMContentLoaded', function () {
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
    const ancVitalsignsModal            = new Modal(document.getElementById('ancVitalsignsModal'))
    const giveMedicationModal           = new Modal(document.getElementById('giveMedicationModal'))
    const serviceDoneModal              = new Modal(document.getElementById('serviceDoneModal'))
    const newAncRegisterationModal      = new Modal(document.getElementById('newAncRegisterationModal'))
    const updateAncRegisterationModal   = new Modal(document.getElementById('updateAncRegisterationModal'))
    const viewAncRegisterationModal     = new Modal(document.getElementById('viewAncRegisterationModal'))
    const dischargeModal                = new Modal(document.getElementById('dischargeModal'))
    const wardAndBedModal               = new Modal(document.getElementById('wardAndBedModal'))
    const bulkRequestModal              = new Modal(document.getElementById('bulkRequestModal'))
    const investigationAndManagementModal   = new Modal(document.getElementById('investigationAndManagementModal'))
    const nursesReportModal             = new Modal(document.getElementById('nursesReportModal'))
    const newNursesReportTemplateModal  = new Modal(document.getElementById('newNursesReportTemplateModal'))
    const editNursesReportTemplateModal = new Modal(document.getElementById('editNursesReportTemplateModal'))
    const fileModal                     = new Modal(document.getElementById('fileModal'))
    const newShiftReportTemplateModal   = new Modal(document.getElementById('newShiftReportTemplateModal'))
    const editShiftReportTemplateModal  = new Modal(document.getElementById('editShiftReportTemplateModal'))
    const viewShiftReportTemplateModal  = new Modal(document.getElementById('viewShiftReportTemplateModal'))

    const visitHistoryDiv           = consultationHistoryModal._element.querySelector('#visitHistoryDiv')
    const addVitalsignsDiv          = document.querySelectorAll('#addVitalsignsDiv')
    const medicationChartDiv        = chartMedicationModal._element.querySelector('#chartMedicationDiv')
    const prescriptionChartDiv      = chartPrescriptionModal._element.querySelector('#chartPrescriptionDiv')
    const medicationChartTable      = chartMedicationModal._element.querySelector('#medicationChartTable')
    const nursingChartTable         = chartPrescriptionModal._element.querySelector('#nursingChartTable')
    const giveMedicationDiv         = giveMedicationModal._element.querySelector('#giveMedicationDiv')
    const saveServiceDoneDiv        = serviceDoneModal._element.querySelector('#saveServiceDoneDiv')
    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')
    const dischargeDetailsDiv       = dischargeModal._element.querySelector('#dischargeDetails')
    const shiftPerformanceDiv       = document.querySelector('#shiftPerformanceDiv')

    const waitingBtn                = document.querySelector('#waitingBtn')
    const addVitalsignsBtn          = document.querySelectorAll('#addVitalsignsBtn')
    const saveMedicationChartBtn    = chartMedicationModal._element.querySelector('#saveMedicationChartBtn')
    const saveGivenMedicationBtn    = giveMedicationModal._element.querySelector('.saveGivenMedicationBtn')
    const savePrescriptionChartBtn  = chartPrescriptionModal._element.querySelector('#savePrescriptionChartBtn')
    const saveServiceDoneBtn        = serviceDoneModal._element.querySelector('.saveServiceDoneBtn')
    const newDeliveryNoteBtn        = treatmentDetailsModal._element.querySelector('#newDeliveryNoteBtn')
    const createDeliveryNoteBtn     = newDeliveryNoteModal._element.querySelector('#createBtn')
    const saveDeliveryNoteBtn       = updateDeliveryNoteModal._element.querySelector('#saveBtn')
    const registerAncBtn            = newAncRegisterationModal._element.querySelector('#registerAncBtn') 
    const saveAncBtn                = updateAncRegisterationModal._element.querySelector('#saveAncBtn') 
    const deleteAncBtn              = viewAncRegisterationModal._element.querySelector('#deleteAncBtn')
    const bulkRequestBtn            = document.querySelector('#newBulkRequestBtn')
    const requestBulkBtn            = bulkRequestModal._element.querySelector('#requestBulkBtn')
    const moreHistoryBtn            = consultationHistoryModal._element.querySelector('#moreHistoryBtn')
    const resourceInput             = document.querySelector('#resource')
    const addInvestigationAndManagmentBtn   = investigationAndManagementModal._element.querySelector('#addInvestigationAndManagementBtn')
    const inpatientsMedChartBtn     = document.querySelector('#inpatientsMedChartBtn')
    const inpatientMedicationBadgeSpan = document.querySelector('#inpatientMedicationBadgeSpan')
    const nursingChartBtn           = document.querySelector('#nursingChartBtn')
    const inpatientNursingBadgeSpan    = document.querySelector('#inpatientNursingBadgeSpan')
    const newNursesReportBtn        = nursesReportModal._element.querySelector('#newNursesReportBtn')
    const createNursesReportBtn     = newNursesReportTemplateModal._element.querySelector('#createNursesReportBtn')
    const saveNursesReportBtn       = editNursesReportTemplateModal._element.querySelector('#saveNursesReportBtn')
    const saveDischargeBtn          = document.querySelector('#saveDischargeBtn')
    const fileBtns                  = document.querySelectorAll('#fileBtn')
    const uploadFileBtn             = fileModal._element.querySelector('#uploadFileBtn')
    const shiftReportBtn            = document.querySelector('#shiftReportBtn')
    const newNursesShiftReportBtn   = document.querySelector('#newNursesShiftReportBtn')
    const createShiftReportBtn      = newShiftReportTemplateModal._element.querySelector('#createShiftReportBtn')
    const saveShiftReportBtn        = editShiftReportTemplateModal._element.querySelector('#saveShiftReportBtn')
    const shiftBadgeSpan            = document.querySelector('#shiftBadgeSpan')

    const itemInput                 = bulkRequestModal._element.querySelector('#item')
    const [outPatientsTab, inPatientsTab, ancPatientsTab, bulkRequestsTab, emergencyTab]  = [document.querySelector('#nav-outPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-ancPatients-tab'), document.querySelector('#nav-bulkRequests-tab'), document.querySelector('#nav-emergency-tab')]
    
    bmiCalculator(document.querySelectorAll('#height, .weight'))
    lmpCalculator(document.querySelectorAll('#lmp'), document.querySelectorAll('#registerationDiv'))

    let outPatientsVisitTable, ancPatientsVisitTable, bulkRequestsTable, emergencyTable, nursesReportTable, surgeryNoteTable, deliveryNoteTable, medicationsTable, patientsFilesTable

    const inPatientsVisitTable          = getPatientsVisitsByFilterTable('inPatientsVisitTable', 'Inpatient')
    const waitingTable                  = getWaitingTable('waitingTable')
    const upcomingMedicationsTable      = getUpcomingMedicationsTable('upcomingMedicationsTable', inpatientsMedChartBtn, inpatientMedicationBadgeSpan)
    const upcomingNursingChartsTable    = getUpcomingNursingChartsTable('upcomingNursingChartsTable', nursingChartBtn, inpatientNursingBadgeSpan)
    const nursesShiftReportTable        = getShiftReportTable('nursesShiftReportTable', 'nurses', shiftBadgeSpan)
    $('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #bulkRequestsTable, #emergencyTable, #nursesReportTable, #upcomingMedicationsTable, #upcomingNursingChartsTable, #waitingTable, #medicationsTable, #otherPrescriptionsTable, #ancVitalSignsTable, #vitalSignsTable').on('error.dt', function(e, settings, techNote, message) {techNote == 7 ? window.location.reload() : ''})

    const shiftPerformance = () => {
        getShiftPerformance('Nurse', shiftPerformanceDiv)
    }

    shiftReportBtn.addEventListener('click', function () {nursesShiftReportTable.draw()})

    shiftPerformance()

    newNursesShiftReportBtn.addEventListener('click', function () {
        newShiftReportTemplateModal.show()
    })

    inPatientsTab.addEventListener('click', function() {
        inPatientsVisitTable.draw();
        upcomingMedicationsTable.draw();
        upcomingNursingChartsTable.draw();
        shiftPerformance();
        nursesShiftReportTable.draw()
    });

    outPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#outPatientsVisitTable' )){
            $('#outPatientsVisitTable').dataTable().fnDraw()
        } else {
            outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient')
        }
        upcomingMedicationsTable.draw()
        upcomingNursingChartsTable.draw()
        shiftPerformance()
        nursesShiftReportTable.draw()
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('ancPatientsVisitTable', 'ANC')
        }
        upcomingMedicationsTable.draw()
        upcomingNursingChartsTable.draw()
        shiftPerformance()
        nursesShiftReportTable.draw()
    })

    bulkRequestsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#bulkRequestsTable' )){
            $('#bulkRequestsTable').dataTable().fnDraw()
        } else {
            bulkRequestsTable = getBulkRequestTable('bulkRequestsTable', 'nurses')
        }
        upcomingMedicationsTable.draw()
        upcomingNursingChartsTable.draw()
        shiftPerformance()
        nursesShiftReportTable.draw()
    })

    emergencyTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#emergencyTable' )){
            $('#emergencyTable').dataTable().fnDraw()
        } else {
            emergencyTable = getEmergencyTable('emergencyTable', 'nurse')
        }
        upcomingMedicationsTable.draw()
        upcomingNursingChartsTable.draw()
        shiftPerformance()
        nursesShiftReportTable.draw()
    })

    waitingBtn.addEventListener('click', function () {
        http.get(`/visits/average`)
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                document.querySelector('#lastWeek').value = response.data.lastWeek
                document.querySelector('#thisWeek').value = response.data.thisWeek
                document.querySelector('#lastMonth').value = response.data.lastMonth
                document.querySelector('#thisMonth').value = response.data.thisMonth
            }
        })
        .catch((error) => {
            console.log(error)
        })
        waitingTable.draw()
    })

    fileBtns.forEach(btn => {btn.addEventListener('click', function() {fileModal.show()})});

    uploadFileBtn.addEventListener('click', function() { uploadFileBtn.setAttribute('disabled', 'disabled')
        const visitId = uploadFileBtn.getAttribute('data-id')
        http.post(`/patientsfiles/${visitId}`, { filename : fileModal._element.querySelector('#filename').value,
            patientsFile: fileModal._element.querySelector('#patientsFile').files[0],
            thirdParty : fileModal._element.querySelector('#thirdParty').value,
            comment : fileModal._element.querySelector('#comment').value,
        }, {"html": fileModal._element, headers: {'Content-Type' : 'multipart/form-data'}})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) { fileModal.hide()
                clearDivValues(fileModal._element); clearValidationErrors(fileModal._element); patientsFilesTable ? patientsFilesTable.draw() : ''
            }
            uploadFileBtn.removeAttribute('disabled')
        })
        .catch((response) => { console.log(response); uploadFileBtn.removeAttribute('disabled')})
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        inPatientsVisitTable.draw()
        outPatientsVisitTable ? outPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        upcomingMedicationsTable.draw()
        upcomingNursingChartsTable.draw()
        shiftPerformance()
    })

    document.querySelectorAll('#upcomingMedicationsoffcanvas, #upcomingNursingChartsoffcanvas').forEach(canvas => {
        canvas.addEventListener('show.bs.offcanvas', function () {
            upcomingMedicationsTable.draw()
            upcomingNursingChartsTable.draw()
        })

    })

    document.querySelectorAll('#upcomingMedicationsoffcanvas, #upcomingNursingChartsoffcanvas, #shiftReportOffcanvas').forEach(canvas => {
        canvas.addEventListener('hide.bs.offcanvas', function () {
            inpatientsMedChartBtn.classList.remove('colour-change', 'colour-change1')
            inpatientsMedChartBtn.classList.add('btn-primary')
            nursingChartBtn.classList.remove('colour-change', 'colour-change1')
            nursingChartBtn.classList.add('btn-primary')
            inPatientsVisitTable.draw()
            outPatientsVisitTable ? outPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            upcomingMedicationsTable.draw()
            upcomingNursingChartsTable.draw()
            shiftPerformance()
            nursesShiftReportTable.draw();
        })
    })

    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #waitingTable, #emergencyTable, #treatmentDetailsModal, #medicationPrescriptionsModal').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
            const vitalsignsBtn             = event.target.closest('.vitalSignsBtn, .ancVitalSignsBtn')
            const ancRegisterationBtn       = event.target.closest('.ancRegisterationBtn')
            const ancBtn                    = event.target.closest('#viewRegisterationBtn, #editRegisterationBtn')
            const dischargedBtn             = event.target.closest('.dischargedBtn')
            const historyBtn                = event.target.closest('.historyBtn')
            const viewMedicationBtn         = event.target.closest('.viewMedicationBtn')
            const viewOtherPrescriptionsBtn = event.target.closest('.viewOtherPrescriptionsBtn')
            const addPrescriptionBtn        = event.target.closest('.addPrescriptionBtn')
            const reportsListBtn            = event.target.closest('.reportsListBtn')
            const markDoneBtn               = event.target.closest('.markDoneBtn')
            const wardBedBtn                = event.target.closest('.wardBedBtn')
            const viewer = 'nurse'
            let [iteration, count]          = [0, 0]
    
            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
                const [visitId, patientType, ancRegId, closed, patientId] = [consultationDetailsBtn.getAttribute('data-id'), consultationDetailsBtn.getAttribute('data-patientType'), consultationDetailsBtn.getAttribute('data-ancregid'), +consultationDetailsBtn.getAttribute('data-closed'), consultationDetailsBtn.getAttribute('data-patientid')] 
                const isAnc = patientType === 'ANC'
                const [modal, div, displayFunction, vitalSignsTable, id, suffixId] = isAnc ? [ancTreatmentDetailsModal, ancTreatmentDiv, AncPatientReviewDetails, getAncVitalSignsTable, ancRegId, 'AncConDetails'] : [treatmentDetailsModal, regularTreatmentDiv, regularReviewDetails, getVitalSignsTableByVisit, visitId, 'ConDetails']
                createDeliveryNoteBtn.setAttribute('data-visitid', visitId); uploadFileBtn.setAttribute('data-id', visitId);
                closed ? modal._element.querySelector('.addVitalsignsDiv').classList.add('d-none') : modal._element.querySelector('.addVitalsignsDiv').classList.remove('d-none')
                modal._element.querySelector('.historyBtn').setAttribute('data-patienttype', patientType); modal._element.querySelector('.historyBtn').setAttribute('data-patientid', patientId)
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
                            deliveryNoteTable   = getDeliveryNoteTable('deliveryNoteTable', visitId, true, modal._element)
                            surgeryNoteTable    = getSurgeryNoteTable('surgeryNoteTable', visitId, false, modal._element)
                            patientsFilesTable  = getPatientsFileTable(`patientsFileTable`, visitId, modal._element)
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
                modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-patienttype', vitalsignsBtn.getAttribute('data-patienttype'))
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
                newAncRegisterationModal._element.querySelector('#registerAncBtn').setAttribute('data-visitid', ancRegisterationBtn.parentElement.dataset.id)
                newAncRegisterationModal.show()
                ancRegisterationBtn.parentElement.removeAttribute('disabled')
            }
            if (viewMedicationBtn){
                viewMedicationBtn.setAttribute('disabled', 'disabled')
                const tableId = medicationPrescriptionsModal._element.querySelector('.medicationsTable').id
                const visitId = viewMedicationBtn.getAttribute('data-visitid') ?? viewMedicationBtn.getAttribute('data-id')
                populatePatientSponsor(medicationPrescriptionsModal, viewMedicationBtn)
                medicationPrescriptionsModal._element.querySelector('.addPrescriptionBtn').setAttribute('data-patient', viewMedicationBtn.getAttribute('data-patient'))
                medicationPrescriptionsModal._element.querySelector('.addPrescriptionBtn').setAttribute('data-sponsor', viewMedicationBtn.getAttribute('data-sponsor'))
                medicationPrescriptionsModal._element.querySelector('.addPrescriptionBtn').setAttribute('data-sponsorcat', viewMedicationBtn.getAttribute('data-sponsorcat'))
                medicationPrescriptionsModal._element.querySelector('.addPrescriptionBtn').setAttribute('data-id', visitId)
                medicationsTable = getNurseMedicationsByFilter(tableId, null, medicationPrescriptionsModal._element, visitId)
    
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
                deleteAncBtn.setAttribute('data-id', btn.getAttribute('data-ancregid'))
                http.get(`/ancregisteration/${btn.getAttribute('data-ancregid')}`)
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
                populateDischargeModal(dischargeModal, dischargedBtn)
                dischargeModal.show()
            }

            if (wardBedBtn){ populateWardAndBedModal(wardAndBedModal, wardBedBtn); wardAndBedModal.show()}

            if (historyBtn){
                historyBtn.setAttribute('disabled', 'disabled')
                const patientId     = historyBtn.getAttribute('data-patientid')
                const isAnc         = historyBtn.getAttribute('data-patienttype') === 'ANC'
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
                    historyBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    historyBtn.removeAttribute('disabled')
                    console.log(error)
                })
            }

            if (addPrescriptionBtn){
                addPrescriptionBtn.setAttribute('disabled', 'disabled')
                resourceInput.setAttribute('data-sponsorcat', addPrescriptionBtn.getAttribute('data-sponsorcat'))
                investigationAndManagementModal._element.querySelector('.investigationAndManagementDiv').classList.remove('d-none')
                const btn = investigationAndManagementModal._element.querySelector('#addInvestigationAndManagementBtn')
                const visitId   =  addPrescriptionBtn.dataset?.id
                populatePatientSponsor(investigationAndManagementModal, addPrescriptionBtn)
                btn.setAttribute('data-visitid', visitId)
                getPrescriptionTableByConsultation('prescriptionTableConReview', null, visitId, investigationAndManagementModal._element)
                investigationAndManagementModal.show()
                setTimeout(()=> {addPrescriptionBtn.removeAttribute('disabled')}, 1000)
            }

            if (reportsListBtn){
                const visitId = reportsListBtn.getAttribute('data-id')
                createNursesReportBtn.setAttribute('data-visitid', visitId)
                populatePatientSponsor(nursesReportModal, reportsListBtn)
                populatePatientSponsor(newNursesReportTemplateModal, reportsListBtn)
                nursesReportTable = getNursesReportTable('nursesReportTable', visitId, nursesReportModal)
                nursesReportModal.show()
            }

            if(markDoneBtn){
                if (confirm("Are you sure you are done with this Patient's visit?")){
                    markDoneBtn.setAttribute('disabled', 'disabled')
                    const visitId = markDoneBtn.getAttribute('data-id')
                    http.patch(`/nurses/done/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            markDoneBtn.removeAttribute('disabled') 
                            inPatientsVisitTable.draw(false); upcomingMedicationsTable.draw(); upcomingNursingChartsTable.draw()
                            outPatientsVisitTable ? outPatientsVisitTable.draw(false) : ''
                            ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
                        }
                      })
                    .catch((error) => {
                        console.log(error)
                        markDoneBtn.removeAttribute('disabled')
                    })
                }
        
            }
        })
    })

    wardAndBedModal._element.querySelector('#saveWardAndBedBtn').addEventListener('click', function() {this.setAttribute('disabled', 'disabled'); const conId = this.getAttribute('data-conid'); let data = { ...getDivData(wardAndBedModal._element)}
        http.patch(`consultation/updatestatus/${conId}`, {...data}, {'html': wardAndBedModal._element})
        .then((response) => {if (response.status >= 200 || response.status <= 300) {wardAndBedModal.hide(); clearValidationErrors(wardAndBedModal._element)} this.removeAttribute('disabled')})
        .catch((response) => {console.log(response); this.removeAttribute('disabled')})
    })

    saveDischargeBtn.addEventListener('click', function () {
        const id = this.getAttribute('data-id')
        saveDischargeBtn.setAttribute('disabled', 'disabled')

        http.patch(`/visits/discharge/${id}`, getDivData(dischargeDetailsDiv), {html:dischargeDetailsDiv})
        .then((response) => {
            if (response) {clearDivValues(dischargeDetailsDiv);  clearValidationErrors(dischargeDetailsDiv)
                inPatientsVisitTable.draw(false); upcomingMedicationsTable.draw(); upcomingNursingChartsTable.draw()
                dischargeModal.hide()
            }
            saveDischargeBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            saveDischargeBtn.removeAttribute('disabled')
            console.log(response)
        })
    })

    newNursesReportBtn.addEventListener('click', function() {newNursesReportTemplateModal.show()})
    newDeliveryNoteBtn.addEventListener('click', function() {newDeliveryNoteModal.show()})

    document.querySelectorAll('#nursesReportTable, #deliveryNoteTable, #patientsFileTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const editNursesReportBtn   = event.target.closest('.editNursesReportBtn')
            const deleteNursesReportBtn = event.target.closest('.deleteNursesReportBtn')
            const deliveryNoteBtn       = event.target.closest('.updateDeliveryNoteBtn, .viewDeliveryNoteBtn')
            const deleteDeliveryNoteBtn = event.target.closest('.deleteDeliveryNoteBtn')
            const deleteFileBtn         = event.target.closest('.deleteFileBtn')
    
            if (editNursesReportBtn) {
                editNursesReportBtn.setAttribute('disabled', 'disabled')
                saveNursesReportBtn.setAttribute('data-table', editNursesReportBtn.dataset.table)
                http.get(`/nursesreport/${editNursesReportBtn.getAttribute('data-id')}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editNursesReportTemplateModal, saveNursesReportBtn, response.data.data)
                    }
                })
                .catch((error) => {
                    console.log(error)
                })
                setTimeout(()=>{editNursesReportBtn.removeAttribute('disabled')}, 2000)
            }
    
            if (deleteNursesReportBtn) {
                deleteNursesReportBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this report?')) {
                    const id = deleteNursesReportBtn.getAttribute('data-id')
                    http.delete(`/nursesreport/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                nursesReportTable ? nursesReportTable.draw(false) : ''
                            }
                            deleteNursesReportBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteNursesReportBtn.removeAttribute('disabled')
                        })
                }
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
                                    $('#' + tableId).dataTable().fnDraw(false)
                                }
                            }
                            deleteDeliveryNoteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            console.log(error)
                            deleteDeliveryNoteBtn.removeAttribute('disabled')
                        })
                } deleteDeliveryNoteBtn.removeAttribute('disabled')
            }

            if (deleteFileBtn){
                deleteFileBtn.setAttribute('disabled', 'disabled')
                    if (confirm('Are you sure you want to delete this file?')) {
                        const id = deleteFileBtn.getAttribute('data-id')
                        http.delete(`/patientsfiles/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                if ($.fn.DataTable.isDataTable( '#'+this.id )){
                                $('#'+this.id).dataTable().fnDraw()
                                }
                                if (response.status == 222){
                                    alert(response.data)
                                }
                                table.draw()
                            }
                            deleteFileBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {console.log(error);deleteFileBtn.removeAttribute('disabled')})
                    } deleteFileBtn.removeAttribute('disabled')
            }
        })
    })

    createNursesReportBtn.addEventListener('click', function() {
        createNursesReportBtn.setAttribute('disabled', 'disabled')
        const visitId       = createNursesReportBtn.getAttribute('data-visitid')
        http.post(`nursesreport/${visitId}`, {report: newNursesReportTemplateModal._element.querySelector('#report').value}, {'html': newNursesReportTemplateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                newNursesReportTemplateModal.hide()
                clearDivValues(newNursesReportTemplateModal._element)
                clearValidationErrors(newNursesReportTemplateModal._element)
                nursesReportTable ? nursesReportTable.draw(false) : ''
            }
            createNursesReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            createNursesReportBtn.removeAttribute('disabled')
        })
    })

    saveNursesReportBtn.addEventListener('click', function() {
        saveNursesReportBtn.setAttribute('disabled', 'disabled')
        const id = saveNursesReportBtn.getAttribute('data-id')
        http.patch(`nursesreport/${id}`, {report: editNursesReportTemplateModal._element.querySelector('#report').value}, {'html': editNursesReportTemplateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                editNursesReportTemplateModal.hide()
                clearValidationErrors(editNursesReportTemplateModal._element)
                nursesReportTable ? nursesReportTable.draw(false) : ''
            }
            saveNursesReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveNursesReportBtn.removeAttribute('disabled')
        })
    })

    registerAncBtn.addEventListener('click', function () {
        registerAncBtn.setAttribute('disabled', 'disabled')
        const patientId = registerAncBtn.dataset.patientid
        const visitId = registerAncBtn.dataset.visitid

        let data = { ...getDivData(newAncRegisterationModal._element), patientId, visitId}
        http.post('/ancregisteration', { ...data }, { "html": newAncRegisterationModal._element })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newAncRegisterationModal.hide()
                clearDivValues(newAncRegisterationModal._element)
                ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
            }
            registerAncBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            registerAncBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    saveAncBtn.addEventListener('click', function () {
        saveAncBtn.setAttribute('disabled', 'disabled')
        const id = saveAncBtn.dataset.id

        http.patch(`/ancregisteration/${id}`, getDivData(updateAncRegisterationModal._element), { "html": updateAncRegisterationModal._element })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateAncRegisterationModal.hide()
                ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
            }
            saveAncBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveAncBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
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
                        ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
                    }
                    deleteAncBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    deleteAncBtn.removeAttribute('disabled')
                    alert(error)
                })
        }
    })

    document.querySelectorAll('#admit').forEach(selectEl => {
        selectEl.addEventListener('change', function(){
            const div = selectEl.parentElement
            const status = selectEl.getAttribute('data-admissionstatus')
            const statuses = ['Inpatient', 'Observation']
            if (statuses.includes(status) && !statuses.includes(selectEl.value)){
                const message = {"admit": [`Pls note that this patient's status was "${status}". You may cause confusion with their medication schedule if you change to outpatient.`]}; handleValidationErrors(message, div)
            } else {clearValidationErrors(div)}
        })
    })

    // All consultation resource inputs
    resourceInput.addEventListener('input', function () {
            const div = resourceInput.parentElement.parentElement.parentElement.parentElement.parentElement
            const datalistEl = div.querySelector(`#resourceList${div.dataset.div}`)
            if (resourceInput.value < 2) {
                datalistEl.innerHTML = ''
                }
            if (resourceInput.value.length > 2) {
                http.get(`/nurses/list/emergency`, {params: {resource: resourceInput.value, sponsorCat: resourceInput.dataset.sponsorcat}}).then((response) => {
                    displayResourceList(datalistEl, response.data)
                })
            }
            const selectedOption = datalistEl.options.namedItem(resourceInput.value)
            if (selectedOption){
                clearValidationErrors(div)
                if (selectedOption.getAttribute('data-cat') == 'Medications'){
                    div.querySelector('.pres').classList.remove('d-none')
                } else {
                    div.querySelector('.qty').classList.remove('d-none')
                    div.querySelector('#quantity').value = 1
                    div.querySelector('.pres').classList.add('d-none')
                }
            }
        })
        
        addInvestigationAndManagmentBtn.addEventListener('click', () => {
                const div = addInvestigationAndManagmentBtn.parentElement.parentElement.parentElement
                addInvestigationAndManagmentBtn.setAttribute('disabled', 'disabled')
                const resourcevalues = getSelectedResourceValues(div, div.querySelector('.resource'), div.querySelector(`#resourceList${div.dataset.div}`))
                const [visitId, divPrescriptionTableId] = [addInvestigationAndManagmentBtn.dataset?.visitid, '#'+div.querySelector('.prescriptionTable').id]
                if (!resourcevalues){const message = {"resource": ["Please pick an from the list"]}; handleValidationErrors(message, div); addInvestigationAndManagmentBtn.removeAttribute('disabled'); return}
                let data = {...getDivData(div), ...resourcevalues, visitId}
                http.post(`prescription/${resourcevalues.resource}`, {...data}, {"html": div})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        new Toast(div.querySelector('#saveInvestigationAndManagementToast'), {delay:2000}).show()
                        clearDivValues(div)
                        clearValidationErrors(div)
                        clearItemsList(div.querySelector(`#resourceList${div.dataset.div}`))
                    }
                    if ($.fn.DataTable.isDataTable( divPrescriptionTableId )){
                        $(divPrescriptionTableId).dataTable().fnDraw(false)
                    }

                    div.querySelector('#quantity').value = 1
                    addInvestigationAndManagmentBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    addInvestigationAndManagmentBtn.removeAttribute('disabled')
                    console.log(error)
                }) 
            })
    

    document.querySelectorAll('#medicationPrescriptionsModal, #otherPrescriptionsModal, #investigationAndManagementModal, #bulkRequestModal, #vitalsignsModal, #ancVitalsignsModal, #chartMedicationModal, #wardAndBedModal').forEach(modal => {
            modal.addEventListener('hide.bs.modal', function(event) {
            inPatientsVisitTable.draw()
            waitingTable.draw()
            outPatientsVisitTable ? outPatientsVisitTable.draw(false) : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
            bulkRequestsTable ? bulkRequestsTable.draw(false) : ''
            emergencyTable ? emergencyTable.draw(false) : ''
            medicationsTable ? medicationsTable.draw(false) : ''
            upcomingMedicationsTable.draw(false)
            upcomingNursingChartsTable.draw(false)
            shiftPerformance()
        })
    })

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal, #consultationHistoryModal, #viewShiftReportTemplateModal, #editShiftReportTemplateModal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
            visitHistoryDiv.innerHTML = ''
            inPatientsVisitTable.draw()
            outPatientsVisitTable ? outPatientsVisitTable.draw(false) : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
            bulkRequestsTable ? bulkRequestsTable.draw(false) : ''
            emergencyTable ? emergencyTable.draw(false) : ''
            upcomingMedicationsTable.draw()
            upcomingNursingChartsTable.draw()
            shiftPerformance()
            nursesShiftReportTable.draw()
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
                            $('#' + tableId).dataTable().fnDraw(false)
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
                                    $('#' + table.id).dataTable().fnDraw(false)
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

    document.querySelectorAll('#medicationChartTable, #nursingChartTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn = event.target.closest('.deleteBtn')
            if (deleteBtn) {
                deleteBtn.setAttribute('disabled', 'disabled')
                const [url, treatmentTableId] = table.id === 'medicationChartTable' ? ['medicationchart', saveMedicationChartBtn.getAttribute('data-table')] : ['nursingchart', savePrescriptionChartBtn.getAttribute('data-table')]
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/${url}/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable('#' + table.id)) {
                                    $('#' + table.id).dataTable().fnDraw()
                                }
                                if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                    $('#' + treatmentTableId).dataTable().fnDraw(false)
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
                http.get(`/bulkrequests/list/bulk`, {params: {resource: itemInput.value, dept: itemInput.dataset.dept}}).then((response) => {
                    displayItemsList(datalistEl, response.data, 'itemOption')
                })
            }
    })

    requestBulkBtn.addEventListener('click', function () {
        requestBulkBtn.setAttribute('disabled', 'disabled')
        const itemId    =  getDatalistOptionId(bulkRequestModal._element, itemInput, bulkRequestModal._element.querySelector(`#itemList`))
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
            const message = {"quantity": ["This quantity is more than the available stock, please reduce the quantity"]}               
            handleValidationErrors(message, bulkRequestModal._element)
            requestBulkBtn.removeAttribute('disabled')
            return
        } else {clearValidationErrors(bulkRequestModal._element)}
        http.post(`/bulkrequests/${itemId}`, getDivData(bulkRequestModal._element), {"html": bulkRequestModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(bulkRequestModal._element.querySelector('.valuesDiv'))
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
    document.querySelectorAll('#treatmentDiv, #medicationPrescriptionsModal, #otherPrescriptionsModal, #visitHistoryDiv, #emergencyTable, #wardAndBedModal').forEach(div => {
        div.addEventListener('click', function (event) {
            const collapseConsultationBtn   = event.target.closest('.collapseConsultationBtn')
            const collapseVisitBtn          = event.target.closest('.collapseVisitBtn')
            const giveMedicationBtn         = event.target.closest('#giveMedicationBtn')
            const chartMedicationBtn        = event.target.closest('#chartMedicationBtn')
            const chartPrescriptionBtn      = event.target.closest('#chartPrescriptionBtn')
            const deleteGivenBtn            = event.target.closest('#deleteGivenBtn')
            const deleteServiceBtn          = event.target.closest('#deleteServiceBtn')
            const discontinueBtn            = event.target.closest('.discontinueBtn')
            const reportServiceBtn          = event.target.closest('#reportServiceBtn')
            const viewer                    = 'nurse'
    
            if (collapseConsultationBtn) {
                const gotoDiv              = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
                const investigationTableId = gotoDiv.querySelector('.investigationTable').id
                const medicationsTableId        = gotoDiv.querySelector('.nurseTreatmentTable').id
                const otherPrescriptionsTableId = gotoDiv.querySelector('.otherPrescriptionsNursesTable').id
                const conId     = gotoDiv.querySelector('.investigationTable').dataset.id
                const isHistory = +collapseConsultationBtn.getAttribute('data-ishistory')
    
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
                }
                setTimeout(goto, 300)
            }

            if (collapseVisitBtn) {
                const visitId               = collapseVisitBtn.getAttribute('data-id')
                const ancRegId              = collapseVisitBtn.getAttribute('data-ancregid')
                const [getVitalsigns, id]   = collapseVisitBtn.getAttribute('data-isanc') ? [getAncVitalSignsTable, ancRegId] : [getVitalSignsTableByVisit, visitId]

                if ($.fn.DataTable.isDataTable('#vitalSignsHistory'+visitId)){$('#vitalSignsHistory'+visitId).dataTable().fnDestroy()}
                if ($.fn.DataTable.isDataTable('#deliveryNoteTableHistory'+visitId)){$('#deliveryNoteTableHistory'+visitId).dataTable().fnDestroy()}
                if ($.fn.DataTable.isDataTable('#surgeryNoteTableHistory'+visitId)){$('#surgeryNoteTableHistory'+visitId).dataTable().fnDestroy()}
                if ($.fn.DataTable.isDataTable('#patientsFileTableHistory'+visitId)){$('#patientsFileTableHistory'+visitId).dataTable().fnDestroy()}
                if ($.fn.DataTable.isDataTable('#billingTableHistory'+visitId)){$('#billingTableHistory'+visitId).dataTable().fnDestroy()}

                const goto = () => {
                    location.href = collapseVisitBtn.getAttribute('data-gotovisit')
                    window.history.replaceState({}, document.title, "/" + "nurses" )
                    getVitalsigns('#vitalSignsHistory'+visitId, id, consultationHistoryModal)
                    getDeliveryNoteTable('deliveryNoteTableHistory'+visitId, visitId, false, consultationHistoryModal._element)
                    getSurgeryNoteTable('surgeryNoteTableHistory'+visitId, visitId, false, consultationHistoryModal._element)
                    getPatientsFileTable('patientsFileTableHistory'+visitId, visitId, consultationHistoryModal._element)
                    getbillingTableByVisit('billingTableHistory'+visitId, visitId, consultationHistoryModal._element)
                }
                setTimeout(goto, 500)
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
                chartPrescriptionModal._element.querySelector('#instruction').value = chartPrescriptionBtn.getAttribute('data-prescription')
                chartPrescriptionModal._element.querySelector('#prescribedBy').value = chartPrescriptionBtn.getAttribute('data-prescribedBy')
                chartPrescriptionModal._element.querySelector('#prescribed').value = chartPrescriptionBtn.getAttribute('data-prescribed')
                savePrescriptionChartBtn.setAttribute('data-id', prescriptionId)
                savePrescriptionChartBtn.setAttribute('data-table', tableId)
                savePrescriptionChartBtn.setAttribute('data-consultation', conId)
                savePrescriptionChartBtn.setAttribute('data-visit', visitId)
    
                getPrescriptionChartByPrescription(nursingChartTable.id, prescriptionId, chartPrescriptionModal._element)
    
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

            if (deleteServiceBtn){
                deleteServiceBtn.setAttribute('disabled', 'disabled')
                const treatmentTableId = deleteServiceBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete this Information?')) {
                    const id = deleteServiceBtn.getAttribute('data-id')
                    http.patch(`/nursingchart/removeservice/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                $('#' + treatmentTableId).dataTable().fnDraw()
                            }
                        }
                        deleteServiceBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteServiceBtn.removeAttribute('disabled')
                    })
                }
                deleteServiceBtn.removeAttribute('disabled')
            }

            if (discontinueBtn){
                if (confirm('Are you sure you want to discontinue prescription?')) {
                    const prescriptionId = discontinueBtn.getAttribute('data-id')
                    const treatmentTableId = discontinueBtn.getAttribute('data-table')
                    http.patch(`/prescription/${prescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                $('#' + treatmentTableId).dataTable().fnDraw(false)
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
        })
    })

    saveMedicationChartBtn.addEventListener('click', function () {
        const prescriptionId = saveMedicationChartBtn.getAttribute('data-id')
        const treatmentTableId = saveMedicationChartBtn.getAttribute('data-table')
        const conId = saveMedicationChartBtn.getAttribute('data-consultation') == 'null' ? '' : saveMedicationChartBtn.getAttribute('data-consultation')
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
                        $('#' + medicationChartTable.id).dataTable().fnDraw(false)
                    }

                    if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                        $('#' + treatmentTableId).dataTable().fnDraw(false)
                    }

                }
                saveMedicationChartBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                saveMedicationChartBtn.removeAttribute('disabled')
                console.log(error)
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

                    if ($.fn.DataTable.isDataTable('#' + nursingChartTable.id)) {
                        $('#' + nursingChartTable.id).dataTable().fnDraw(false)
                    }

                    if ($.fn.DataTable.isDataTable('#' + otherPrescriptionsTableId)) {
                        $('#' + otherPrescriptionsTableId).dataTable().fnDraw(false)
                    }

                }
                savePrescriptionChartBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                savePrescriptionChartBtn.removeAttribute('disabled')
                console.log(error)
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
                        $('#' + treatmentTableId).dataTable().fnDraw(false)
                    }
                }
                saveGivenMedicationBtn.removeAttribute('disabled')
                giveMedicationModal.hide()
            })
            .catch((error) => {
                saveGivenMedicationBtn.removeAttribute('disabled')
                console.log(error)
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
                        $('#' + otherPrescriptionTableId).dataTable().fnDraw(false)
                    }
                }
                saveServiceDoneBtn.removeAttribute('disabled')
                serviceDoneModal.hide()
            })
            .catch((error) => {
                saveServiceDoneBtn.removeAttribute('disabled')
                console.log(error)
            })
    })

    createDeliveryNoteBtn.addEventListener('click', function () {
        createDeliveryNoteBtn.setAttribute('disabled', 'disabled')
        const visitId = createDeliveryNoteBtn.dataset.visitid

        let data = { ...getDivData(newDeliveryNoteModal._element), visitId }
        http.post('/deliverynote', {...data}, {"html": newDeliveryNoteModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newDeliveryNoteModal.hide(); clearDivValues(newDeliveryNoteModal._element); clearValidationErrors(newDeliveryNoteModal._element)
                if ($.fn.DataTable.isDataTable('#deliveryNoteTable')) {$('#deliveryNoteTable').dataTable().fnDraw()}
            }
            createDeliveryNoteBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createDeliveryNoteBtn.removeAttribute('disabled')
            console.log(error)
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
            console.log(error)
        })
    })

    createShiftReportBtn.addEventListener('click', function() {
        const report    = newShiftReportTemplateModal._element.querySelector('#report')
        const shift     = newShiftReportTemplateModal._element.querySelector('#shift')
        createShiftReportBtn.setAttribute('disabled', 'disabled')
        http.post(`shiftreport`, {
            report: report.value, 
            department: newShiftReportTemplateModal._element.querySelector('#department').value,
            shift:  shift.value,
        }, 
            {'html': newShiftReportTemplateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                newShiftReportTemplateModal.hide()
                report.value = ''; shift.value = '';
                clearValidationErrors(newShiftReportTemplateModal._element)
                nursesShiftReportTable.draw(false)
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
                nursesShiftReportTable.draw(false)
            }
            saveShiftReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveShiftReportBtn.removeAttribute('disabled')
        })
    })

    document.querySelectorAll('#nursesShiftReportTable').forEach(table => {
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
                                nursesShiftReportTable.draw(false)
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
}

function displayResourceList(datalistEl, data) {
    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', 'resourceOption')
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)
        option.setAttribute('data-cat', line.category)
        option.setAttribute('data-plainname', line.plainName)

        !datalistEl.options.namedItem(line.name) ? datalistEl.appendChild(option) : ''
    })
}
