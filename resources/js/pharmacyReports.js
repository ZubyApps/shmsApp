import {Modal } from "bootstrap";
import $ from 'jquery';
import { getByResourceTable, getMissingPharmacySummaryTable, getPharmacySummaryTable } from "./tables/pharmacyReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourcePharmacyModal   = new Modal(document.getElementById('byResourcePharmacyModal'))

    const datesDiv                  = document.querySelector('.datesDiv')
    const missingDatesDiv           = document.querySelector('.missingDatesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')
    const missingTab                = document.querySelector('#nav-missing-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')
    const searchMedicationByMonthBtn = document.querySelector('.searchMedicationByMonthBtn')
    const searchMissingWithDatesBtn  = document.querySelector('.searchMissingWithDatesBtn')
    const searchMissingByMonthBtn    = document.querySelector('.searchMissingByMonthBtn')

    let pharmacyTable, byResourceTable, missingTable
    pharmacyTable = getPharmacySummaryTable('summaryTable')
    datesDiv.querySelector('#pharmacyMonth').value == '' ? datesDiv.querySelector('#pharmacyMonth').value = new Date().toISOString().slice(0,7) : ''

    summaryTab.addEventListener('click', function() {
        pharmacyTable.draw()
    })

    missingTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#missingTable' )){
            $('#missingTable').dataTable().fnDraw()
        } else {
            missingTable = getMissingPharmacySummaryTable('missingTable')
        }
    })

    searchWithDatesBtn.addEventListener('click', function () {
        datesDiv.querySelector('#pharmacyMonth').value = ''
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
        missingTable = getPharmacySummaryTable('summaryTable', null, null, datesDiv.querySelector('#pharmacyMonth').value)
    })

    searchMissingWithDatesBtn.addEventListener('click', function () {
        missingDatesDiv.querySelector('#missingMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#missingTable' )){
            $('#missingTable').dataTable().fnDestroy()
        }
        missingTable = getMissingPharmacySummaryTable('missingTable', missingDatesDiv.querySelector('#startDate').value, missingDatesDiv.querySelector('#endDate').value)
    })

    searchMissingByMonthBtn.addEventListener('click', function () {
        missingDatesDiv.querySelector('#startDate').value = ''; missingDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#missingTable' )){
            $('#missingTable').dataTable().fnDestroy()
        }
        missingTable = getMissingPharmacySummaryTable('missingTable', null, null, missingDatesDiv.querySelector('#missingMonth').value)
    })

    document.querySelector('#summaryTable').addEventListener('click', function (event) {
        const showPatientsBtn    = event.target.closest('.showPatientsBtn')
        const from               = datesDiv.querySelector('#startDate').value
        const to                 = datesDiv.querySelector('#endDate').value
        const date               = datesDiv.querySelector('#pharmacyMonth').value

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