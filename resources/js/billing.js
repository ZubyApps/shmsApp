import { Offcanvas, Modal, Toast } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getDivData, clearValidationErrors, resetFocusEndofLine, openModals, displayMedicalReportModal, removeDisabled} from "./helpers"
import { getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit, getPatientsBill, getExpensesTable, getBalancingTable, getDueCashRemindersTable, getBillReminderTable } from "./tables/billingTables";
import { getOutpatientsInvestigationTable } from "./tables/investigationTables";
import html2pdf  from "html2pdf.js"
import { getShiftReportTable } from "./tables/pharmacyTables";
import { getMedicalReportTable } from "./tables/doctorstables";
$.fn.dataTable.ext.errMode = 'throw';


window.addEventListener('DOMContentLoaded', function () {
    const billingModal                  = new Modal(document.getElementById('billingModal'))
    const dischargeBillModal            = new Modal(document.getElementById('dischargeBillModal'))
    const outstandingBillsModal         = new Modal(document.getElementById('outstandingBillsModal'))
    const billModal                     = new Modal(document.getElementById('billModal'))
    const newExpenseModal               = new Modal(document.getElementById('newExpenseModal'))
    const updateExpenseModal            = new Modal(document.getElementById('updateExpenseModal'))
    const thirdPartyServiceModal        = new Modal(document.getElementById('thirdPartyServiceModal'))
    const newShiftReportTemplateModal   = new Modal(document.getElementById('newShiftReportTemplateModal'))
    const editShiftReportTemplateModal  = new Modal(document.getElementById('editShiftReportTemplateModal'))
    const viewShiftReportTemplateModal  = new Modal(document.getElementById('viewShiftReportTemplateModal'))
    const medicalReportListModal        = new Modal(document.getElementById('medicalReportListModal'))
    const viewMedicalReportModal        = new Modal(document.getElementById('viewMedicalReportModal'))
    const registerBillReminderModal     = new Modal(document.getElementById('registerBillReminderModal'))
    const smsTemplateModal              = new Modal(document.getElementById('smsTemplateModal'))
    const confirmPaymentModal           = new Modal(document.getElementById('confirmPaymentModal'))

    const balancingDateDiv              = document.querySelector('.balancingDateDiv')
    const billRemindersDatesDiv         = document.querySelector('.billRemindersDatesDiv')

    const waitingBtn                    = document.querySelector('#waitingBtn')
    const outpatientsInvestigationBtn   = document.querySelector('#outpatientsInvestigationBtn')
    const newExpenseBtn                 = document.querySelector('#newExpenseBtn')
    const saveExpenseBtn                = newExpenseModal._element.querySelector('#saveExpenseBtn')
    const updateExpenseBtn              = updateExpenseModal._element.querySelector('#updateExpenseBtn')
    const searchBalanceByDateBtn        = balancingDateDiv.querySelector('.searchBalanceByDateBtn')
    const saveThirPartyServiceBtn       = thirdPartyServiceModal._element.querySelector('#saveThirPartyServiceBtn')
    const dischargeBillBtn              = billingModal._element.querySelector('#dischargeBillBtn')
    const addBillBtn                    = dischargeBillModal._element.querySelector('#addBillBtn')
    const shiftReportBtn                = document.querySelector('#shiftReportBtn')
    const newBillingReportBtn           = document.querySelector('#newBillingReportBtn')
    const createShiftReportBtn          = newShiftReportTemplateModal._element.querySelector('#createShiftReportBtn')
    const saveShiftReportBtn            = editShiftReportTemplateModal._element.querySelector('#saveShiftReportBtn')
    const downloadReportBtn             = viewMedicalReportModal._element.querySelector('#downloadReportBtn')
    const saveBillReminderBtn           = registerBillReminderModal._element.querySelector('#saveBillReminderBtn')
    const dueCashRemindersListBtn        = document.querySelector('#dueRemindersListBtn')
    const dueCashRemindersListCount      = document.querySelector('#dueRemindersListCount')
    const searchBillRemindersWithDatesBtn   = document.querySelector('.searchBillRemindersWithDatesBtn')
    const searchBillRemindersMonthBtn       = document.querySelector('.searchBillRemindersMonthBtn')
    const sendSmsBtn                        = smsTemplateModal._element.querySelector('#sendSms')
    const savePaymentBtn                    = confirmPaymentModal._element.querySelector('#savePaymentBtn')
    

    const outPatientsTab                = document.querySelector('#nav-outPatients-tab')
    const inPatientsTab                 = document.querySelector('#nav-inPatients-tab')
    const ancPatientsTab                = document.querySelector('#nav-ancPatients-tab')
    const openVisitsTab                 = document.querySelector('#nav-openVisits-tab')
    const expensesTab                   = document.querySelector('#nav-expenses-tab')
    const balancingTab                  = document.querySelector('#nav-balancing-tab')
    const billRemindersTab              = document.querySelector('#nav-billReminders-tab')

    const reportModalBody               = viewMedicalReportModal._element.querySelector('.reportModalBody')
    const patientsFullName              = viewMedicalReportModal._element.querySelector('#patientsFullName')
    const patientsInfo                  = viewMedicalReportModal._element.querySelector('#patientsInfo')

    const changeBillSpan                = billModal._element.querySelector('.changeBill')
    const downloadBillSummaryBtn        = billModal._element.querySelector('#downloadBillSummaryBtn')
    const billSummaryBody               = billModal._element.querySelector('.billSummaryBody')
    const shiftBadgeSpan                = document.querySelector('#shiftBadgeSpan')


    let inPatientsVisitTable, ancPatientsVisitTable, billingTable, paymentTable, openVisitsTable, expensesTable, balancingTable, medicalReportTable, billRemindersTable

    const outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient', 'consulted')
    const waitingTable = getWaitingTable('waitingTable')
    const outpatientInvestigationTable = getOutpatientsInvestigationTable('outpatientInvestigationsTable', true)
    const billingShiftReportTable = getShiftReportTable('billingShiftReportTable', 'billing', shiftBadgeSpan)
    const dueCashRemindersTable = getDueCashRemindersTable('dueRemindersListTable')

    $('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #outpatientInvestigationsTable, #waitingTable, #billingTable, #openVisitsTable, #expensesTable, #balancingTable, #billingShiftReportTable').on('error.dt', function(e, settings, techNote, message) {techNote == 7 ? window.location.reload() : ''})    

    outPatientsTab.addEventListener('click', function() {outPatientsVisitTable.draw(); dueCashRemindersTable.draw()})
    outpatientsInvestigationBtn.addEventListener('click', function () {outpatientInvestigationTable.draw()})
    shiftReportBtn.addEventListener('click', function () {billingShiftReportTable.draw()})

    newBillingReportBtn.addEventListener('click', function () {
        newShiftReportTemplateModal.show()
    })

    dueCashRemindersTable.on('draw.init', function() {
        const count = dueCashRemindersTable.rows().count()
        if (count > 0 ){
            dueCashRemindersListCount.innerHTML = count
        } else {
            dueCashRemindersListCount.innerHTML = ''
        }
    })

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitsByFilterTable('inPatientsVisitTable', 'Inpatient', 'consulted')
        }
        billingShiftReportTable.draw()
        dueCashRemindersTable.draw()
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('ancPatientsVisitTable', 'ANC', 'consulted')
        }
        billingShiftReportTable.draw()
        dueCashRemindersTable.draw()
    })

    openVisitsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#openVisitsTable' )){
            $('#openVisitsTable').dataTable().fnDraw()
        } else {
            openVisitsTable = getPatientsVisitsByFilterTable('openVisitsTable', '', 'openvisits')
        }
        billingShiftReportTable.draw()
        dueCashRemindersTable.draw()
    })

    expensesTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#expensesTable' )){
            $('#expensesTable').dataTable().fnDraw()
        } else {
            expensesTable = getExpensesTable('expensesTable', 'billing')
        }
        billingShiftReportTable.draw()
        dueCashRemindersTable.draw()
    })

    balancingTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#balancingTable' )){
            $('#balancingTable').dataTable().fnDraw()
        } else {
            balancingTable = getBalancingTable('balancingTable')
        }
        billingShiftReportTable.draw()
        dueCashRemindersTable.draw()
    })

    billRemindersTab.addEventListener('click', function () {
        billRemindersDatesDiv.querySelector('#monthYear').value == '' ? billRemindersDatesDiv.querySelector('#monthYear').value = new Date().toISOString().slice(0,7) : ''
        let date = new Date().toISOString().split('T')[0]
        document.querySelector('#monthYear').setAttribute('max', date.slice(0,7))
        if ($.fn.DataTable.isDataTable( '#billRemindersTable' )){
            $('#billRemindersTable').dataTable().fnDraw()
            dueCashRemindersTable.draw()
        } else {
            billRemindersTable = getBillReminderTable('billRemindersTable')
            dueCashRemindersTable.draw()
        }
    })

    searchBalanceByDateBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#balancingTable' )){
            $('#balancingTable').dataTable().fnDestroy()
        }
        balancingTable = getBalancingTable('balancingTable', null, balancingDateDiv.querySelector('#balanceDate').value)
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

    newExpenseBtn.addEventListener('click', function () {
        newExpenseModal.show()
    })

    dueCashRemindersListBtn.addEventListener('click', function () {
        dueCashRemindersTable.draw()
    })

    document.querySelectorAll('#waitingListOffcanvas2, #offcanvasInvestigations, #viewShiftReportTemplateModal, #newShiftReportTemplateModal, #dueRemindersListOffcanvas').forEach(canvas => {
        canvas.addEventListener('hide.bs.offcanvas', function () {
            outPatientsVisitTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            billingShiftReportTable.draw()
            dueCashRemindersTable.draw()
            billRemindersTable ? billRemindersTable.draw() : ''
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

    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #outstandingBillsTable, #openVisitsTable, #waitingTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const billingDetailsBtn = event.target.closest('.consultationDetailsBtn')
            const patientsBillBtn   = event.target.closest('.patientsBillBtn')
            const medicalReportBtn  = event.target.closest('.medicalReportBtn')
            const closeVisitBtn     = event.target.closest('.closeVisitBtn')
            
            if (billingDetailsBtn){
                const [visitId, conId] = [billingDetailsBtn.getAttribute('data-id'),  billingDetailsBtn.getAttribute('data-conid')]
                billingTable = getbillingTableByVisit('billingTable', visitId, billingModal._element, true)
                paymentTable = getPaymentTableByVisit('paymentTable', visitId, billingModal._element)
                addBillBtn.setAttribute('data-visitid', visitId); addBillBtn.setAttribute('data-conid', conId)
                outstandingBillsModal.hide()
                billingModal.show()
            }

            if (patientsBillBtn){
                const visitId   = patientsBillBtn.getAttribute('data-id')
                billModal._element.querySelector('.patient').innerHTML      = patientsBillBtn.getAttribute('data-patient')
                billModal._element.querySelector('.billingStaff').innerHTML = patientsBillBtn.getAttribute('data-staff')
                changeBillSpan.setAttribute('data-visitid', visitId)
                getPatientsBill('billTable', visitId, billModal._element, 'category')
                billModal.show()
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

            if (closeVisitBtn){
                if (confirm('Are you sure you want to close this Visit?')) {
                    const visitId = closeVisitBtn.getAttribute('data-id')
                    http.patch(`/visits/close/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            waitingTable.draw()
                            outPatientsVisitTable.draw(false)
                            inPatientsVisitTable ? inPatientsVisitTable.draw(false) : ''
                            ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : ''
                            openVisitsTable ? openVisitsTable.draw(false) : ''
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

    changeBillSpan.addEventListener('click', function () {
        const visitId = changeBillSpan.getAttribute('data-visitid')

        if ($.fn.DataTable.isDataTable('#billTable')) {
            $('#billTable').dataTable().fnDestroy()
        }

        if (changeBillSpan.innerHTML == 'Summary'){
            changeBillSpan.innerHTML = 'Details'
            getPatientsBill('billTable', visitId, billModal._element, 'sub_category')
        } else {
            changeBillSpan.innerHTML = 'Summary'
            getPatientsBill('billTable', visitId, billModal._element, 'category')
        }
    })


    document.querySelector('#billingTable').addEventListener('mouseover',  function (event) {
        const thirdPartyServiceBtn  = event.target.closest('.thirdPartyServiceBtn')
        if (thirdPartyServiceBtn){
            thirdPartyServiceBtn.style.cursor = 'pointer'
        }
    })

    document.querySelector('#billingTable').addEventListener('click',  function (event) {
            const payBtn                    = event.target.closest('.payBtn')
            const paymentDetailsDiv         = document.querySelector('.paymentDetailsDiv')
            const discountBtn               = event.target.closest('.discountBtn')
            const outstandingsBtn           = event.target.closest('.outstandingsBtn')
            const sponsorOutstandingsBtn    = event.target.closest('.sponsorOutstandingsBtn')
            const cardNoOutstandingsBtn     = event.target.closest('.cardNoOutstandingsBtn')
            const thirdPartyServiceBtn      = event.target.closest('.thirdPartyServiceBtn')
            const registerBillReminderBtn   = event.target.closest('.registerBillReminderBtn')
            

            if (payBtn) {
                payBtn.setAttribute('disabled', 'disabled')

                const visitId   = payBtn.getAttribute('data-id')
                const patientId = payBtn.getAttribute('data-patientid')

                let data = {...getDivData(paymentDetailsDiv), visitId, patientId}

                http.post('/billing/pay', {...data}, {'html': paymentDetailsDiv})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        new Toast(paymentDetailsDiv.querySelector('#savePaymentToast'), {delay:2000}).show()
                        billingTable ? billingTable.draw() : ''
                        paymentTable ? paymentTable.draw() : ''
                        clearDivValues(paymentDetailsDiv)
                        clearValidationErrors(paymentDetailsDiv)
                    }

                    payBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                    payBtn.removeAttribute('disabled')
                })
            }

            if (discountBtn){
                const visitId    = discountBtn.getAttribute('data-id')
                const discountInput      = discountBtn.parentElement.querySelector('.discountInput')
                discountBtn.classList.add('d-none')
                discountInput.classList.remove('d-none')
                resetFocusEndofLine(discountInput)
                
                discountInput.addEventListener('blur', function () {
                    if (discountInput.value){
                        http.patch(`/billing/discount/${visitId}`, {discount: discountInput.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable( '#billingTable' )){
                                    $('#billingTable').dataTable().fnDraw()
                                }
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                            if ($.fn.DataTable.isDataTable( '#billingTable' )){
                                $('#billingTable').dataTable().fnDraw()
                            }
                        })
                    } else {
                        if ($.fn.DataTable.isDataTable( '#billingTable' )){
                            $('#billingTable').dataTable().fnDraw()
                        }
                    }                 
                })
            }

            if (outstandingsBtn){
                const patientId = outstandingsBtn.dataset.patientid
                const sponsorCat    = outstandingsBtn.dataset.sponsorcat
                getPatientsVisitsByFilterTable('outstandingBillsTable', '', 'outstandings', patientId, '', '', sponsorCat)
                outstandingBillsModal.show()
                billingModal.hide()
            }

            if (sponsorOutstandingsBtn){
                const sponsorId = sponsorOutstandingsBtn.dataset.sponsorid
                getPatientsVisitsByFilterTable('outstandingBillsTable', '', 'outstandings', '', sponsorId)
                outstandingBillsModal.show()
                billingModal.hide()
            }

            if (cardNoOutstandingsBtn){
                const cardNo        = cardNoOutstandingsBtn.dataset.cardno
                const sponsorCat    = cardNoOutstandingsBtn.dataset.sponsorcat
                getPatientsVisitsByFilterTable('outstandingBillsTable', '', 'outstandings', '', '', cardNo, sponsorCat)
                outstandingBillsModal.show()
                billingModal.hide()
            }

            if (thirdPartyServiceBtn){
                thirdPartyServiceModal._element.querySelector('#patient').value = thirdPartyServiceBtn.getAttribute('data-patient')
                thirdPartyServiceModal._element.querySelector('#service').value = thirdPartyServiceBtn.getAttribute('data-service')
                thirdPartyServiceModal._element.querySelector('#saveThirPartyServiceBtn').setAttribute('data-id', thirdPartyServiceBtn.getAttribute('data-id'))
                thirdPartyServiceModal.show()
            }

            if (registerBillReminderBtn){
                registerBillReminderModal._element.querySelector('#patient').value = registerBillReminderBtn.getAttribute('data-patient')
                registerBillReminderModal._element.querySelector('#sponsor').value = registerBillReminderBtn.getAttribute('data-sponsor')
                saveBillReminderBtn.setAttribute('data-id', registerBillReminderBtn.getAttribute('data-id'))
                registerBillReminderModal.show()
            }
    })

    saveBillReminderBtn.addEventListener('click', function () {
        const visitId = saveBillReminderBtn.getAttribute('data-id')
        saveBillReminderBtn.setAttribute('disabled', 'disabled')

        let data = {...getDivData(registerBillReminderModal._element), visitId }

        http.post('/reminders/cash', {...data}, {"html": registerBillReminderModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                registerBillReminderModal.hide()
                    clearDivValues(registerBillReminderModal._element)
                    clearValidationErrors(registerBillReminderModal._element)
                    billingTable ? billingTable.draw(false) : ''
                }
                saveBillReminderBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            // hmoReportsTable ? hmoReportsTable.draw(false) : ''
            saveBillReminderBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    dischargeBillBtn.addEventListener('click', function () {
        // dischargeBillModal._element.querySelector('#note').value = 'Discharge Bill'
        dischargeBillModal.show()
    })

    addBillBtn.addEventListener('click', function () {
        addBillBtn.setAttribute('disabled', 'disabled')
        const [visitId, conId] = [addBillBtn.getAttribute('data-visitid'), addBillBtn.getAttribute('data-conid')]
        http.post(`/billing/dischargebill`, {...getDivData(dischargeBillModal._element), visitId, conId}, {"html": dischargeBillModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    dischargeBillModal.hide()
                    clearDivValues(dischargeBillModal._element)
                    clearValidationErrors(dischargeBillModal._element)
                    billingTable ? billingTable.draw(false) : ''
                    paymentTable ? paymentTable.draw(false) : ''
                }
                addBillBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            addBillBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    saveThirPartyServiceBtn.addEventListener('click', function () {
        saveThirPartyServiceBtn.setAttribute('disabled', 'disabled')
        const thirdPartyId = saveThirPartyServiceBtn.getAttribute('data-id')
        http.post(`/thirdpartyservices/${thirdPartyId}`, getDivData(thirdPartyServiceModal._element), {"html": thirdPartyServiceModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                billingTable ? billingTable.draw() : ''
                thirdPartyServiceModal.hide()
            }
            saveThirPartyServiceBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveThirPartyServiceBtn.removeAttribute('disabled')
            console.log(error.response.data.data.message)
        })
    })

    document.querySelector('#paymentTable').addEventListener('click', function (event) {
        const deleteBtn = event.target.closest('.deleteBtn')

        if (deleteBtn){
            const id = deleteBtn.getAttribute('data-id')
            const tableId = deleteBtn.getAttribute('data-table')
            
            if (confirm('Are you sure you want to delete this payment?')) {
                deleteBtn.setAttribute('disabled', 'disabled')
                http.delete(`/billing/payment/delete/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            if ($.fn.DataTable.isDataTable('#'+tableId)){
                            $('#'+tableId).dataTable().fnDraw()
                            }
                            if ($.fn.DataTable.isDataTable('#billingTable')){
                            $('#billingTable').dataTable().fnDraw()
                            }
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        if (error.response.status === 403){
                            alert(error.response.data.message); 
                        }
                        console.log(error)
                        deleteBtn.removeAttribute('disabled')
                    })
            }
            
        }
    })

    saveExpenseBtn.addEventListener('click', function () {
        saveExpenseBtn.setAttribute('disabled', 'disabled')
        http.post('/expenses', {...getDivData(newExpenseModal._element)}, {"html": newExpenseModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newExpenseModal.hide()
                    clearDivValues(newExpenseModal._element)
                    clearValidationErrors(newExpenseModal._element)
                    expensesTable ? expensesTable.draw() : ''
                }
                saveExpenseBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveExpenseBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    document.querySelector('#expensesTable').addEventListener('click', function (event) {
        const editExpenseBtn    = event.target.closest('.editExpenseBtn')
        const deleteExpenseBtn    = event.target.closest('.deleteExpenseBtn')

        if (editExpenseBtn) {
            editExpenseBtn.setAttribute('disabled', 'disabled')
            const expense = editExpenseBtn.getAttribute('data-id')
            http.get(`/expenses/${ expense }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateExpenseModal, updateExpenseBtn, response.data.data)
                    }
                    editExpenseBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    editExpenseBtn.removeAttribute('disabled')
                    alert(error.response.data.data.message)
                })
        }

        if (deleteExpenseBtn){
            deleteExpenseBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Expense?')) {
                const expense = deleteExpenseBtn.getAttribute('data-id')
                http.delete(`/expenses/${expense}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            expensesTable ? expensesTable.draw() : ''
                        }
                        deleteExpenseBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                        deleteExpenseBtn.removeAttribute('disabled')
                    })
            }
        }
    })

    updateExpenseBtn.addEventListener('click', function (event) {
        const expense = event.currentTarget.getAttribute('data-id')
        updateExpenseBtn.setAttribute('disabled', 'disabled')
        http.post(`/expenses/${expense}`, getDivData(updateExpenseModal._element), {"html": updateExpenseModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateExpenseModal.hide()
                clearValidationErrors(updateExpenseModal._element)
                expensesTable ? expensesTable.draw() : ''
            }
            updateExpenseBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            updateExpenseBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    document.querySelectorAll('#billingModal, #billModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function () {
            outPatientsVisitTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            openVisitsTable ? openVisitsTable.draw() : ''
            billingShiftReportTable.draw()
        })
    })

    document.querySelectorAll('#newExpenseModal, #updateExpenseModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function () {
            clearValidationErrors(modal)
        })
    })

    smsTemplateModal._element.addEventListener('hide.bs.modal', function () {
            dueCashRemindersTable.draw()
        })

    outstandingBillsModal._element.addEventListener('hide.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#outstandingBillsTable')){
            $('#outstandingBillsTable').dataTable().fnDestroy()
            }
        outPatientsVisitTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        billingShiftReportTable.draw()
    })

    downloadBillSummaryBtn.addEventListener('click', function () {
        const patient = billSummaryBody.querySelector('.patient').innerHTML

        var opt = {
        margin:       0.3,
        filename:     patient + "'s Bill.pdf",
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(billSummaryBody).save()
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
                billingShiftReportTable.draw(false)
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
                billingShiftReportTable.draw(false)
            }
            saveShiftReportBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveShiftReportBtn.removeAttribute('disabled')
        })
    })

    document.querySelectorAll('#billingShiftReportTable').forEach(table => {
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
                                billingShiftReportTable.draw(false)
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

    document.querySelector('#dueRemindersListTable').addEventListener('click', function (event) {
        const firstReminderSelect   = event.target.closest('.firstReminderSelect')
        const secondReminderSelect  = event.target.closest('.secondReminderSelect')
        const finalReminderSelect   = event.target.closest('.finalReminderSelect')
        // const confirmedPaidInput    = event.target.closest('.confirmedPaidInput')
        const confirmedPaidBtn    = event.target.closest('.confirmedPaidBtn')
        const smsOption             = event.target.closest('.smsOption')
        const dueRemindersFieldset  = document.querySelector('#dueRemindersFieldset')

       if (firstReminderSelect){
            const reminderId = firstReminderSelect.getAttribute('data-id')

            firstReminderSelect.addEventListener('change', function () {
                if (smsMessenger(firstReminderSelect)){
                    http.get(`/reminders/smsdetails/${reminderId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                openModals(smsTemplateModal, sendSmsBtn, response.data.data)
                                sendSmsBtn.setAttribute('data-select', 'firstReminderSelect')
                                sendSmsBtn.setAttribute('data-reminder', firstReminderSelect.value)
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                        })
                } else {
                    dueRemindersFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/reminders/firstreminder/${reminderId}`, {reminder: firstReminderSelect.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                dueCashRemindersTable.draw()
                                dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset)) 
                            }
                        })
                        .catch((error) => {
                            dueCashRemindersTable.draw()
                            dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            console.log(error)
                        })               
                }
            })
        }

       if (secondReminderSelect){
            const reminderId  = secondReminderSelect.getAttribute('data-id')
                
            secondReminderSelect.addEventListener('change', function () {
                if (smsMessenger(secondReminderSelect)){
                    http.get(`/reminders/smsdetails/${reminderId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                openModals(smsTemplateModal, sendSmsBtn, response.data.data)
                                sendSmsBtn.setAttribute('data-select', 'secondReminderSelect')
                                sendSmsBtn.setAttribute('data-reminder', secondReminderSelect.value)
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                        })

                } else {
                    dueRemindersFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/reminders/secondreminder/${reminderId}`, {reminder: secondReminderSelect.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                    dueCashRemindersTable.draw()
                                    dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset)) 
                            }
                        })
                        .catch((error) => {
                            dueCashRemindersTable.draw()
                            dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            console.log(error)
                        })               
                }
            })
        }

       if (finalReminderSelect){
            const reminderId  = finalReminderSelect.getAttribute('data-id')
                
            finalReminderSelect.addEventListener('change', function () {
                if (smsMessenger(finalReminderSelect)){
                    http.get(`/reminders/smsdetails/${reminderId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                openModals(smsTemplateModal, sendSmsBtn, response.data.data)
                                sendSmsBtn.setAttribute('data-select', 'finalReminderSelect')
                                sendSmsBtn.setAttribute('data-reminder', finalReminderSelect.value)
                            }
                        })
                        .catch((error) => {
                            console.log(error)
                        })
                } else {
                    dueRemindersFieldset.setAttribute('disabled', 'disabled')
                        http.patch(`/reminders/finalreminder/${reminderId}`, {reminder: finalReminderSelect.value})
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                    dueCashRemindersTable.draw()
                                    dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            }
                        })
                        .catch((error) => {
                            dueCashRemindersTable.draw()
                            dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
                            console.log(error)
                        })               
                }
            })
        }

    //    if (confirmedPaidInput){
    //         const reminderId  = confirmedPaidInput.getAttribute('data-id')
                
    //         confirmedPaidInput.addEventListener('blur', function () {
    //             dueRemindersFieldset.setAttribute('disabled', 'disabled')
    //                 http.patch(`/reminders/confirmedpaid/${reminderId}`, {confirmedPayDate: confirmedPaidInput.value})
    //                 .then((response) => {
    //                     if (response.status >= 200 || response.status <= 300) {
    //                             dueCashRemindersTable.draw()
    //                             dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
    //                     }
    //                 })
    //                 .catch((error) => {
    //                     dueCashRemindersTable.draw()
    //                     dueCashRemindersTable.on('draw', removeDisabled(dueRemindersFieldset))
    //                     console.log(error)
    //                 })               
    //         })
    //     }

        if (confirmedPaidBtn){
            savePaymentBtn.setAttribute('data-id', confirmedPaidBtn.getAttribute('data-id'))
            confirmPaymentModal._element.querySelector('#patient').value = confirmedPaidBtn.getAttribute('data-patient')
            confirmPaymentModal.show()
        }
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
        dueCashRemindersTable.draw()
    })

    sendSmsBtn.addEventListener('click', function () {
        const reminderId  = sendSmsBtn.getAttribute('data-id')
        const selectEl  = sendSmsBtn.getAttribute('data-select')
        const reminder  = sendSmsBtn.getAttribute('data-reminder')

        let data = {...getDivData(smsTemplateModal._element), selectEl, reminder}
        
        sendSmsBtn.setAttribute('disabled', 'disabled')
        http.post(`/reminders/sendsms/${reminderId}`, {...data}, {"html": smsTemplateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                smsTemplateModal.hide()
                    clearDivValues(smsTemplateModal._element)
                    clearValidationErrors(smsTemplateModal._element)
                }
                sendSmsBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            sendSmsBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })
})

const smsMessenger = (selectElement) => {
        return selectElement.value == 'Texted'
}