import {Modal } from "bootstrap";
import $ from 'jquery';
import { getByResourceTable, getPharmacySummaryTable } from "./tables/pharmacyReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourcePharmacyModal   = new Modal(document.getElementById('byResourcePharmacyModal'))

    const datesDiv                  = document.querySelector('.datesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')
    const searchMedicationByMonthBtn = document.querySelector('.searchMedicationByMonthBtn')

    let pharmacyTable, byResourceTable
    pharmacyTable = getPharmacySummaryTable('summaryTable')
    datesDiv.querySelector('#medicationMonth').value = new Date().toISOString().slice(0,7)

    summaryTab.addEventListener('click', function() {
        pharmacyTable.draw()
    })

    searchWithDatesBtn.addEventListener('click', function () {
        datesDiv.querySelector('#medicationMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        pharmacyTable = getPharmacySummaryTable('summaryTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    searchMedicationByMonthBtn.addEventListener('click', function () {
        datesDiv.querySelector('#startDate').value = ''; datesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        pharmacyTable = getPharmacySummaryTable('summaryTable', null, null, datesDiv.querySelector('#medicationMonth').value)
    })

    document.querySelector('#summaryTable').addEventListener('click', function (event) {
        const showPatientsBtn    = event.target.closest('.showPatientsBtn')
        const from               = datesDiv.querySelector('#startDate').value
        const to                 = datesDiv.querySelector('#endDate').value
        const date               = datesDiv.querySelector('#medicationMonth').value

        if (showPatientsBtn){
            const id = showPatientsBtn.getAttribute('data-id')
            byResourcePharmacyModal._element.querySelector('#resource').value = showPatientsBtn.getAttribute('data-resource') 
            byResourcePharmacyModal._element.querySelector('#subcategory').value = showPatientsBtn.getAttribute('data-subcategory')

            if (date) {
                byResourcePharmacyModal._element.querySelector('#resourceMonth').value = date
                byResourceTable = getByResourceTable('byResourceTable', id, byResourcePharmacyModal, null, null, date)
                byResourcePharmacyModal.show()
                return
            }
            
            if (from && to ){
                byResourcePharmacyModal._element.querySelector('#from').value = from
                byResourcePharmacyModal._element.querySelector('#to').value = to
                byResourceTable = getByResourceTable('byResourceTable', id, byResourcePharmacyModal, from, to)
                byResourcePharmacyModal.show()
                return
            }

            byResourcePharmacyModal._element.querySelector('#resourceMonth').value = new Date().toISOString().slice(0,7)
            byResourceTable = getByResourceTable('byResourceTable', id, byResourcePharmacyModal)
            byResourcePharmacyModal.show()
        }
    })
})