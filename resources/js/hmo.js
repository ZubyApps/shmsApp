import { Offcanvas, Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getOrdinal, getDivData, clearValidationErrors, loadingSpinners, removeDisabled, displayList, getPatientSponsorDatalistOptionId, resetFocusEndofLine, displayMedicalReportModal} from "./helpers"
import { getAllHmoPatientsVisitTable, getApprovalListTable, getHmoReconciliationTable, getHmoReportsTable, getSentBillsTable, getVerificationTable, getVisitPrescriptionsTable, getWaitingTable } from "./tables/hmoTables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getLabTableByConsultation, getMedicalReportTable, getMedicationsByFilter, getOtherPrescriptionsByFilter, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { getVitalsignsChartByVisit } from "./charts/vitalsignsCharts";
import { getbillingTableByVisit } from "./tables/billingTables";
import { getAncVitalSignsTable, getOtherPrescriptionsByFilterNurses } from "./tables/nursesTables";
import html2pdf  from "html2pdf.js"


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas         = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
    const hmoApprovalListCanvas     = new Offcanvas(document.getElementById('hmoApprovalListOffcanvas'))
    const nhisApprovalListCanvas    = new Offcanvas(document.getElementById('nhisApprovalListOffcanvas'))

    const treatmentDetailsModal     = new Modal(document.getElementById('treatmentDetailsModal'))
    const ancTreatmentDetailsModal  = new Modal(document.getElementById('ancTreatmentDetailsModal'))
    const verifyModal               = new Modal(document.getElementById('verifyModal'))
    const investigationsModal       = new Modal(document.getElementById('investigationsModal'))
    const labResultModal            = new Modal(document.getElementById('labResultModal'))
    const makeBillModal             = new Modal(document.getElementById('makeBillModal'))
    const changeSponsorModal        = new Modal(document.getElementById('changeSponsorModal'))
    const reconciliationModal       = new Modal(document.getElementById('reconciliationModal'))
    const medicalReportListModal    = new Modal(document.getElementById('medicalReportListModal'))
    const viewMedicalReportModal    = new Modal(document.getElementById('viewMedicalReportModal'))

    const codeTextDiv               = verifyModal._element.querySelector('#codeTextDiv')
    const sponsorDetailsDiv         = changeSponsorModal._element.querySelector('#sponsorDetailsDiv')
    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')
    
    const waitingBtn                = document.querySelector('#waitingBtn')
    const hmoApprovalListBtn        = document.querySelector('#hmoApprovalListBtn')
    const hmoApprovalListCount      = document.querySelector('#hmoApprovalListCount')
    const nhisApprovalListBtn       = document.querySelector('#nhisApprovalListBtn')
    const nhisApprovalListCount     = document.querySelector('#nhisApprovalListCount')
    const verifyBtn                 = verifyModal._element.querySelector('#verifyBtn')
    const saveNewSponsorBtn         = changeSponsorModal._element.querySelector('#saveNewSponsorBtn')
    const markAsSentBtn             = makeBillModal._element.querySelector('#markAsSentBtn')
    
    const verificationTab           = document.querySelector('#nav-verifyPatients-tab')
    const treatmentsTab             = document.querySelector('#nav-treatments-tab')
    const sentBillsTab              = document.querySelector('#nav-sentBills-tab')
    const hmoReportsTab             = document.querySelector('#nav-hmoReports-tab')
    const newSponsorCategoryInput           = document.querySelector('#newSponsorCategory')
    const newPatientSponsorInputEl          = document.querySelector('#newPatientSponsor')
    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')

    const filterListOption                  = document.querySelector('#filterList')
    const datesDiv                          = document.querySelector('.datesDiv')
    const searchWithDatesBtn                = document.querySelector('.searchWithDatesBtn')
    const reportDatesDiv                    = document.querySelector('.reportsDatesDiv')
    const searchReportsBtn                  = document.querySelector('.searchReportsBtn')
    const downloadReportBtn                 = viewMedicalReportModal._element.querySelector('#downloadReportBtn')

    const reportModalBody                   = viewMedicalReportModal._element.querySelector('.reportModalBody')
    const patientsFullName                  = viewMedicalReportModal._element.querySelector('#patientsFullName')
    const patientsInfo                      = viewMedicalReportModal._element.querySelector('#patientsInfo')

    const waitingTable = getWaitingTable('waitingTable')
    const verificationTable = getVerificationTable('verificationTable')
    const hmoApprovalListTable = getApprovalListTable('hmoApprovalListTable',null, hmoApprovalListCount)
    const nhisApprovalListTable = getApprovalListTable('nhisApprovalListTable', 'NHIS')
    let hmotreatmentsTable, visitPrescriptionsTable, sentBillsTable, hmoReportsTable, reconciliationTable, medicalReportTable, hmoIntervalId, nhisIntervalId

    hmoApprovalListTable.on('draw.init', function() {
        const count = hmoApprovalListTable.rows().count()
        if (count > 0 ){
            hmoApprovalListCount.innerHTML = count
        } else {
            hmoApprovalListCount.innerHTML = ''
        }
    })

    nhisApprovalListTable.on('draw.init', function() {
        const count = nhisApprovalListTable.rows().count()
        if (count > 0 ){
            nhisApprovalListCount.innerHTML = count
        } else {
            nhisApprovalListCount.innerHTML = ''
        }
    })

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    hmoApprovalListBtn.addEventListener('click', function () {
        hmoApprovalListTable.draw()
    })

    nhisApprovalListBtn.addEventListener('click', function () {
        nhisApprovalListTable.draw()
    })

    verificationTab.addEventListener('click', function() {
        verificationTable.draw()
        hmoApprovalListTable.draw()
        nhisApprovalListTable.draw()
    })

    treatmentsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDraw()
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        } else {
            hmotreatmentsTable = getAllHmoPatientsVisitTable('#hmoTreatmentsTable')
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        }
    })

    sentBillsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
            $('#sentBillsTable').dataTable().fnDraw()
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        } else {
            sentBillsTable = getSentBillsTable('#sentBillsTable')
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        }
    })

    hmoReportsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#hmoReportsTable' )){
            $('#hmoReportsTable').dataTable().fnDraw()
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        } else {
            hmoReportsTable = getHmoReportsTable('hmoReportsTable')
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        }
    })

    filterListOption.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDestroy()
        }
        hmotreatmentsTable = getAllHmoPatientsVisitTable('#hmoTreatmentsTable', filterListOption.value)
        hmoApprovalListTable.draw()
        nhisApprovalListTable.draw()
    })

    document.querySelectorAll('#hmoApprovalListOffcanvas, #nhisApprovalListOffcanvas, waitingListOffcanvas2').forEach(table => {
        table.addEventListener('hide.bs.offcanvas', function() {
            verificationTable.draw()
            hmotreatmentsTable ? hmotreatmentsTable.draw() : ''
            sentBillsTable ? sentBillsTable.draw() : ''
            hmoReportsTable ? hmoReportsTable.draw() : ''
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        })
    })
    
    document.querySelectorAll('#hmoTreatmentsTable, #sentBillsTable').forEach(table => {
        table.addEventListener('click', function (event) {
                const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
                const patientBillBtn            = event.target.closest('.patientBillBtn')
                const investigationsBtn         = event.target.closest('.investigationsBtn')
                const closeVisitBtn             = event.target.closest('.closeVisitBtn')
                const medicalReportBtn          = event.target.closest('.medicalReportBtn')
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
                    const hmoDoneBy = patientBillBtn.getAttribute('data-hmodoneby')
                    makeBillModal._element.querySelector('#patient').value = patientBillBtn.getAttribute('data-patient')
                    makeBillModal._element.querySelector('#sponsor').value = patientBillBtn.getAttribute('data-sponsor')
                    makeBillModal._element.querySelector('#markAsSentBtn').setAttribute('data-id', visitId)
                    makeBillModal._element.querySelector('#markAsSentBtn').innerHTML = hmoDoneBy === 'null' ? '<i class="bi bi-check-circle me-1"></i> Mark as Sent' : 'Sent (Unmark)'
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
    
                if (closeVisitBtn){
                    if (confirm('Are you sure you want to close this Visit?')) {
                        const visitId = closeVisitBtn.getAttribute('data-id')
                        http.patch(`/visits/close/${visitId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                waitingTable.draw()
                                hmotreatmentsTable ? hmotreatmentsTable.draw() : ''
                            }
                        })
                        .catch((error) => {
                            if (error.response.status === 403){
                                alert(error.response.data.message) 
                            }
                            console.log(error)
                        })
                    }
                }

                if (medicalReportBtn){
                    const visitId = medicalReportBtn.getAttribute('data-id')
                    medicalReportListModal._element.querySelector('#patient').value = medicalReportBtn.getAttribute('data-patient')
                    medicalReportListModal._element.querySelector('#sponsorName').value = medicalReportBtn.getAttribute('data-sponsor')
                    medicalReportListModal._element.querySelector('#age').value = medicalReportBtn.getAttribute('data-age')
                    medicalReportListModal._element.querySelector('#sex').value = medicalReportBtn.getAttribute('data-sex')
                    medicalReportTable = getMedicalReportTable('medicalReportTable', visitId, medicalReportListModal._element)
                    medicalReportListModal.show()

                }
            })
    })

    document.querySelector('#medicalReportTable').addEventListener('click', function (event) {
        const viewMedicalReportbtn = event.target.closest('.viewMedicalReportBtn')

        if (viewMedicalReportbtn) {
            viewMedicalReportbtn.setAttribute('disabled', 'disabled')
            http.get(`/medicalreports/display/${viewMedicalReportbtn.getAttribute('data-id')}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    displayMedicalReportModal(viewMedicalReportModal, response.data.data)
                }
            })
            .catch((error) => {
                console.log(error)
            })
            setTimeout(()=>{viewMedicalReportbtn.removeAttribute('disabled')}, 2000)
        }
    })

    patientsInfo.addEventListener('click', function(){patientsInfo.setAttribute('hidden', 'hidden'); patientsFullName.removeAttribute('hidden')})
    patientsFullName.addEventListener('click', function(){patientsFullName.setAttribute('hidden', 'hidden'); patientsInfo.removeAttribute('hidden')})

    downloadReportBtn.addEventListener('click', function () {
        const patientFullName = reportModalBody.querySelector('#patientsFullName').innerHTML
        const type = reportModalBody.querySelector('#type').innerHTML

        var opt = {
        margin:       0.5,
        filename:     patientFullName + `'s ${type}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(reportModalBody).save()
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

    searchWithDatesBtn.addEventListener('click', function () {
        if (!datesDiv.querySelector('#startDate').value && !datesDiv.querySelector('#endDate').value){
            return alert('Please pick valid dates')
        }
        if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
            $('#sentBillsTable').dataTable().fnDestroy()
        }
        getSentBillsTable('#sentBillsTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    searchReportsBtn.addEventListener('click', function () {
        if ((datesDiv.querySelector('#startDate').value && !datesDiv.querySelector('#endDate').value) || (!datesDiv.querySelector('#startDate').value && datesDiv.querySelector('#endDate').value)){
            return alert('Please fill both dates')
        }
        if ($.fn.DataTable.isDataTable( '#hmoReportsTable' )){
            $('#hmoReportsTable').dataTable().fnDestroy()
        }
        getHmoReportsTable('hmoReportsTable', reportDatesDiv.querySelector('#category').value, reportDatesDiv.querySelector('#startDate').value, reportDatesDiv.querySelector('#endDate').value)
    })

    markAsSentBtn.addEventListener('click', function() {
        const visitId = markAsSentBtn.getAttribute('data-id')
        markAsSentBtn.setAttribute('disabled', 'disabled')
        http.patch(`/hmo/markassent/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        makeBillModal.hide()
                    }
                    markAsSentBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                    markAsSentBtn.removeAttribute('disabled')
                })
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
        const removeBtn     = event.target.closest('.closeVisitBtn, .deleteVisitBtn')

        if (removeBtn){                
            const [visitId, string]  = [removeBtn.getAttribute('data-id'), removeBtn.id == 'closeVisitBtn' ? 'close' : 'delete']
            if (confirm(`Are you sure you want to ${string} the Visit?`)) {
                http.patch(`/visits/${string}/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        waitingTable.draw()
                    }
                })
                .catch((error) => {
                    if (error.response.status === 403){
                        alert(error.response.data.message); 
                    }
                })
            }
        }
    })

    document.querySelector('#hmoReportsTable').addEventListener('click', function (event) {
        const showVisitisBtn    = event.target.closest('.showVisitisBtn')
        const from              = reportDatesDiv.querySelector('#startDate').value
        const to                = reportDatesDiv.querySelector('#endDate').value

        if (showVisitisBtn){
            const id = showVisitisBtn.getAttribute('data-id')
            reconciliationModal._element.querySelector('#sponsor').value = showVisitisBtn.getAttribute('data-sponsor') + ' - ' + showVisitisBtn.getAttribute('data-category')
            reconciliationModal._element.querySelector('#from').value = from
            reconciliationModal._element.querySelector('#to').value = to
            reconciliationTable = getHmoReconciliationTable('#reconciliationTable', id, reconciliationModal, from, to)
            reconciliationModal.show()
        }
    })

    document.querySelector('#visitPrescriptionsTable').addEventListener('click', function (event) {
        const hmoBillSpan       = event.target.closest('.hmoBillSpan')
        const makeBillFieldset  = document.querySelector('#makeBillFieldset')
        const unmarkSent        = event.target.closest('.unmarkSent')
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

        if (unmarkSent){
            alert('Please unmark as sent before you can make changes')
        }
    })

    document.querySelector('#reconciliationTable').addEventListener('click', function (event) {
        const payBtnSpan       = event.target.closest('.payBtnSpan')
        const reconciliationFieldset  = document.querySelector('#reconciliationFieldset')
        if (payBtnSpan){
            const prescriptionId    = payBtnSpan.getAttribute('data-id')
            const payInput      = payBtnSpan.parentElement.querySelector('.payInput')
            payBtnSpan.classList.add('d-none')
            payInput.classList.remove('d-none')
            resetFocusEndofLine(payInput)
            
            payInput.addEventListener('blur', function () {
                reconciliationFieldset.setAttribute('disabled', 'disabled')
                http.patch(`/hmo/pay/${prescriptionId}`, {amountPaid: payInput.value})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        reconciliationTable ?  reconciliationTable.draw() : ''
                        reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                    }
                })
                .catch((error) => {
                    console.log(error)
                    reconciliationTable ?  reconciliationTable.draw() : ''
                    reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                })                
            })
        }
    })

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal, #makeBillModal, #reconciliationModal, #investigationsModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
            hmotreatmentsTable ?  hmotreatmentsTable.draw() : ''
            sentBillsTable ?  sentBillsTable.draw() : ''
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        })
    })

    verifyModal._element.addEventListener('hide.bs.modal', function () {
        verificationTable.draw()
        hmoApprovalListTable.draw()
        nhisApprovalListTable.draw()
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        verificationTable.draw()
        hmotreatmentsTable ?hmotreatmentsTable.draw() : ''
        hmoApprovalListTable.draw()
        nhisApprovalListTable.draw()
    })

    document.querySelectorAll('#treatmentDiv, #investigationModalDiv').forEach(table => {
        table.addEventListener('click', function (event) {
            const collapseConsultationBtn   = event.target.closest('.collapseConsultationBtn')
            const approvalBtn               = event.target.closest('#approvalBtn')
            const downloadResultBtn         = event.target.closest('#downloadResultBtn')
            const viewer = 'hmo'
    
            if (collapseConsultationBtn) {
                const gotoDiv = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
                const investigationTableId = gotoDiv.querySelector('.investigationTable').id
                const treatmentTableId = gotoDiv.querySelector('.treatmentTable').id
                const otherPrescriptionsTableId = gotoDiv.querySelector('.otherPrescriptionsTable').id
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
                    getLabTableByConsultation(investigationTableId, treatmentDetailsModal._element, viewer, conId, null)
                    getMedicationsByFilter(treatmentTableId, conId, treatmentDetailsModal._element)
                    getOtherPrescriptionsByFilter(otherPrescriptionsTableId, conId, treatmentDetailsModal._element, null, null)
                }
                setTimeout(goto, 300)
            }
    
            if (downloadResultBtn) {
                labResultModal._element.querySelector('#test').innerHTML = downloadResultBtn.getAttribute('data-investigation')
                labResultModal._element.querySelector('#patientsId').innerHTML = downloadResultBtn.getAttribute('data-patient')
                labResultModal._element.querySelector('#result').innerHTML = downloadResultBtn.getAttribute('data-result')
                labResultModal._element.querySelector('#resultDate').innerHTML = downloadResultBtn.getAttribute('data-sent')
                labResultModal._element.querySelector('#StaffFullName').innerHTML = downloadResultBtn.getAttribute('data-stafffullname')
                labResultModal.show()
            }
        })
    })

})

function openHmoModals(modal, button, { id, visitId, ancRegId, patientType, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}