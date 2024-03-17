import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getAccountsSummaryTable, getCapitationPaymentsTable } from "./tables/accountReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourceModal            = new Modal(document.getElementById('byResourceModal'))

    const datesDiv                  = document.querySelector('.datesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')
    const capitationPaymentsTab     = document.querySelector('#nav-capitationPayments-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')

    let payMethodsSummmaryTable, capitationPaymentsTable, byResourceTable
    payMethodsSummmaryTable = getAccountsSummaryTable('summaryTable')

    summaryTab.addEventListener('click', function() {
        payMethodsSummmaryTable.draw()
    })

    capitationPaymentsTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#capitationPaymentsTable' )){
            $('#capitationPaymentsTable').dataTable().fnDraw()
        } else {
            capitationPaymentsTable = getCapitationPaymentsTable('capitationPaymentsTable')
        }
    })

    searchWithDatesBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        payMethodsSummmaryTable = getAccountsSummaryTable('summaryTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    document.querySelector('#summaryTable').addEventListener('click', function (event) {
        const showPatientsBtn    = event.target.closest('.showPatientsBtn')
        const from               = datesDiv.querySelector('#startDate').value
        const to                 = datesDiv.querySelector('#endDate').value

        if (showPatientsBtn){
            const id = showPatientsBtn.getAttribute('data-id')
            byResourceModal._element.querySelector('#resource').value = showPatientsBtn.getAttribute('data-resource') 
            byResourceModal._element.querySelector('#subcategory').value = showPatientsBtn.getAttribute('data-subcategory')
            byResourceModal._element.querySelector('#from').value = from
            byResourceModal._element.querySelector('#to').value = to
            byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal, datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
            byResourceModal.show()
        }
    })

    document.querySelector('#capitationPaymentsTable').addEventListener('click', function (event) {
        const deletePaymentBtn    = event.target.closest('.deletePaymentBtn')

        if (deletePaymentBtn){
            deletePaymentBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Capitation Payment?')) {
                const capitationPayment = deletePaymentBtn.getAttribute('data-id')
                http.delete(`/capitation/${capitationPayment}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            capitationPaymentsTable ? capitationPaymentsTable.draw() : ''
                        }
                        deletePaymentBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })
})