import { Offcanvas, Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getOrdinal, getDivData, clearValidationErrors, loadingSpinners, removeDisabled, displayList, getPatientSponsorDatalistOptionId} from "./helpers"
import { getAllHmoPatientsVisitTable, getApprovalListTable, getVerificationTable, getVisitPrescriptionsTable, getWaitingTable } from "./tables/hmoTables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getLabTableByConsultation, getTreatmentTableByConsultation, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { getVitalsignsChartByVisit } from "./charts/vitalsignsCharts";
import { getbillingTableByVisit } from "./tables/billingTables";
import { getAncVitalSignsTable } from "./tables/nursesTables";


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas         = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
    const hmoApprovalListCanvas        = new Offcanvas(document.getElementById('hmoApprovalListOffcanvas'))
    const nhisApprovalListCanvas        = new Offcanvas(document.getElementById('nhisApprovalListOffcanvas'))

    const treatmentDetailsModal     = new Modal(document.getElementById('treatmentDetailsModal'))
    const ancTreatmentDetailsModal  = new Modal(document.getElementById('ancTreatmentDetailsModal'))
    const approvalModal             = new Modal(document.getElementById('approvalModal'))
    const verifyModal               = new Modal(document.getElementById('verifyModal'))
    const investigationsModal       = new Modal(document.getElementById('investigationsModal'))
    const makeBillModal             = new Modal(document.getElementById('makeBillModal'))
    const changeSponsorModal        = new Modal(document.getElementById('changeSponsorModal'))

    const codeTextDiv               = verifyModal._element.querySelector('#codeTextDiv')
    const sponsorDetailsDiv         = changeSponsorModal._element.querySelector('#sponsorDetailsDiv')
    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')
    
    const waitingBtn                = document.querySelector('#waitingBtn')
    const hmoApprovalListBtn        = document.querySelector('#hmoApprovalListBtn')
    const nhisApprovalListBtn       = document.querySelector('#nhisApprovalListBtn')
    const verifyBtn                 = verifyModal._element.querySelector('#verifyBtn')
    const saveNewSponsorBtn         = changeSponsorModal._element.querySelector('#saveNewSponsorBtn')
    
    const verificationTab           = document.querySelector('#nav-verifyPatients-tab')
    const treatmentsTab             = document.querySelector('#nav-treatments-tab')
    const billPatientsTab                   = document.querySelector('#nav-billpatients-tab')
    const reportsTab                        = document.querySelector('#nav-reports-tab')
    const newSponsorCategoryInput           = document.querySelector('#newSponsorCategory')
    const newPatientSponsorInputEl          = document.querySelector('#newPatientSponsor')
    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')

    const filterListOption          = document.querySelector('#filterList')


    const waitingTable = getWaitingTable('waitingTable')
    const verificationTable = getVerificationTable('verificationTable')
    const hmoApprovalListTable = getApprovalListTable('hmoApprovalListTable')
    const nhisApprovalListTable = getApprovalListTable('nhisApprovalListTable', 'NHIS')
    let hmotreatmentsTable, visitPrescriptionsTable

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    hmoApprovalListBtn.addEventListener('click', function () {
        hmoApprovalListTable.draw()
    })

    nhisApprovalListBtn.addEventListener('click', function () {
        nhisApprovalListTable.draw()
    })

    verificationTab.addEventListener('click', function() {verificationTable.draw()})

    treatmentsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDraw()
        } else {
            hmotreatmentsTable = getAllHmoPatientsVisitTable('#hmoTreatmentsTable')
        }
    })

    hmoApprovalListCanvas._element.addEventListener('hide.bs.offcanvas', function() {
        verificationTable.draw()
        hmotreatmentsTable ? hmotreatmentsTable.draw() : ''
    })

    nhisApprovalListCanvas._element.addEventListener('hide.bs.offcanvas', function() {
        verificationTable.draw()
        hmotreatmentsTable ? hmotreatmentsTable.draw() : ''
    })
    
    document.querySelector('#hmoTreatmentsTable').addEventListener('click', function (event) {
            const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
            const patientBillBtn            = event.target.closest('.patientBillBtn')
            const investigationsBtn         = event.target.closest('.investigationsBtn')
            const viewer                    = 'hmo'
    
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
    
                            openHmoModals(modal, div, patientBio)
    
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
    
            if (patientBillBtn) {
                const tableId = '#' + makeBillModal._element.querySelector('.visitPrescriptionsTable').id
                const visitId = patientBillBtn.getAttribute('data-id')
                makeBillModal._element.querySelector('#patient').value = patientBillBtn.getAttribute('data-patient')
                makeBillModal._element.querySelector('#sponsor').value = patientBillBtn.getAttribute('data-sponsor')
                makeBillModal._element.querySelector('#markSentBtn').setAttribute('data-id', visitId)
    
                visitPrescriptionsTable = getVisitPrescriptionsTable(tableId, visitId, makeBillModal)
                makeBillModal.show()
            }

            if (investigationsBtn) {
                investigationsBtn.setAttribute('disabled', 'disabled')
                const tableId = investigationsModal._element.querySelector('.investigationsTable').id
                const visitId = investigationsBtn.getAttribute('data-id')
                investigationsModal._element.querySelector('#patient').value = investigationsBtn.getAttribute('data-patient')
                investigationsModal._element.querySelector('#sponsorName').value = investigationsBtn.getAttribute('data-sponsor')
    
                getLabTableByConsultation(tableId, investigationsModal._element, viewer, null, visitId)
    
                investigationsModal.show()
                investigationsBtn.removeAttribute('disabled')
            }

            // if (closeBtn) {

            // }
        })

    document.querySelectorAll('#hmoApprovalListTable, #nhisApprovalListTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const approveBtn        = event.target.closest('.approveBtn')
            const rejectBtn         = event.target.closest('.rejectBtn')
            const resetBtn          = event.target.closest('.resetBtn')
            const approvedByBtn     = this.querySelector('.approvedBy')
            const approvalFieldset  = this.id == 'hmoApprovalListTable' ? document.querySelector('#hmoApprovalFieldset') :  document.querySelector('#nhisApprovalFieldset')
            const table             = this.id == 'hmoApprovalListTable' ? hmoApprovalListTable : nhisApprovalListTable
        
            if (approveBtn) {
                    const prescriptionId = approveBtn.getAttribute('data-id')
                    approveBtn.classList.add('d-none')
                    approveBtn.parentElement.querySelector('.rejectBtn').classList.add('d-none')
                    const noteInput = approveBtn.parentElement.querySelector('.noteInput')
                    noteInput.classList.remove('d-none')
                    noteInput.focus()
                    noteInput.addEventListener('blur', function() {
                        approvalFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/hmo/approve/${prescriptionId}`, {note: noteInput.value})
                        .then((response) => {
                            if (response.status == 200) {
                                table.draw()
                                table.on('draw', removeDisabled(approvalFieldset))                        
                            }
                            if (response.status == 222){
                                table.draw()
                                alert(response.data)
                                table.on('draw', removeDisabled(approvalFieldset))
                                
                            }
                        })
                            .catch((error) => {
                                console.log(error.response.data)
                                alert(error.response.data)
                                table.on('draw', removeDisabled(approvalFieldset))
                                
                            })
                        })
                         
                }
        
                if (rejectBtn) {
                    const prescriptionId = rejectBtn.getAttribute('data-id')
                    rejectBtn.classList.add('d-none')
                    rejectBtn.parentElement.querySelector('.approveBtn').classList.add('d-none')
                    const noteInput = rejectBtn.parentElement.querySelector('.noteInput')
                    noteInput.classList.remove('d-none')
                    noteInput.focus()
                    noteInput.addEventListener('blur', function() {
                        approvalFieldset.setAttribute('disabled', 'disabled')
                        if (noteInput.value) {
                            http.patch(`/hmo/reject/${prescriptionId}`, {note: noteInput.value})
                            .then((response) => {
                                if (response.status >= 200 || response.status <= 300) {
                                    table.draw()
                                    table.on('draw', removeDisabled(approvalFieldset))
                                }
                            })
                            .catch((error) => {
                                console.log(error)
                                rejectBtn.removeAttribute('disabled')
                                table.draw()
                                table.on('draw', removeDisabled(approvalFieldset))
                            })
                        } else{
                            table.draw()
                            table.on('draw', removeDisabled(approvalFieldset))
                        }
                    })
                }

                if (resetBtn){
                    const prescriptionId = resetBtn.getAttribute('data-id')
                    approvedByBtn.innerHTML = 'Wait...'
                    http.patch(`/hmo/reset/${prescriptionId}`)
                            .then((response) => {
                                if (response.status >= 200 || response.status <= 300) {
                                    table.draw()
                                    table.on('draw', removeDisabled(approvalFieldset))
                                }
                            })
                            .catch((error) => {
                                console.log(error)
                                resetBtn.removeAttribute('disabled')
                                table.draw()
                                table.on('draw', removeDisabled(approvalFieldset))
                            })
                }
            })
    })

    filterListOption.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDestroy()
        }
        getAllHmoPatientsVisitTable('#hmoTreatmentsTable', filterListOption.value)
    })

    document.querySelector('#verificationTable').addEventListener('click', function (event) {
        const verifyPatientBtn = event.target.closest('.verifyPatientBtn')
        const changeSponsorBtn = event.target.closest('.changeSponsorBtn')

        if (verifyPatientBtn) {
            verifyBtn.setAttribute('data-id', verifyPatientBtn.getAttribute('data-id'))
            verifyBtn.setAttribute('data-table', verifyPatientBtn.getAttribute('data-table'))
            verifyModal._element.querySelector('#patientId').value = verifyPatientBtn.getAttribute('data-patient')
            verifyModal._element.querySelector('#sponsorName').value = verifyPatientBtn.getAttribute('data-sponsor')
            verifyModal._element.querySelector('#staffId').value = verifyPatientBtn.getAttribute('data-staffid')
            verifyModal._element.querySelector('#phoneNumber').value = verifyPatientBtn.getAttribute('data-phone')
            verifyModal.show()
        }

        if (changeSponsorBtn){
            saveNewSponsorBtn.setAttribute('data-id', changeSponsorBtn.getAttribute('data-id'))
            saveNewSponsorBtn.setAttribute('data-table', changeSponsorBtn.getAttribute('data-table'))
            changeSponsorModal._element.querySelector('#patientId').value = changeSponsorBtn.getAttribute('data-patient')
            changeSponsorModal._element.querySelector('#sponsorName').value = changeSponsorBtn.getAttribute('data-sponsor')
            changeSponsorModal._element.querySelector('#staffId').value = changeSponsorBtn.getAttribute('data-staffid')
            changeSponsorModal._element.querySelector('#phoneNumber').value = changeSponsorBtn.getAttribute('data-phone')
            changeSponsorModal.show()
        }
    })

    verifyBtn.addEventListener('click', function () {
        verifyBtn.setAttribute('disabled', 'disabled')
        const visitId = verifyBtn.getAttribute('data-id')

        let data = { ...getDivData(codeTextDiv), visitId }

        http.post(`/hmo/verify/${visitId}`,  { ...data }, { "html": codeTextDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {

                clearDivValues(codeTextDiv)
                clearValidationErrors(codeTextDiv)

            }
            verifyBtn.removeAttribute('disabled')
            verifyModal.hide()
        })
        .catch((error) => {
            console.log(error)
            verifyBtn.removeAttribute('disabled')
        })
    })

    newSponsorCategoryInput.addEventListener('change', function() {
        if (newSponsorCategoryInput.value) {
            http.get(`/sponsorcategory/list_sponsors/${newSponsorCategoryInput.value}`, {params: {category: newSponsorCategoryInput.value}})
            .then((response) => {
                    displayList(newPatientSponsorDatalistEl, 'sponsorOption', response.data)
            })
        }
    })

    saveNewSponsorBtn.addEventListener('click', function () {
        saveNewSponsorBtn.setAttribute('disabled', 'disabled')
        const visitId = saveNewSponsorBtn.getAttribute('data-id')
        const sponsor = getPatientSponsorDatalistOptionId(changeSponsorModal, newPatientSponsorInputEl, newPatientSponsorDatalistEl)

        let data = { ...getDivData(sponsorDetailsDiv), sponsor }

        http.patch(`/visits/changesponsor/${visitId}`,  { ...data }, { "html": sponsorDetailsDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(sponsorDetailsDiv)
                clearValidationErrors(sponsorDetailsDiv)
                verificationTable.draw()
            }
            saveNewSponsorBtn.removeAttribute('disabled')
            changeSponsorModal.hide()
        })
        .catch((error) => {
            console.log(error)
            saveNewSponsorBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const removeBtn  = event.target.closest('.removeBtn')

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
                    console.log(error)
                    removeBtn.removeAttribute('disabled')
                })
            }  
        }
    
    })

    document.querySelector('#visitPrescriptionsTable').addEventListener('click', function (event) {
        const hmoBillSpan       = event.target.closest('.hmoBillSpan')
        const makeBillFieldset  = document.querySelector('#makeBillFieldset')
        if (hmoBillSpan){
            const prescriptionId    = hmoBillSpan.getAttribute('data-id')
            const hmoBillInput      = hmoBillSpan.parentElement.querySelector('.hmoBillInput')
            hmoBillSpan.classList.add('d-none')
            hmoBillInput.classList.remove('d-none')
            hmoBillInput.focus()
            
            hmoBillInput.addEventListener('blur', function () {
                makeBillFieldset.setAttribute('disabled', 'disabled')
                http.patch(`/hmo/bill/${prescriptionId}`, {bill: hmoBillInput.value})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        visitPrescriptionsTable ?  visitPrescriptionsTable.draw() : ''
                        visitPrescriptionsTable.on('draw', removeDisabled(makeBillFieldset))
                    }
                })
                .catch((error) => {
                    console.log(error)
                    visitPrescriptionsTable ?  visitPrescriptionsTable.draw() : ''
                    visitPrescriptionsTable.on('draw', removeDisabled(makeBillFieldset))
                })                
            })
        }
    })

    // treatmentDetailsModal._element.addEventListener('hide.bs.modal', function () {
    //     treatmentDiv.innerHTML = ''
    // })

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
        })
    })

    verifyModal._element.addEventListener('hide.bs.modal', function () {
        verificationTable.draw()
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        verificationTable.draw()
        hmotreatmentsTable ?hmotreatmentsTable.draw() : ''
    })

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseConsultationBtn = event.target.closest('.collapseConsultationBtn')
        const approvalBtn = event.target.closest('#approvalBtn')
        const viewer = 'hmo'

        if (collapseConsultationBtn) {
            const gotoDiv = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
            const investigationTableId = gotoDiv.querySelector('.investigationTable').id
            const treatmentTableId = gotoDiv.querySelector('.treatmentTable').id
            const conId = gotoDiv.querySelector('.investigationTable').dataset.id

            if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                $('#' + investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                $('#' + treatmentTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseConsultationBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "hmo")
                getLabTableByConsultation(investigationTableId, reviewDetailsModal._element, viewer, conId, null)
                getTreatmentTableByConsultation(treatmentTableId, conId, reviewDetailsModal._element)
            }
            setTimeout(goto, 300)
        }
    })

})

function openHmoModals(modal, button, { id, visitId, ancRegId, patientType, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}