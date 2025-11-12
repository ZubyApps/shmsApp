import { Offcanvas, Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getOrdinal, getDivData, clearValidationErrors, loadingSpinners, removeDisabled, displayList, getPatientSponsorDatalistOptionId, resetFocusEndofLine, displayMedicalReportModal, debounce} from "./helpers"
import { getAllHmoPatientsVisitTable, getApprovalListTable, getBillReminderTable, getDueHmoRemindersTable, getHmoReconciliationTable, getHmoReportsTable, getNhisReconTable, getSentBillsTable, getVerificationTable, getVisitPrescriptionsTable, getWaitingTable } from "./tables/hmoTables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getLabTableByConsultation, getMedicalReportTable, getMedicationsByFilter, getOtherPrescriptionsByFilter, getProceduresListTable, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { getbillingTableByVisit } from "./tables/billingTables";
import { getAncVitalSignsTable } from "./tables/nursesTables";
import html2pdf  from "html2pdf.js"
$.fn.dataTable.ext.errMode = 'throw';


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
    const capitationPaymentModal    = new Modal(document.getElementById('capitationPaymentModal'))
    const registerBillSentModal     = new Modal(document.getElementById('registerBillSentModal'))
    const confirmPaymentModal       = new Modal(document.getElementById('confirmPaymentModal'))

    const codeTextDiv               = verifyModal._element.querySelector('#codeTextDiv')
    const sponsorDetailsDiv         = changeSponsorModal._element.querySelector('#sponsorDetailsDiv')
    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')
    
    const waitingBtn                = document.querySelector('#waitingBtn')
    const hmoApprovalListBtn        = document.querySelector('#hmoApprovalListBtn')
    const hmoApprovalListCount      = document.querySelector('#hmoApprovalListCount')
    const nhisApprovalListBtn       = document.querySelector('#nhisApprovalListBtn')
    const nhisApprovalListCount     = document.querySelector('#nhisApprovalListCount')
    const dueHmoRemindersListBtn    = document.querySelector('#dueRemindersListBtn')
    const dueHmoRemindersListCount  = document.querySelector('#dueRemindersListCount')
    const verifyBtn                 = verifyModal._element.querySelector('#verifyBtn')
    const saveNewSponsorBtn         = changeSponsorModal._element.querySelector('#saveNewSponsorBtn')
    const markAsSentBtn             = makeBillModal._element.querySelector('#markAsSentBtn')
    
    const verificationTab           = document.querySelector('#nav-verifyPatients-tab')
    const treatmentsTab             = document.querySelector('#nav-treatments-tab')
    const sentBillsTab              = document.querySelector('#nav-sentBills-tab')
    const hmoReportsTab             = document.querySelector('#nav-hmoReports-tab')
    const nhisReconTab              = document.querySelector('#nav-nhisRecon-tab')
    const billRemindersTab          = document.querySelector('#nav-billReminders-tab')
    const [verificationView, treatmentsView, sentBillsView, hmoReportsView, nhisReconView, billRemindersView] = [
        document.querySelector('#nav-verifyPatients-view'), 
        document.querySelector('#nav-treatments-view'), 
        document.querySelector('#nav-sentBills-view'),
        document.querySelector('#nav-hmoReports-view'),
        document.querySelector('#nav-nhisRecon-view'),
        document.querySelector('#nav-billReminders-view'),
    ]
    const newSponsorCategoryInput           = document.querySelector('#newSponsorCategory')
    const newPatientSponsorInputEl          = document.querySelector('#newPatientSponsor')
    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')

    const searchWithDatesBtn                = document.querySelector('.searchWithDatesBtn')
    const searchBillsMonthBtn               = document.querySelector('.searchBillsMonthBtn')
    const searchReportsBtn                  = document.querySelector('.searchReportsBtn')
    const searchReportsMonthBtn             = document.querySelector('.searchReportsMonthBtn')
    const searchNhisConBtn                  = document.querySelector('.searchNhisConBtn')
    const searchBillRemindersWithDatesBtn       = document.querySelector('.searchBillRemindersWithDatesBtn')
    const searchBillRemindersMonthBtn       = document.querySelector('.searchBillRemindersMonthBtn')
    const saveCapitationPaymentBtn          = capitationPaymentModal._element.querySelector('#saveCapitationPaymentBtn')
    const saveReminderBtn                   = registerBillSentModal._element.querySelector('#saveReminderBtn')
    const savePaymentBtn                    = confirmPaymentModal._element.querySelector('#savePaymentBtn')
    const proceduresListBtn                 = document.querySelector('#proceduresListBtn')
    const proceduresListCount               = document.querySelector('#proceduresListCount')

    const filterListOption                  = document.querySelector('#filterList')
    const datesDiv                          = document.querySelector('.datesDiv')
    const reportDatesDiv                    = document.querySelector('.reportsDatesDiv')
    const nhisMonthYearDiv                  = document.querySelector('.nhisMonthYearDiv')
    const billRemindersDatesDiv             = document.querySelector('.billRemindersDatesDiv')
    const downloadReportBtn                 = viewMedicalReportModal._element.querySelector('#downloadReportBtn')

    const reportModalBody                   = viewMedicalReportModal._element.querySelector('.reportModalBody')
    const patientsFullName                  = viewMedicalReportModal._element.querySelector('#patientsFullName')
    const patientsInfo                      = viewMedicalReportModal._element.querySelector('#patientsInfo')

    const waitingTable          = getWaitingTable('#waitingTable')
    const verificationTable     = getVerificationTable('#verificationTable')
    const hmoApprovalListTable  = getApprovalListTable('#hmoApprovalListTable',null)
    const nhisApprovalListTable = getApprovalListTable('#nhisApprovalListTable', 'NHIS')
    const dueHmoRemindersTable  = getDueHmoRemindersTable('dueRemindersListTable')
    const proceduresListTable   = getProceduresListTable('#proceduresListTable', 'pending', 'hmo')
    let hmotreatmentsTable, visitPrescriptionsTable, sentBillsTable, hmoReportsTable, reconciliationTable, medicalReportTable, nhisReconTable, billRemindersTable

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

    dueHmoRemindersTable.on('draw.init', function() {
        const count = dueHmoRemindersTable.rows().count()
        if (count > 0 ){
            dueHmoRemindersListCount.innerHTML = count
        } else {
            dueHmoRemindersListCount.innerHTML = ''
        }
    })

    const refreshHomeTables = debounce(() => {
        hmoApprovalListTable.draw(false)
        nhisApprovalListTable.draw(false)
        dueHmoRemindersTable.draw(false)
        proceduresListTable.draw(false);
    }, 60000)

    const refreshApprovalTables = debounce(() => {
        hmoApprovalListTable.draw(false)
        nhisApprovalListTable.draw(false)
    }, 60000)

    const refreshMainTables = debounce(() => {
        verificationView.checkVisibility() ? verificationTable.draw(false) : '';
        treatmentsView.checkVisibility() ? hmotreatmentsTable ? hmotreatmentsTable.draw(false) : '' : ''
        sentBillsView.checkVisibility() ? sentBillsTable ? sentBillsTable.draw(false) : '' : ''
        hmoReportsView.checkVisibility() ? hmoReportsTable ? hmoReportsTable.draw(false) : '' : ''
        nhisReconView.checkVisibility() ? nhisReconTable ? nhisReconTable.draw(false) : '' : ''
        billRemindersTab.checkVisibility() ? billRemindersTable ? billRemindersTable.draw(false) : '' : ''
    }, 100)

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

    proceduresListTable.on('draw.init', function() {
        const count = proceduresListTable.rows().count()
        if (count > 0 ){
            proceduresListCount.innerHTML = count
        } else {
            proceduresListCount.innerHTML = ''
        }
    })

    hmoApprovalListBtn.addEventListener('click', function () {
        hmoApprovalListTable.draw()
    })

    nhisApprovalListBtn.addEventListener('click', function () {
        nhisApprovalListTable.draw()
    })

    dueHmoRemindersListBtn.addEventListener('click', function () {
        dueHmoRemindersTable.draw()
    })

    verificationTab.addEventListener('click', function() {
        refreshMainTables()
        refreshHomeTables()
    })

    proceduresListBtn.addEventListener('click', function () {proceduresListTable.draw(false)})

    treatmentsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDraw()
        } else {
            hmotreatmentsTable = getAllHmoPatientsVisitTable('#hmoTreatmentsTable')
        }
        refreshHomeTables()
    })

    sentBillsTab.addEventListener('click', function () {
        datesDiv.querySelector('#monthYear').value == '' ? datesDiv.querySelector('#monthYear').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
            $('#sentBillsTable').dataTable().fnDraw()
        } else {
            sentBillsTable = getSentBillsTable('#sentBillsTable')
        }
        refreshHomeTables()
    })

    hmoReportsTab.addEventListener('click', function () {
        reportDatesDiv.querySelector('#monthYear').value == '' ? reportDatesDiv.querySelector('#monthYear').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#hmoReportsTable' )){
            $('#hmoReportsTable').dataTable().fnDraw()
        } else {
            hmoReportsTable = getHmoReportsTable('#hmoReportsTable')
        }
        refreshHomeTables()
    })

    nhisReconTab.addEventListener('click', function () {
        nhisMonthYearDiv.querySelector('#nhisDate').value == '' ? nhisMonthYearDiv.querySelector('#nhisDate').value = new Date().toISOString().slice(0,7) : ''
        let date = new Date().toISOString().split('T')[0]
        document.querySelector('#nhisDate').setAttribute('max', date.slice(0,7))
        if ($.fn.DataTable.isDataTable( '#nhisReconTable' )){
            $('#nhisReconTable').dataTable().fnDraw()
        } else {
            nhisReconTable = getNhisReconTable('#nhisReconTable')
        }
        refreshHomeTables()
        
    })

    billRemindersTab.addEventListener('click', function () {
        billRemindersDatesDiv.querySelector('#monthYear').value == '' ? billRemindersDatesDiv.querySelector('#monthYear').value = new Date().toISOString().slice(0,7) : ''
        let date = new Date().toISOString().split('T')[0]
        document.querySelector('#monthYear').setAttribute('max', date.slice(0,7))
        if ($.fn.DataTable.isDataTable( '#billRemindersTable' )){
            $('#billRemindersTable').dataTable().fnDraw()
        } else {
            billRemindersTable = getBillReminderTable('billRemindersTable')
        }
        refreshHomeTables()
    })

    filterListOption.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable( '#hmoTreatmentsTable' )){
            $('#hmoTreatmentsTable').dataTable().fnDestroy()
        }
        hmotreatmentsTable = getAllHmoPatientsVisitTable('#hmoTreatmentsTable', filterListOption.value)
        refreshHomeTables()
    })

    document.querySelectorAll('#waitingListOffcanvas2, #dueRemindersListOffcanvas, #proceduresListOffcanvas').forEach(table => {
        table.addEventListener('hide.bs.offcanvas', function() {
            refreshMainTables();
            refreshHomeTables();
        })
    })

    document.querySelectorAll('#hmoApprovalListOffcanvas, #nhisApprovalListOffcanvas').forEach(table => {
        table.addEventListener('hide.bs.offcanvas', function() {
            hmoApprovalListTable.draw()
            nhisApprovalListTable.draw()
        })
    })
    
    document.querySelectorAll('#hmoTreatmentsTable, #sentBillsTable').forEach(table => {
        table.addEventListener('click', function (event) {
                const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
                const patientBillBtn            = event.target.closest('.patientBillBtn')
                const investigationsBtn         = event.target.closest('.investigationsBtn')
                const treatVisitBtn             = event.target.closest('.treatVisitBtn')
                const closeVisitBtn             = event.target.closest('.closeVisitBtn')
                const medicalReportBtn          = event.target.closest('.medicalReportBtn')
                const filterByOpen              = event.target.closest('.filterByOpen')
                const removeFilter              = event.target.closest('.removeFilter')
                const viewer                    = 'hmo'
        
                if (consultationDetailsBtn) {
                    consultationDetailsBtn.setAttribute('disabled', 'disabled')
                    const btnHtml = consultationDetailsBtn.innerHTML
                    consultationDetailsBtn.innerHTML = loadingSpinners()
        
                    const [visitId, visitType, ancRegId] = [consultationDetailsBtn.getAttribute('data-id'), consultationDetailsBtn.getAttribute('data-visitType'), consultationDetailsBtn.getAttribute('data-ancregid')]
                    const isAnc = visitType === 'ANC'
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

                                    if(isAnc){
                                        const goto = () => {                                    
                                            getLabTableByConsultation('investigationTable'+line.id, modal._element, 'lab', line.id, '')
                                            getMedicationsByFilter('treatmentTable'+line.id, line.id, modal._element)
                                            getOtherPrescriptionsByFilter('otherPrescriptionsTable'+line.id, line.id, modal._element)
                                        }
                                        setTimeout(goto, 300)
                                    }
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
                    makeBillModal._element.querySelector('#sponsor').value = patientBillBtn.getAttribute('data-sponsor') + ' - ' + patientBillBtn.getAttribute('data-sponsorcat')
                    makeBillModal._element.querySelector('#staffId').value = patientBillBtn.getAttribute('data-staffId') ?? ''
                    makeBillModal._element.querySelector('#age').value     = patientBillBtn.getAttribute('data-age')
                    makeBillModal._element.querySelector('#sex').value     = patientBillBtn.getAttribute('data-sex')
                    makeBillModal._element.querySelector('#phone').value   = patientBillBtn.getAttribute('data-phone')
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
                    investigationsModal._element.querySelector('#sponsorName').value = investigationsBtn.getAttribute('data-sponsor') + ' - ' + investigationsBtn.getAttribute('data-sponsorcat')
        
                    getLabTableByConsultation(tableId, investigationsModal._element, viewer, null, visitId)
        
                    investigationsModal.show()
                    investigationsBtn.removeAttribute('disabled')
                }
    
                if (treatVisitBtn){
                    if (confirm('Please confirm that you are taking responsibility to process this Visit?')) {
                        const visitId = treatVisitBtn.getAttribute('data-id')
                        http.patch(`/hmo/treat/${visitId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                hmotreatmentsTable ? hmotreatmentsTable.draw() : ''
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                        })
                    }
                }
                
                if (closeVisitBtn){
                    if (confirm('Are you sure you want to close this Visit?')) {
                        const visitId = closeVisitBtn.getAttribute('data-id')
                        http.patch(`/visits/close/${visitId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                refreshMainTables();
                                refreshApprovalTables();
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
                    medicalReportListModal._element.querySelector('#sponsorName').value = medicalReportBtn.getAttribute('data-sponsor') + ' - ' + medicalReportBtn.getAttribute('data-sponsorcat')
                    medicalReportListModal._element.querySelector('#age').value = medicalReportBtn.getAttribute('data-age')
                    medicalReportListModal._element.querySelector('#sex').value = medicalReportBtn.getAttribute('data-sex')
                    medicalReportTable = getMedicalReportTable('medicalReportTable', visitId, medicalReportListModal._element)
                    medicalReportListModal.show()
                }

                if (filterByOpen){
                    datesDiv.querySelector('#monthYear').value ? (datesDiv.querySelector('#startDate').value = '', datesDiv.querySelector('#endDate').value = '') : ''
                    if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
                        $('#sentBillsTable').dataTable().fnDestroy()
                    }
                    sentBillsTable = getSentBillsTable('#sentBillsTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value, datesDiv.querySelector('#monthYear').value, 1)
                }

                if (removeFilter){
                    datesDiv.querySelector('#monthYear').value ? (datesDiv.querySelector('#startDate').value = '', datesDiv.querySelector('#endDate').value = '') : ''
                    if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
                        $('#sentBillsTable').dataTable().fnDestroy()
                    }
                    sentBillsTable = getSentBillsTable('#sentBillsTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value, datesDiv.querySelector('#monthYear').value)
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
            const resetDiv          = event.target.closest('.resetDiv')
            const approveRejectDiv  = event.target.closest('.approveRejectDiv')
        
            if (approveBtn) {
                const prescriptionId = approveBtn.getAttribute('data-id')
                const parentDivState = approveBtn.parentElement.innerHTML
                approveBtn.classList.add('d-none')
                const parentDiv = approveBtn.parentElement
                parentDiv.querySelector('.rejectBtn').classList.add('d-none')
                const noteInput = parentDiv.querySelector('.noteInput')
                noteInput.classList.remove('d-none')
                noteInput.focus()
                noteInput.addEventListener('blur', function() {
                    // approvalFieldset.setAttribute('disabled', 'disabled')
                    http.patch(`/hmo/approve/${prescriptionId}`, {note: noteInput.value})
                    .then((response) => {
                        if (response.status == 200) {
                            parentDiv.innerHTML = 'approved'
                            // table.draw(false)
                            // table.on('draw', removeDisabled(approvalFieldset))                        
                        }
                        if (response.status == 222){
                            parentDiv.innerHTML = response.data
                            // errorTimeStateMgt(parentDiv, parentDivState, response.data, )
                            // alert(response.data)
                            // table.draw(false)
                            // table.on('draw', removeDisabled(approvalFieldset))   
                        }
                    })
                        .catch((error) => {
                            errorTimeStateMgt(parentDiv, parentDivState, 'failed!...', 1000)
                            console.log(error.response.data)
                            // table.draw(false)
                            // table.on('draw', removeDisabled(approvalFieldset))
                            
                        })
                    })          
                }
        
                if (rejectBtn) {
                    const prescriptionId = rejectBtn.getAttribute('data-id')
                    const parentDivState = rejectBtn.parentElement.innerHTML
                    rejectBtn.classList.add('d-none')
                    const parentDiv = rejectBtn.parentElement
                    parentDiv.querySelector('.approveBtn').classList.add('d-none')
                    const noteInput = parentDiv.querySelector('.noteInput')
                    noteInput.classList.remove('d-none')
                    noteInput.focus()
                    noteInput.addEventListener('blur', function() {
                        // approvalFieldset.setAttribute('disabled', 'disabled')
                        if (noteInput.value) {
                            http.patch(`/hmo/reject/${prescriptionId}`, {note: noteInput.value})
                            .then((response) => {
                                if (response.status >= 200) {
                                    // table.draw(false)
                                    // table.on('draw', removeDisabled(approvalFieldset))
                                    parentDiv.innerHTML = 'rejected'
                                }
                                if (response.status == 222){
                                    parentDiv.innerHTML = response.data
                                    // errorTimeStateMgt(parentDiv, parentDivState, response.data, )
                                    // alert(response.data)
                                    // table.draw(false)
                                    // table.on('draw', removeDisabled(approvalFieldset))   
                                }
                            })
                            .catch((error) => {
                                errorTimeStateMgt(parentDiv, parentDivState, 'failed!...', 1000)
                                console.log(error)
                                // rejectBtn.removeAttribute('disabled')
                                // table.draw(false)
                                // table.on('draw', removeDisabled(approvalFieldset))
                            })
                        } else{
                            // table.draw(false)
                            // table.on('draw', removeDisabled(approvalFieldset))
                            errorTimeStateMgt(parentDiv, parentDivState, 'type something!...', 1000)
                            // parentDiv.innerHTML = parentDivState
                        }
                    })
                }

                if (resetBtn){
                    const prescriptionId = resetBtn.getAttribute('data-id')
                    approvedByBtn.innerHTML = 'Wait...'
                    const approveRejectDiv  = resetDiv.parentElement.querySelector('.approveRejectDiv')
                    http.patch(`/hmo/reset/${prescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            resetDiv.classList.add('d-none')
                            approveRejectDiv.classList.remove('d-none')
                            // console.log(approveRejectDiv)
                            // table.draw(false)
                            // table.on('draw', removeDisabled(approvalFieldset))
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        resetDiv.classList.add('d-none')
                        approveRejectDiv.classList.remove('d-none')
                        // table.draw(false)
                        // table.on('draw', removeDisabled(approvalFieldset))
                    })
                }
            })
    })

    searchWithDatesBtn.addEventListener('click', function () {
        datesDiv.querySelector('#monthYear').value = ''

        if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
            $('#sentBillsTable').dataTable().fnDestroy()
        }
        sentBillsTable = getSentBillsTable('#sentBillsTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    searchBillsMonthBtn.addEventListener('click', function () {
        datesDiv.querySelector('#startDate').value = ''; datesDiv.querySelector('#endDate').value = '';

        if ($.fn.DataTable.isDataTable( '#sentBillsTable' )){
            $('#sentBillsTable').dataTable().fnDestroy()
        }
        sentBillsTable = getSentBillsTable('#sentBillsTable', null, null, datesDiv.querySelector('#monthYear').value)
    })

    searchReportsBtn.addEventListener('click', function () {
        reportDatesDiv.querySelector('#monthYear').value = ''

        if ($.fn.DataTable.isDataTable( '#hmoReportsTable' )){
            $('#hmoReportsTable').dataTable().fnDestroy()
        }
        hmoReportsTable = getHmoReportsTable('#hmoReportsTable', reportDatesDiv.querySelector('#category').value, reportDatesDiv.querySelector('#startDate').value, reportDatesDiv.querySelector('#endDate').value)
    })

    searchReportsMonthBtn.addEventListener('click', function () {
        reportDatesDiv.querySelector('#startDate').value = ''; reportDatesDiv.querySelector('#endDate').value = '';

        if ($.fn.DataTable.isDataTable( '#hmoReportsTable' )){
            $('#hmoReportsTable').dataTable().fnDestroy()
        }
        hmoReportsTable = getHmoReportsTable('#hmoReportsTable', reportDatesDiv.querySelector('#category').value, null, null, reportDatesDiv.querySelector('#monthYear').value)
    })

    searchNhisConBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#nhisReconTable' )){
            $('#nhisReconTable').dataTable().fnDestroy()
        }
        nhisReconTable = getNhisReconTable('#nhisReconTable', nhisMonthYearDiv.querySelector('#nhisDate').value)
    })

    searchBillRemindersWithDatesBtn.addEventListener('click', function () {
        billRemindersDatesDiv.querySelector('#monthYear').value = ''

        if ($.fn.DataTable.isDataTable( '#billRemindersTable' )){
            $('#billRemindersTable').dataTable().fnDestroy()
        }
        billRemindersTable = getBillReminderTable('billRemindersTable', billRemindersDatesDiv.querySelector('#startDate').value, billRemindersDatesDiv.querySelector('#endDate').value)
    })

    searchBillRemindersMonthBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#billRemindersTable' )){
            $('#billRemindersTable').dataTable().fnDestroy()
        }
        billRemindersTable = getBillReminderTable('billRemindersTable', null, null, billRemindersDatesDiv.querySelector('#monthYear').value)
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
            verifyModal._element.querySelector('#sponsorName').value = verifyPatientBtn.getAttribute('data-sponsor') + ' - ' + verifyPatientBtn.getAttribute('data-sponsorcat')
            verifyModal._element.querySelector('#staffId').value = verifyPatientBtn.getAttribute('data-staffid')
            verifyModal._element.querySelector('#phoneNumber').value = verifyPatientBtn.getAttribute('data-phone')
            verifyModal._element.querySelector('#status').value = verifyPatientBtn.getAttribute('data-status')
            verifyModal._element.querySelector('#codeText').value = verifyPatientBtn.getAttribute('data-codeText')
            verifyModal.show()
        }

        if (changeSponsorBtn){
            saveNewSponsorBtn.setAttribute('data-id', changeSponsorBtn.getAttribute('data-id'))
            saveNewSponsorBtn.setAttribute('data-table', changeSponsorBtn.getAttribute('data-table'))
            changeSponsorModal._element.querySelector('#patientId').value = changeSponsorBtn.getAttribute('data-patient')
            changeSponsorModal._element.querySelector('#sponsorName').value = changeSponsorBtn.getAttribute('data-sponsor') + ' - ' + changeSponsorBtn.getAttribute('data-sponsorcat')
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
        const showVisitsBtn     = event.target.closest('.showVisitsBtn')
        const registerBillSent  = event.target.closest('.registerBillSent')
        const from              = reportDatesDiv.querySelector('#startDate').value
        const to                = reportDatesDiv.querySelector('#endDate').value

        if (showVisitsBtn){
            const id = showVisitsBtn.getAttribute('data-id')
            const date = showVisitsBtn.getAttribute('data-yearmonth') ?? reportDatesDiv.querySelector('#monthYear').value
            reconciliationModal._element.querySelector('#sponsor').value = showVisitsBtn.getAttribute('data-sponsor')
            reconciliationModal._element.querySelector('#sponsorCategory').value = showVisitsBtn.getAttribute('data-category')
            reconciliationModal._element.querySelector('#from').value = from
            reconciliationModal._element.querySelector('#to').value = to
            reconciliationModal._element.querySelector('#visitMonth').value = date
            
            if (date){
                reconciliationTable = getHmoReconciliationTable('#reconciliationTable', id, reconciliationModal, null, null, date)
                reconciliationModal.show()
                return
            }

            if(from && to){
                reconciliationTable = getHmoReconciliationTable('#reconciliationTable', id, reconciliationModal, from, to)
                reconciliationModal.show()
                return
            }

            reconciliationModal._element.querySelector('#visitMonth').value = date
            reconciliationTable = getHmoReconciliationTable('#reconciliationTable', id, reconciliationModal)
            reconciliationModal.show()
        }

        if (registerBillSent){
            registerBillSentModal._element.querySelector('#sponsor').value = registerBillSent.getAttribute('data-sponsor')
            registerBillSentModal._element.querySelector('#monthYear').value = registerBillSent.getAttribute('data-monthYear')
            saveReminderBtn.setAttribute('data-id', registerBillSent.getAttribute('data-id'))
            registerBillSentModal.show()
        }
    })

    saveReminderBtn.addEventListener('click', function () {
        const sponsorId = saveReminderBtn.getAttribute('data-id')
        saveReminderBtn.setAttribute('disabled', 'disabled')

        let data = {...getDivData(registerBillSentModal._element), sponsorId }

        http.post('/reminders/hmo', {...data}, {"html": registerBillSentModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                registerBillSentModal.hide()
                    clearDivValues(registerBillSentModal._element)
                    clearValidationErrors(registerBillSentModal._element)
                    hmoReportsTable ? hmoReportsTable.draw(false) : ''
                }
                saveReminderBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveReminderBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
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
            resetFocusEndofLine(hmoBillInput)
            
            hmoBillInput.addEventListener('blur', function () {
                makeBillFieldset.setAttribute('disabled', 'disabled')
                http.patch(`/hmo/bill/${prescriptionId}`, {bill: hmoBillInput.value})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        visitPrescriptionsTable ?  visitPrescriptionsTable.draw(false) : ''
                        visitPrescriptionsTable.on('draw', removeDisabled(makeBillFieldset))
                    }
                })
                .catch((error) => {
                    console.log(error)
                    visitPrescriptionsTable ?  visitPrescriptionsTable.draw(false) : ''
                    visitPrescriptionsTable.on('draw', removeDisabled(makeBillFieldset))
                })                
            })
        }

        if (unmarkSent){
            alert('Please unmark as sent before you can make changes')
        }
    })

    document.querySelectorAll('#reconciliationTable, #nhisReconTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const payBtnSpan                = event.target.closest('.payBtnSpan')
            const reconciliationFieldset    = document.querySelector('#reconciliationFieldset')
            const enterCapitationPaymentBtn = event.target.closest('.enterCapitationPaymentBtn')
            const addSpanBtn                = event.target.closest('.addSpanBtn')
            const payBulkSpan               = event.target.closest('.payBulkSpan')
    
            if (payBtnSpan){
                const prescriptionId    = payBtnSpan.getAttribute('data-id')
                const payInput          = payBtnSpan.parentElement.querySelector('.payInput')
                payBtnSpan.classList.add('d-none')
                payInput.classList.remove('d-none')
                // payInput.focus()
                resetFocusEndofLine(payInput)
                
                payInput.addEventListener('blur', function () {
                    reconciliationFieldset.setAttribute('disabled', 'disabled')
                    http.patch(`/hmo/pay/${prescriptionId}`, {amountPaid: payInput.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            reconciliationTable ?  reconciliationTable.draw(false) : ''
                            reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                        }
                    })
                    .catch((error) => {
                        reconciliationTable ?  reconciliationTable.draw(false) : ''
                        reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                        console.log(error)
                    })                
                })
            }

            if (addSpanBtn){
                const prescriptionId    = addSpanBtn.getAttribute('data-id')
                const addAmount         = addSpanBtn.parentElement.querySelector('.addAmount')
                const payInput          = addSpanBtn.parentElement.querySelector('.payInput')
                const payBtnSpan          = addSpanBtn.parentElement.querySelector('.payBtnSpan')
                payInput.classList.remove('d-none')
                payInput.setAttribute('readonly', true)
                addAmount.classList.remove('d-none')
                payBtnSpan.classList.add('d-none')

                resetFocusEndofLine(addAmount)

                addAmount.addEventListener('blur', function () {
                    reconciliationFieldset.setAttribute('disabled', 'disabled')
                    http.patch(`/hmo/pay/${prescriptionId}`, {amountPaid: +payInput.value + +addAmount.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            reconciliationTable ?  reconciliationTable.draw(false) : ''
                            reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                        }
                    })
                    .catch((error) => {
                        reconciliationTable ?  reconciliationTable.draw(false) : ''
                        reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                        console.log(error)
                    })                
                })

            }

            if (enterCapitationPaymentBtn){
                capitationPaymentModal._element.querySelector('#sponsor').value = enterCapitationPaymentBtn.getAttribute('data-sponsor')
                capitationPaymentModal._element.querySelector('#monthYear').value = enterCapitationPaymentBtn.getAttribute('data-monthYear')
                saveCapitationPaymentBtn.setAttribute('data-id', enterCapitationPaymentBtn.getAttribute('data-id'))
                capitationPaymentModal.show()
            }

            if (payBulkSpan){
                const visitId           = payBulkSpan.getAttribute('data-id')
                const totalHmoBill      = payBulkSpan.getAttribute('data-totalhmobill')
                const totalPaid         = payBulkSpan.getAttribute('data-totalpaid')
                const payBulkInput      = payBulkSpan.parentElement.querySelector('.payBulkInput')

                if (+totalPaid > 0){
                    alert('Payment(s) already exist! Please enter any additions manually')                        
                    return
                }

                payBulkSpan.classList.add('d-none')
                payBulkInput.classList.remove('d-none')
                payBulkInput.focus()

                payBulkInput.addEventListener('blur', function () {
                    if (!payBulkInput.value || payBulkInput.value == 0){
                        reconciliationTable ?  reconciliationTable.draw(false) : ''
                        return
                    }
                    if (+totalHmoBill > payBulkInput.value){
                        alert('Cannot use "Pay Bulk" if the payment is less than the HMO bill. Pls enter it manually')
                        payBulkSpan.classList.remove('d-none')
                        payBulkInput.classList.add('d-none')
                        return
                    }

                    reconciliationFieldset.setAttribute('disabled', 'disabled')
                    http.patch(`/hmo/paybulk/${visitId}`, {bulkPayment: payBulkInput.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            reconciliationTable ?  reconciliationTable.draw(false) : ''
                            reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                        }
                    })
                    .catch((error) => {
                        reconciliationTable ?  reconciliationTable.draw(false) : ''
                        reconciliationTable.on('draw', removeDisabled(reconciliationFieldset))
                        console.log(error)
                    })                
                })
            }
        })    
    })

    capitationPaymentModal._element.querySelector('#numberOfLives').addEventListener('input', function() {
        capitationPaymentModal._element.querySelector('#amountPaid').value = this.value * capitationPaymentModal._element.querySelector('#perLife').value
    })

    saveCapitationPaymentBtn.addEventListener('click', function () {
        const sponsor = saveCapitationPaymentBtn.getAttribute('data-id')
        const perLife = capitationPaymentModal._element.querySelector('#perLife').value
        let data = {...getDivData(capitationPaymentModal._element), sponsor }

        http.post('/capitation', {...data}, {"html": capitationPaymentModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                capitationPaymentModal.hide()
                    clearDivValues(capitationPaymentModal._element)
                    nhisReconTable ? nhisReconTable.draw(false) : ''
                    capitationPaymentModal._element.querySelector('#perLife').value = perLife   
                }
                saveCapitationPaymentBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            nhisReconTable ? nhisReconTable.draw(false) : ''
            saveCapitationPaymentBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal, #makeBillModal, #reconciliationModal, #investigationsModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
            refreshApprovalTables();
            refreshMainTables();
        })
    })

    verifyModal._element.addEventListener('hide.bs.modal', function () {
        refreshApprovalTables();
        refreshMainTables();
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        refreshApprovalTables()
        refreshMainTables()
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
                let goToTimer = setTimeout(goto, 300)
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

    document.querySelector('#billRemindersTable').addEventListener('click', function (event) {
        const deleteFirstReminderBtn    = event.target.closest('.deleteFirstReminderBtn')
        const deleteSecondReminderBtn   = event.target.closest('.deleteSecondReminderBtn')
        const deleteFinalReminderBtn    = event.target.closest('.deleteFinalReminderBtn')
        const deletePaidBtn             = event.target.closest('.deletePaidBtn')
        const deleteBillReminderBtn     = event.target.closest('.deleteBillReminderBtn')

        if (deleteFirstReminderBtn){
            deleteFirstReminderBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this First reminder?')) {
                const billReminderId = deleteFirstReminderBtn.getAttribute('data-id')
                http.patch(`/reminders/deletefirst/${billReminderId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            billRemindersTable ? billRemindersTable.draw() : ''
                        }
                        deleteFirstReminderBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteFirstReminderBtn.removeAttribute('disabled')
                        alert(error)
                    })
            }
        }
        if (deleteSecondReminderBtn){
            deleteSecondReminderBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Second reminder?')) {
                const billReminderId = deleteSecondReminderBtn.getAttribute('data-id')
                http.patch(`/reminders/deletesecond/${billReminderId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            billRemindersTable ? billRemindersTable.draw() : ''
                        }
                        deleteSecondReminderBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteSecondReminderBtn.removeAttribute('disabled')
                        alert(error)
                    })
            }
        }
        if (deleteFinalReminderBtn){
            deleteFinalReminderBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Final reminder?')) {
                const billReminderId = deleteFinalReminderBtn.getAttribute('data-id')
                http.patch(`/reminders/deletefinal/${billReminderId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            billRemindersTable ? billRemindersTable.draw() : ''
                        }
                        deleteFinalReminderBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteFinalReminderBtn.removeAttribute('disabled')
                        alert(error)
                    })
            }
        }
        if (deletePaidBtn){
            deletePaidBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Paid Confirmation?')) {
                const billReminderId = deletePaidBtn.getAttribute('data-id')
                http.patch(`/reminders/deletepaid/${billReminderId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            billRemindersTable ? billRemindersTable.draw() : ''
                        }
                        deletePaidBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deletePaidBtn.removeAttribute('disabled')
                        alert(error)
                    })
            }
        }

        if (deleteBillReminderBtn){
            deleteBillReminderBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Reminder?')) {
                const billReminderId = deleteBillReminderBtn.getAttribute('data-id')
                http.delete(`/reminders/${billReminderId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            billRemindersTable ? billRemindersTable.draw() : ''
                        }
                        deleteBillReminderBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBillReminderBtn.removeAttribute('disabled')
                        alert(error)
                    })
            }
        }
    })

    document.querySelectorAll('#dueRemindersListTable, #hmoReportsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const firstReminderSelect   = event.target.closest('.firstReminderSelect')
            const secondReminderSelect  = event.target.closest('.secondReminderSelect')
            const finalReminderSelect   = event.target.closest('.finalReminderSelect')
            // const confirmedPaidInput    = event.target.closest('.confirmedPaidInput')
            const confirmedPaidBtn    = event.target.closest('.confirmedPaidBtn')
            const dueRemindersFieldset  = document.querySelector('#dueRemindersFieldset')
    
           if (firstReminderSelect){
                const reminderId  = firstReminderSelect.getAttribute('data-id')
                    
                firstReminderSelect.addEventListener('blur', function () {
                    dueRemindersFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/reminders/firstreminder/${reminderId}`, {reminder:  firstReminderSelect.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                dueHmoRemindersTable.draw()
                                dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset)) 
                            }
                        })
                        .catch((error) => {
                            dueHmoRemindersTable.draw()
                            dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            console.log(error)
                        })               
                })
            }
    
           if (secondReminderSelect){
                const reminderId  = secondReminderSelect.getAttribute('data-id')
                    
                secondReminderSelect.addEventListener('blur', function () {
                    dueRemindersFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/reminders/secondreminder/${reminderId}`, {reminder : secondReminderSelect.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                    dueHmoRemindersTable.draw()
                                    dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset)) 
                            }
                        })
                        .catch((error) => {
                            dueHmoRemindersTable.draw()
                            dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            console.log(error)
                        })               
                })
            }
    
           if (finalReminderSelect){
                const reminderId  = finalReminderSelect.getAttribute('data-id')
                    
                finalReminderSelect.addEventListener('blur', function () {
                    dueRemindersFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/reminders/finalreminder/${reminderId}`, {reminder : finalReminderSelect.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                    dueHmoRemindersTable.draw()
                                    dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            }
                        })
                        .catch((error) => {
                            dueHmoRemindersTable.draw()
                            dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            console.log(error)
                        })               
                })
            }
    
        //    if (confirmedPaidInput){
        //         const reminderId  = confirmedPaidInput.getAttribute('data-id')
                    
        //         confirmedPaidInput.addEventListener('blur', function () {
        //             dueRemindersFieldset.setAttribute('disabled', 'disabled')
        //                 http.patch(`/reminders/confirmedpaid/${reminderId}`, {confirmedPaidDate: confirmedPaidInput.value})
        //                 .then((response) => {
        //                     if (response.status >= 200 || response.status <= 300) {
        //                             dueHmoRemindersTable.draw()
        //                             dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
        //                     }
        //                 })
        //                 .catch((error) => {
        //                     dueHmoRemindersTable.draw()
        //                     dueHmoRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
        //                     console.log(error)
        //                 })               
        //         })
        //     }
    
            if (confirmedPaidBtn){
                savePaymentBtn.setAttribute('data-id', confirmedPaidBtn.getAttribute('data-id'))
                confirmPaymentModal._element.querySelector('#sponsor').value = confirmedPaidBtn.getAttribute('data-sponsor')
                confirmPaymentModal._element.querySelector('#monthYear').value = confirmedPaidBtn.getAttribute('data-monthYear')
                confirmPaymentModal.show()
            }
        })
    })

    savePaymentBtn.addEventListener('click', function () {
        const reminderId = savePaymentBtn.getAttribute('data-id')
        savePaymentBtn.setAttribute('disabled', 'disabled')

        http.patch(`/reminders/confirmedpaid/${reminderId}`, getDivData(confirmPaymentModal._element), {"html": confirmPaymentModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                confirmPaymentModal.hide()
                }
                savePaymentBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            savePaymentBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    confirmPaymentModal._element.addEventListener('hide.bs.modal', function(event) {
        clearValidationErrors(confirmPaymentModal._element)
        clearDivValues(confirmPaymentModal._element)
        dueHmoRemindersTable.draw()
        refreshMainTables();
    })

})

function openHmoModals(modal, button, { id, visitId, ancRegId, visitType, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}

const errorTimeStateMgt = (currentEl, precervedEl, message, timer) => {
    currentEl.innerHTML = message
    return  setTimeout(() => {
                currentEl.innerHTML = precervedEl
            }, timer)
}