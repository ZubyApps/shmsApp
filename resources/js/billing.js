import { Offcanvas, Modal, Toast } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { clearDivValues, getDivData, clearValidationErrors, resetFocusEndofLine} from "./helpers"
import { getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit, getPatientsBill } from "./tables/billingTables";
import { getOutpatientsInvestigationTable } from "./tables/investigationTables";
import html2pdf  from "html2pdf.js"


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas             = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
    const billingModal                  = new Modal(document.getElementById('billingModal'))
    const outstandingBillsModal         = new Modal(document.getElementById('outstandingBillsModal'))
    const billModal                     = new Modal(document.getElementById('billModal'))

    const waitingBtn                    = document.querySelector('#waitingBtn')
    const outpatientsInvestigationBtn   = document.querySelector('#outpatientsInvestigationBtn')

    const outPatientsTab                = document.querySelector('#nav-outPatients-tab')
    const inPatientsTab                 = document.querySelector('#nav-inPatients-tab')
    const ancPatientsTab                = document.querySelector('#nav-ancPatients-tab')
    const changeBillSpan                = billModal._element.querySelector('.changeBill')
    const downloadBillSummaryBtn        = billModal._element.querySelector('#downloadBillSummaryBtn')
    const billSummaryBody               = billModal._element.querySelector('.billSummaryBody')


    let inPatientsVisitTable, ancPatientsVisitTable

    const outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient', 'consulted')
    const waitingTable = getWaitingTable('waitingTable')
    const outpatientInvestigationTable = getOutpatientsInvestigationTable('outpatientInvestigationsTable', true)

    outPatientsTab.addEventListener('click', function() {outPatientsVisitTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitsByFilterTable('inPatientsVisitTable', 'Inpatient', 'consulted')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('ancPatientsVisitTable', 'ANC', 'consulted')
        }
    })

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    document.querySelectorAll('#waitingListOffcanvas2, #offcanvasInvestigations').forEach(canvas => {
        canvas.addEventListener('hide.bs.offcanvas', function () {
            outPatientsVisitTable.draw()
            inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
            ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
        })

    })

    outpatientsInvestigationBtn.addEventListener('click', function () {
        outpatientInvestigationTable.draw()
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

    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #outstandingBillsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const billingDetailsBtn = event.target.closest('.consultationDetailsBtn')
            const patientsBillBtn   = event.target.closest('.patientsBillBtn')
            const closeVisitBtn     = event.target.closest('.closeVisitBtn')
            
            if (billingDetailsBtn){
                const visitId = billingDetailsBtn.getAttribute('data-id') 
                getbillingTableByVisit('billingTable', visitId, billingModal._element, true)
                getPaymentTableByVisit('paymentTable', visitId, billingModal._element)
                outstandingBillsModal.hide()
                billingModal.show()
            }

            if (patientsBillBtn){
                const visitId   = patientsBillBtn.getAttribute('data-id')
                billModal._element.querySelector('.patient').innerHTML      = patientsBillBtn.getAttribute('data-patient')
                billModal._element.querySelector('.billingStaff').innerHTML = patientsBillBtn.getAttribute('data-staff')
                changeBillSpan.setAttribute('visitid', visitId)
                getPatientsBill('billTable', visitId, billModal._element, 'category')
                billModal.show()
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

    changeBillSpan.addEventListener('click', function () {
        const visitId = changeBillSpan.getAttribute('visitid')

        if ($.fn.DataTable.isDataTable('#billTable')) {
            $('#billTable').dataTable().fnDestroy()
        }

        if (changeBillSpan.innerHTML == 'Summary'){
            changeBillSpan.innerHTML = 'Details'
            getPatientsBill('billTable', visitId, billModal._element, 'name')
        } else {
            changeBillSpan.innerHTML = 'Summary'
            getPatientsBill('billTable', visitId, billModal._element, 'category')
        }
    })


    document.querySelector('#billingTable').addEventListener('click',  function (event) {
            const payBtn = event.target.closest('.payBtn')
            const paymentDetailsDiv = document.querySelector('.paymentDetailsDiv')
            const discountBtn       = event.target.closest('.discountBtn')
            const outstandingsBtn    = event.target.closest('.outstandingsBtn')

            if (payBtn) {
                payBtn.setAttribute('disabled', 'disabled')

                const visitId   = payBtn.getAttribute('data-id')
                const patientId = payBtn.getAttribute('data-patientid')

                let data = {...getDivData(paymentDetailsDiv), visitId, patientId}

                http.post('/billing/pay', {...data}, {'html': paymentDetailsDiv})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        new Toast(paymentDetailsDiv.querySelector('#savePaymentToast'), {delay:2000}).show()
                        clearDivValues(paymentDetailsDiv)
                        clearValidationErrors(paymentDetailsDiv)
                    }
                    if ($.fn.DataTable.isDataTable( '#billingTable' )){
                        $('#billingTable').dataTable().fnDraw()
                    }
                    if ($.fn.DataTable.isDataTable( '#paymentTable' )){
                        $('#paymentTable').dataTable().fnDraw()
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

    billingModal._element.addEventListener('hide.bs.modal', function () {
        outPatientsVisitTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    outstandingBillsModal._element.addEventListener('hide.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#outstandingBillsTable')){
            $('#outstandingBillsTable').dataTable().fnDestroy()
            }
        outPatientsVisitTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
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
})