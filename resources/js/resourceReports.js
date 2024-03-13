import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getResourceValueSummaryTable, getUsedResourcesSummaryTable } from "./tables/resourceReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourceModal            = new Modal(document.getElementById('byResourceModal'))

    const datesDiv                  = document.querySelector('.datesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')
    const usedSummaryTab                = document.querySelector('#nav-usedSummary-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')

    let resourceValuesTable, usedSummaryTable, byResourceTable
    resourceValuesTable = getResourceValueSummaryTable('summaryTable')

    summaryTab.addEventListener('click', function() {
        resourceValuesTable.draw()
    })

    usedSummaryTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#usedSummaryTable' )){
            $('#usedSummaryTable').dataTable().fnDraw()
        } else {
            usedSummaryTable = getUsedResourcesSummaryTable('usedSummaryTable')
        }
    })

    searchWithDatesBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        resourceValuesTable = getInvestigationsSummaryTable('summaryTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
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
})