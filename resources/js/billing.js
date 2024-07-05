import { Offcanvas, Modal, Toast } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getDivData, clearValidationErrors, resetFocusEndofLine, openModals, displayMedicalReportModal} from "./helpers"
import { getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit, getPatientsBill, getExpensesTable, getBalancingTable } from "./tables/billingTables";
import { getOutpatientsInvestigationTable } from "./tables/investigationTables";
import html2pdf  from "html2pdf.js"
import { getShiftReportTable } from "./tables/pharmacyTables";
import { getMedicalReportTable } from "./tables/doctorstables";
$.fn.dataTable.ext.errMode = 'throw';


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas             = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
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

    const balancingDateDiv              = document.querySelector('.balancingDateDiv')

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

    const outPatientsTab                = document.querySelector('#nav-outPatients-tab')
    const inPatientsTab                 = document.querySelector('#nav-inPatients-tab')
    const ancPatientsTab                = document.querySelector('#nav-ancPatients-tab')
    const openVisitsTab                 = document.querySelector('#nav-openVisits-tab')
    const expensesTab                   = document.querySelector('#nav-expenses-tab')
    const balancingTab                  = document.querySelector('#nav-balancing-tab')

    const reportModalBody               = viewMedicalReportModal._element.querySelector('.reportModalBody')
    const patientsFullName              = viewMedicalReportModal._element.querySelector('#patientsFullName')
    const patientsInfo                  = viewMedicalReportModal._element.querySelector('#patientsInfo')

    const changeBillSpan                = billModal._element.querySelector('.changeBill')
    const downloadBillSummaryBtn        = billModal._element.querySelector('#downloadBillSummaryBtn')
    const billSummaryBody               = billModal._element.querySelector('.billSummaryBody')
    const shiftBadgeSpan                = document.querySelector('#shiftBadgeSpan')


    let inPatientsVisitTable, ancPatientsVisitTable, billingTable, paymentTable, openVisitsTable, expensesTable, balancingTable, medicalReportTable

    const outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient', 'consulted')
    const waitingTable = getWaitingTable('waitingTable')
    const outpatientInvestigationTable = getOutpatientsInvestigationTable('outpatientInvestigationsTable', true)
    const billingShiftReportTable = getShiftReportTable('billingShiftReportTable', 'billing', shiftBadgeSpan)

    $('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #outpatientInvestigationsTable, #waitingTable, #billingTable, #openVisitsTable, #expensesTable, #balancingTable, #billingShiftReportTable').on('error.dt', function(e, settings, techNote, message) {techNote == 7 ? window.location.reload() : ''})    

    outPatientsTab.addEventListener('click', function() {outPatientsVisitTable.draw()})
    outpatientsInvestigationBtn.addEventListener('click', function () {outpatientInvestigationTable.draw()})
    shiftReportBtn.addEventListener('click', function () {billingShiftReportTable.draw()})

    newBillingReportBtn.addEventListener('click', function () {
        newShiftReportTemplateModal.show()
    })

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitsByFilterTable('inPatientsVisitTable', 'Inpatient', 'consulted')
        }
        billingShiftReportTable.draw()
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('ancPatientsVisitTable', 'ANC', 'consulted')
        }
        billingShiftReportTable.draw()
    })

    openVisitsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#openVisitsTable' )){
            $('#openVisitsTable').dataTable().fnDraw()
        } else {
            openVisitsTable = getPatientsVisitsByFilterTable('openVisitsTable', '', 'openvisits')
        }
        billingShiftReportTable.draw()
    })

    expensesTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#expensesTable' )){
            $('#expensesTable').dataTable().fnDraw()
        } else {
            expensesTable = getExpensesTable('expensesTable', 'billing')
        }
        billingShiftReportTable.draw()
    })

    balancingTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#balancingTable' )){
            $('#balancingTable').dataTable().fnDraw()
        } else {
            balancingTable = getBalancingTable('balancingTable')
        }
        billingShiftReportTable.draw()
    })

    searchBalanceByDateBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#balancingTable' )){
            $('#balancingTable').dataTable().fnDestroy()
        }
        balancingTable = getBalancingTable('balancingTable', null, balancingDateDiv.querySelector('#balanceDate').value)
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

    document.querySelectorAll('#waitingListOffcanvas2, #offcanvasInvestigations, #viewShiftReportTemplateModal, #newShiftReportTemplateModal').forEach(canvas => {
        canvas.addEventListener('hide.bs.offcanvas', function () {
            outPatientsVisitTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
            billingShiftReportTable.draw()
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
                            outPatientsVisitTable.draw()
                            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
                            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
                            openVisitsTable ? openVisitsTable.draw() : ''
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
            const payBtn                = event.target.closest('.payBtn')
            const paymentDetailsDiv     = document.querySelector('.paymentDetailsDiv')
            const discountBtn           = event.target.closest('.discountBtn')
            const outstandingsBtn       = event.target.closest('.outstandingsBtn')
            const thirdPartyServiceBtn  = event.target.closest('.thirdPartyServiceBtn')
            

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
                getPatientsVisitsByFilterTable('outstandingBillsTable', '', 'outstandings', patientId)
                outstandingBillsModal.show()
                billingModal.hide()
            }

            if (thirdPartyServiceBtn){
                thirdPartyServiceModal._element.querySelector('#patient').value = thirdPartyServiceBtn.getAttribute('data-patient')
                thirdPartyServiceModal._element.querySelector('#service').value = thirdPartyServiceBtn.getAttribute('data-service')
                thirdPartyServiceModal._element.querySelector('#saveThirPartyServiceBtn').setAttribute('data-id', thirdPartyServiceBtn.getAttribute('data-id'))
                thirdPartyServiceModal.show()
            }
    })

    dischargeBillBtn.addEventListener('click', function () {
        dischargeBillModal._element.querySelector('#note').value = 'Discharge Bill'
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

})