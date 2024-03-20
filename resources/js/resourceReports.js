import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getByResourceCategoryTable, getResourceValueSummaryTable, getUsedResourcesSummaryTable } from "./tables/resourceReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourceCategoryModal    = new Modal(document.getElementById('byResourceCategoryModal'))

    const datesDiv                  = document.querySelector('.datesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')
    const usedSummaryTab                = document.querySelector('#nav-usedSummary-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')
    const searchResourcesByMonthBtn = document.querySelector('.searchResourcesByMonthBtn')

    let resourceValuesTable, usedSummaryTable, byResourceCategoryTable
    resourceValuesTable = getResourceValueSummaryTable('summaryTable')

    summaryTab.addEventListener('click', function() {
        resourceValuesTable.draw()
    })

    usedSummaryTab.addEventListener('click', function() {
        datesDiv.querySelector('#resourcesMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#usedSummaryTable' )){
            $('#usedSummaryTable').dataTable().fnDraw()
        } else {
            usedSummaryTable = getUsedResourcesSummaryTable('usedSummaryTable')
        }
    })

    searchWithDatesBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#usedSummaryTable' )){
            $('#usedSummaryTable').dataTable().fnDestroy()
        }
        usedSummaryTable = getUsedResourcesSummaryTable('usedSummaryTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    searchResourcesByMonthBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#usedSummaryTable' )){
            $('#usedSummaryTable').dataTable().fnDestroy()
        }
        usedSummaryTable = getUsedResourcesSummaryTable('usedSummaryTable', null, null, datesDiv.querySelector('#resourcesMonth').value)
    })

    document.querySelector('#usedSummaryTable').addEventListener('click', function (event) {
        const showPrescriptionsBtn    = event.target.closest('.showPrescriptionsBtn')
        const from                    = datesDiv.querySelector('#startDate').value
        const to                 = datesDiv.querySelector('#endDate').value
        const date               = datesDiv.querySelector('#resourcesMonth').value

        if (showPrescriptionsBtn){
            const id = showPrescriptionsBtn.getAttribute('data-id')
            byResourceCategoryModal._element.querySelector('#category').value = showPrescriptionsBtn.getAttribute('data-category')
            
            if (date){
                byResourceCategoryModal._element.querySelector('#resourcesMonth').value = date
                byResourceCategoryTable = getByResourceCategoryTable('byResourceCategoryTable', id, byResourceCategoryModal, null, null, datesDiv.querySelector('#resourcesMonth').value)
                byResourceCategoryModal.show()
                return
            }
            
            if (datesDiv.querySelector('#startDate').value && datesDiv.querySelector('#endDate').value){
                byResourceCategoryModal._element.querySelector('#from').value = from
                byResourceCategoryModal._element.querySelector('#to').value = to
                byResourceCategoryTable = getByResourceCategoryTable('byResourceCategoryTable', id, byResourceCategoryModal, datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
                byResourceCategoryModal.show()
                return
                
            }
            byResourceCategoryModal._element.querySelector('#resourcesMonth').value = new Date().toISOString().slice(0,7)
            byResourceCategoryTable = getByResourceCategoryTable('byResourceCategoryTable', id, byResourceCategoryModal)
            byResourceCategoryModal.show()
        }
    })
})