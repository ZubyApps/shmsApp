import { Offcanvas, Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getOrdinal, getDivData, clearValidationErrors, loadingSpinners} from "./helpers"
import { getAllHmoPatientsVisitTable, getApprovalListTable, getVerificationTable, getVisitPrescriptionsTable, getWaitingTable } from "./tables/hmoTables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getLabTableByConsultation, getTreatmentTableByConsultation, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { getVitalsignsChartByVisit } from "./charts/vitalsignsCharts";


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas         = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
    const ApprovaleListCanvas       = new Offcanvas(document.getElementById('approvalListOffcanvas'))

    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const approvalModal             = new Modal(document.getElementById('approvalModal'))
    const verifyModal               = new Modal(document.getElementById('verifyModal'))
    const investigationsModal       = new Modal(document.getElementById('investigationsModal'))
    const makeBillModal             = new Modal(document.getElementById('makeBillModal'))

    const codeTextDiv               = verifyModal._element.querySelector('#codeTextDiv')
    const treatmentDiv              = document.querySelector('#treatmentDiv')
    const approveDiv                = document.querySelector('#approveDiv')
    
    const waitingBtn                = document.querySelector('#waitingBtn')
    const approvalListBtn           = document.querySelector('#approvalListBtn')
    const verifyBtn                 = verifyModal._element.querySelector('#verifyBtn')
    
    const verificationTab           = document.querySelector('#nav-verifyPatients-tab')
    const treatmentsTab             = document.querySelector('#nav-treatments-tab')
    const billPatientsTab                  = document.querySelector('#nav-billpatients-tab')
    const reportsTab                = document.querySelector('#nav-reports-tab')

    const filterListOption          = document.querySelector('#filterList')


    const waitingTable = getWaitingTable('waitingTable')
    const verificationTable = getVerificationTable('verificationTable')
    const approvalListTable = getApprovalListTable('approvalListTable')
    let hmotreatmentsTable, visitPrescriptionsTable

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    approvalListBtn.addEventListener('click', function () {
        approvalListTable.draw()
    })

    verificationTab.addEventListener('click', function() {verificationTable.draw()})

    treatmentsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDraw()
        } else {
            hmotreatmentsTable = getAllHmoPatientsVisitTable('#hmoTreatmentsTable')
        }
    })
    
    document.querySelector('#hmoTreatmentsTable').addEventListener('click', function (event) {
            const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
            const patientBillBtn            = event.target.closest('.patientBillBtn')
            const investigationsBtn         = event.target.closest('.investigationsBtn')

            const viewer = 'hmo'
    
            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
    
                const visitId = consultationDetailsBtn.getAttribute('data-id')
                const patientType = consultationDetailsBtn.getAttribute('data-patientType')
    
                http.get(`/consultation/consultations/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            let iteration = 0
                            let count = 0
    
                            const consultations = response.data.consultations.data
                            const patientBio = response.data.bio
    
                            openHmoModals(reviewDetailsModal, treatmentDiv, patientBio)
    
                            consultations.forEach(line => {
                                iteration++
    
                                iteration > 1 ? count++ : ''
    
                                if (patientType === 'ANC') {
                                    treatmentDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                                } else {
                                    treatmentDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                                }
                            })
    
                            getVitalSignsTableByVisit('#vitalSignsTableNurses', visitId, reviewDetailsModal, viewer)
                            reviewDetailsModal.show()
    
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
                investigationsModal._element.querySelector('#sponsor').value = investigationsBtn.getAttribute('data-sponsor')
    
                getLabTableByConsultation(tableId, investigationsModal._element, viewer, null, visitId)
    
                investigationsModal.show()
                investigationsBtn.removeAttribute('disabled')
            }
    
        })

    document.querySelector('#approvalListTable').addEventListener('click', function (event) {
        const approveBtn    = event.target.closest('.approveBtn')
        const rejectBtn     = event.target.closest('.rejectBtn')

        if (approveBtn) {
            const prescriptionId = approveBtn.getAttribute('data-id')
            approveBtn.classList.add('d-none')
            approveBtn.parentElement.querySelector('.rejectBtn').classList.add('d-none')
            const noteInput = approveBtn.parentElement.querySelector('.noteInput')
            noteInput.classList.remove('d-none')
            noteInput.focus()
            noteInput.addEventListener('blur', function() {
                http.patch(`/hmo/approve/${prescriptionId}`, {note: noteInput.value})
                .then((response) => {
                    if (response.status == 200) {
                        approveBtn.removeAttribute('disabled')
                        approvalListTable.draw()
                    }
                    if (response.status == 222){
                        approvalListTable.draw()
                        alert(response.data)
                    }
                })
                    .catch((error) => {
                        console.log(error.response.data)
                        alert(error.response.data)
                        approvalListTable.draw()
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
                if (noteInput.value) {
                    http.patch(`/hmo/reject/${prescriptionId}`, {note: noteInput.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            rejectBtn.removeAttribute('disabled')
                            approvalListTable.draw()
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        approvalListTable.draw()
                        rejectBtn.removeAttribute('disabled')
                    })
                } else{approvalListTable.draw()}
            })
            
        }
    })

    filterListOption.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDestroy()
        }
        getAllHmoPatientsVisitTable('#hmoTreatmentsTable', filterListOption.value)
    })

    document.querySelector('#verificationTable').addEventListener('click', function (event) {
        const verifyPatientBtn = event.target.closest('.verifyPatientBtn')

        if (verifyPatientBtn) {
            verifyBtn.setAttribute('data-id', verifyPatientBtn.getAttribute('data-id'))
            verifyBtn.setAttribute('data-table', verifyPatientBtn.getAttribute('data-table'))
            verifyModal._element.querySelector('#patientId').value = verifyPatientBtn.getAttribute('data-patient')
            verifyModal._element.querySelector('#sponsorName').value = verifyPatientBtn.getAttribute('data-sponsor')
            verifyModal._element.querySelector('#staffId').value = verifyPatientBtn.getAttribute('data-staffid')
            verifyModal._element.querySelector('#phoneNumber').value = verifyPatientBtn.getAttribute('data-phone')
            verifyModal.show()
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
        const hmoBillSpan = event.target.closest('.hmoBillSpan')

        if (hmoBillSpan){
            const prescriptionId    = hmoBillSpan.getAttribute('data-id')
            const hmoBillInput      = hmoBillSpan.parentElement.querySelector('.hmoBillInput')
            hmoBillSpan.classList.add('d-none')
            hmoBillInput.classList.remove('d-none')
            hmoBillInput.focus()
            
            hmoBillInput.addEventListener('blur', function () {
                http.patch(`/hmo/bill/${prescriptionId}`, {bill: hmoBillInput.value})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        visitPrescriptionsTable.draw()
                    }
                })
                .catch((error) => {
                    console.log(error)
                    visitPrescriptionsTable.draw()
                })                
            })
        }
    })

    reviewDetailsModal._element.addEventListener('hide.bs.modal', function () {
        treatmentDiv.innerHTML = ''
    })

    verifyModal._element.addEventListener('hide.bs.modal', function () {
        verificationTable.draw()
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        verificationTable.draw()
        hmotreatmentsTable ?hmotreatmentsTable.draw() : ''
    })

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseBtn = event.target.closest('.collapseBtn')
        const approvalBtn = event.target.closest('#approvalBtn')
        const viewer = 'hmo'

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const investigationTableId = gotoDiv.querySelector('.investigationTable').id
            console.log(investigationTableId)
            const treatmentTableId = gotoDiv.querySelector('.treatmentTable').id
            console.log(treatmentTableId)
            const conId = gotoDiv.querySelector('.investigationTable').dataset.id

            if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                $('#' + investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                $('#' + treatmentTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "hmo")
                getLabTableByConsultation(investigationTableId, reviewDetailsModal._element, viewer, conId, null)
                getTreatmentTableByConsultation(treatmentTableId, conId, reviewDetailsModal._element)
            }
            setTimeout(goto, 300)
        }
    })

})

function openHmoModals(modal, button, { id, visitId, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}