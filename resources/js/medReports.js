import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getBySponsorTable, getDistribution1Table, getDistribution2Table, getFrequencyTable, getRegBySponsorTable } from "./tables/patientReportTables";
import { getByResourceTable, getDischargeSummaryTable, getMedServiceSummaryTable, getNewBirthsTable, getReferredOrDeceasedVisits } from "./tables/medReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourceModal            = new Modal(document.getElementById('byResourceModal'))

    const datesDiv                  = document.querySelector('.datesDiv')
    const newBirthsDatesDiv         = document.querySelector('.newBirthsDatesDiv')
    const referredDatesDiv          = document.querySelector('.referredDatesDiv')
    const deceasedDatesDiv          = document.querySelector('.deceasedDatesDiv')
    const dischargeSummaryDatesDiv  = document.querySelector('.dischargeSummaryDatesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')
    const newBirthsTab              = document.querySelector('#nav-newBirths-tab')
    const referredTab               = document.querySelector('#nav-referred-tab')
    const deceasedTab               = document.querySelector('#nav-deceased-tab')
    const dischargeSummaryTab       = document.querySelector('#nav-dischargeSummary-tab')

    const searchWithDatesBtn           = document.querySelector('.searchWithDatesBtn')
    const searchMedServiceByMonthBtn   = document.querySelector('.searchMedServiceByMonthBtn')

    const searchNewBirthsWithDatesBtn  = document.querySelector('.searchNewBirthsWithDatesBtn')
    const searchNewBirthsByMonthBtn    = document.querySelector('.searchNewBirthsByMonthBtn')

    const searchReferredWithDatesBtn   = document.querySelector('.searchReferredWithDatesBtn')
    const searchReferredByMonthBtn     = document.querySelector('.searchReferredByMonthBtn')

    const searchDeceasedWithDatesBtn   = document.querySelector('.searchDeceasedWithDatesBtn')
    const searchDeceasedByMonthBtn     = document.querySelector('.searchDeceasedByMonthBtn')

    const searchDischargeSummaryWithDatesBtn   = document.querySelector('.searchDischargeSummaryWithDatesBtn')
    const searchDischargeSummaryByMonthBtn     = document.querySelector('.searchDischargeSummaryByMonthBtn')

    let medServicesTable, newBirthsTable, referredTable, deceasedTable, dischargeSummaryTable
    medServicesTable = getMedServiceSummaryTable('summaryTable')
    datesDiv.querySelector('#medServiceMonth').value = new Date().toISOString().slice(0,7)

    summaryTab.addEventListener('click', function() {
        medServicesTable.draw()
    })

    newBirthsTab.addEventListener('click', function() {
        newBirthsDatesDiv.querySelector('#newBirthsMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#newBirthsTable' )){
            $('#newBirthsTable').dataTable().fnDraw()
        } else {
            newBirthsTable = getNewBirthsTable('newBirthsTable')
        }
    })

    referredTab.addEventListener('click', function() {
        referredDatesDiv.querySelector('#referredMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#referredTable' )){
            $('#referredTable').dataTable().fnDraw()
        } else {
            referredTable = getReferredOrDeceasedVisits('referredTable', 'Referred')
        }
    })

    deceasedTab.addEventListener('click', function() {
        deceasedDatesDiv.querySelector('#deceasedMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#deceasedTable' )){
            $('#deceasedTable').dataTable().fnDraw()
        } else {
            deceasedTable = getReferredOrDeceasedVisits('deceasedTable', 'Deceased')
        }
    })

    dischargeSummaryTab.addEventListener('click', function() {
        dischargeSummaryDatesDiv.querySelector('#dischargeSummaryMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#dischargeSummaryTable' )){
            $('#dischargeSummaryTable').dataTable().fnDraw()
        } else {
            dischargeSummaryTable = getDischargeSummaryTable('dischargeSummaryTable')
        }
    })

    searchWithDatesBtn.addEventListener('click', function () {
        datesDiv.querySelector('#medServiceMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        medServicesTable = getMedServiceSummaryTable('summaryTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    searchMedServiceByMonthBtn.addEventListener('click', function () {
        datesDiv.querySelector('#startDate').value = ''; datesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        medServicesTable = getMedServiceSummaryTable('summaryTable', null, null, datesDiv.querySelector('#medServiceMonth').value)
    })

    searchNewBirthsWithDatesBtn.addEventListener('click', function () {
        newBirthsDatesDiv.querySelector('#newBirthsMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#newBirthsTable' )){
            $('#newBirthsTable').dataTable().fnDestroy()
        }
        newBirthsTable = getNewBirthsTable('newBirthsTable', newBirthsDatesDiv.querySelector('#startDate').value, newBirthsDatesDiv.querySelector('#endDate').value)
    })

    searchNewBirthsByMonthBtn.addEventListener('click', function () {
        newBirthsDatesDiv.querySelector('#startDate').value = ''; newBirthsDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#newBirthsTable' )){
            $('#newBirthsTable').dataTable().fnDestroy()
        }
        newBirthsTable = getNewBirthsTable('newBirthsTable', null, null, newBirthsDatesDiv.querySelector('#newBirthsMonth').value)
    })

    searchReferredWithDatesBtn.addEventListener('click', function () {
        referredDatesDiv.querySelector('#referredMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#referredTable' )){
            $('#referredTable').dataTable().fnDestroy()
        }
        referredTable = getReferredOrDeceasedVisits('referredTable', 'Referred', referredDatesDiv.querySelector('#startDate').value, referredDatesDiv.querySelector('#endDate').value)
    })

    searchReferredByMonthBtn.addEventListener('click', function () {
        referredDatesDiv.querySelector('#startDate').value = ''; referredDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#referredTable' )){
            $('#referredTable').dataTable().fnDestroy()
        }
        referredTable = getReferredOrDeceasedVisits('referredTable', 'Referred', null, null, referredDatesDiv.querySelector('#referredMonth').value)
    })

    searchDeceasedWithDatesBtn.addEventListener('click', function () {
        deceasedDatesDiv.querySelector('#deceasedMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#deceasedTable' )){
            $('#deceasedTable').dataTable().fnDestroy()
        }
        deceasedTable = getReferredOrDeceasedVisits('deceasedTable', 'Deceased', deceasedDatesDiv.querySelector('#startDate').value, deceasedDatesDiv.querySelector('#endDate').value)
    })

    searchDeceasedByMonthBtn.addEventListener('click', function () {
        deceasedDatesDiv.querySelector('#startDate').value = ''; referredDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#deceasedTable' )){
            $('#deceasedTable').dataTable().fnDestroy()
        }
        deceasedTable = getReferredOrDeceasedVisits('deceasedTable', 'Deceased', null, null, deceasedDatesDiv.querySelector('#deceasedMonth').value)
    })

    searchDischargeSummaryWithDatesBtn.addEventListener('click', function () {
        dischargeSummaryDatesDiv.querySelector('#dischargeSummaryMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#dischargeSummaryTable' )){
            $('#dischargeSummaryTable').dataTable().fnDestroy()
        }
        dischargeSummaryTable = getDischargeSummaryTable('dischargeSummaryTable', dischargeSummaryDatesDiv.querySelector('#startDate').value, dischargeSummaryDatesDiv.querySelector('#endDate').value)
    })

    searchDischargeSummaryByMonthBtn.addEventListener('click', function () {
        dischargeSummaryDatesDiv.querySelector('#startDate').value = ''; dischargeSummaryDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#dischargeSummaryTable' )){
            $('#dischargeSummaryTable').dataTable().fnDestroy()
        }
        dischargeSummaryTable = getDischargeSummaryTable('dischargeSummaryTable', null, null, dischargeSummaryDatesDiv.querySelector('#dischargeSummaryMonth').value)
    })

    document.querySelectorAll('#summaryTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const showPatientsBtn   = event.target.closest('.showPatientsBtn')
            const from              = datesDiv.querySelector('#startDate').value
            const to                = datesDiv.querySelector('#endDate').value
            const date              = datesDiv.querySelector('#medServiceMonth').value
    
            if (showPatientsBtn){
                const id = showPatientsBtn.getAttribute('data-id')
                byResourceModal._element.querySelector('#resource').value = showPatientsBtn.getAttribute('data-resource') 
                byResourceModal._element.querySelector('#subcategory').value = showPatientsBtn.getAttribute('data-subcategory')
                
                if (date) {
                    byResourceModal._element.querySelector('#resourceMonth').value = date
                    byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal, null, null, date)
                    byResourceModal.show()
                    return
                }
                
                if (from && to ){
                    byResourceModal._element.querySelector('#from').value = from
                    byResourceModal._element.querySelector('#to').value = to
                    byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal, from, to)
                    byResourceModal.show()
                    return
                }

                byResourceModal._element.querySelector('#resourceMonth').value = new Date().toISOString().slice(0,7)
                byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal)
                byResourceModal.show()
            }
        })
    })
})