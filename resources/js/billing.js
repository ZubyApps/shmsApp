import { Offcanvas, Modal, Toast } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsInvestigations";
import { getWaitingTable, getPatientsVisitsByFilterTable, getbillingTableByVisit, getPaymentTableByVisit } from "./tables/billingTables";


window.addEventListener('DOMContentLoaded', function () {
    const waitingListCanvas     = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
    const billingModal          = new Modal(document.getElementById('billingModal'))

    const waitingBtn            = document.querySelector('#waitingBtn')

    const outPatientsTab        = document.querySelector('#nav-outPatients-tab')
    const inPatientsTab         = document.querySelector('#nav-inPatients-tab')
    const ancPatientsTab        = document.querySelector('#nav-ancPatients-tab')


    let inPatientsVisitTable, ancPatientsVisitTable

    const outPatientsVisitTable = getPatientsVisitsByFilterTable('outPatientsVisitTable', 'Outpatient')
    const waitingTable = getWaitingTable('waitingTable')

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

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
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

    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const billingDetailsBtn = event.target.closest('.billingDetailsBtn')
            
            if (billingDetailsBtn){
                const visitId = billingDetailsBtn.getAttribute('data-id') 
                getbillingTableByVisit('billingTable', visitId, billingModal._element, true)
                getPaymentTableByVisit('paymentTable', visitId, billingModal._element)
                billingModal.show()
            }
        })
    })

    document.querySelector('#billingTable').addEventListener('click',  function (event) {
            const payBtn = event.target.closest('.payBtn')
            const paymentDetailsDiv = document.querySelector('.paymentDetailsDiv')
            const discountBtn = event.target.closest('.discountBtn')

            if (payBtn) {
                console.log(payBtn)
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
                discountInput.focus()
                
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
                        alert(error)
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
})