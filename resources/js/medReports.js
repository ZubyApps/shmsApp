import {Modal } from "bootstrap";
import $ from 'jquery';
import { getByResourceTable, getDischargeReasonTable, getDischargeSummaryTable, getMedServiceSummaryTable, getNewBirthsTable } from "./tables/medReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourceModal            = new Modal(document.getElementById('byResourceModal'))
    const visitsByDischargeModal     = new Modal(document.getElementById('visitsByDischargeModal'))

    const datesDiv                  = document.querySelector('.datesDiv')
    const newBirthsDatesDiv         = document.querySelector('.newBirthsDatesDiv')
    const dischargeSummaryDatesDiv  = document.querySelector('.dischargeSummaryDatesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')
    const newBirthsTab              = document.querySelector('#nav-newBirths-tab')
    const dischargeSummaryTab       = document.querySelector('#nav-dischargeSummary-tab')

    const searchWithDatesBtn           = document.querySelector('.searchWithDatesBtn')
    const searchMedServiceByMonthBtn   = document.querySelector('.searchMedServiceByMonthBtn')

    const searchNewBirthsWithDatesBtn  = document.querySelector('.searchNewBirthsWithDatesBtn')
    const searchNewBirthsByMonthBtn    = document.querySelector('.searchNewBirthsByMonthBtn')

    const searchDischargeSummaryWithDatesBtn   = document.querySelector('.searchDischargeSummaryWithDatesBtn')
    const searchDischargeSummaryByMonthBtn     = document.querySelector('.searchDischargeSummaryByMonthBtn')

    let medServicesTable, newBirthsTable, referredTable, deceasedTable, dischargeSummaryTable, byResourceTable, dischargeReasonTable
    medServicesTable = getMedServiceSummaryTable('summaryTable')
    datesDiv.querySelector('#medServiceMonth').value = new Date().toISOString().slice(0,7)

    summaryTab.addEventListener('click', function() {
        medServicesTable.draw()
    })

    newBirthsTab.addEventListener('click', function() {
        newBirthsDatesDiv.querySelector('#newBirthsMonth').value == '' ? newBirthsDatesDiv.querySelector('#newBirthsMonth').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#newBirthsTable' )){
            $('#newBirthsTable').dataTable().fnDraw()
        } else {
            newBirthsTable = getNewBirthsTable('newBirthsTable')
        }
    })

    dischargeSummaryTab.addEventListener('click', function() {
        dischargeSummaryDatesDiv.querySelector('#dischargeSummaryMonth').value == '' ? dischargeSummaryDatesDiv.querySelector('#dischargeSummaryMonth').value = new Date().toISOString().slice(0,7) : ''
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

    document.querySelectorAll('#summaryTable, #dischargeSummaryTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const showPatientsBtn   = event.target.closest('.showPatientsBtn')
            const medFrom           = datesDiv.querySelector('#startDate').value
            const medTo             = datesDiv.querySelector('#endDate').value
            const medDate           = datesDiv.querySelector('#medServiceMonth').value
            const showVisitsBtn     = event.target.closest('.showVisitsBtn')
            const dischargedFrom    = dischargeSummaryDatesDiv.querySelector('#startDate').value
            const dischargedTo      = dischargeSummaryDatesDiv.querySelector('#endDate').value
            const dischargedDate    = dischargeSummaryDatesDiv.querySelector('#dischargeSummaryMonth').value
    
            if (showPatientsBtn){
                const id = showPatientsBtn.getAttribute('data-id')
                byResourceModal._element.querySelector('#resource').value = showPatientsBtn.getAttribute('data-resource') 
                byResourceModal._element.querySelector('#subcategory').value = showPatientsBtn.getAttribute('data-subcategory')
                
                if (medDate) {
                    byResourceModal._element.querySelector('#resourceMonth').value = medDate
                    byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal, null, null, medDate)
                    byResourceModal.show()
                    return
                }
                
                if (medFrom && medTo ){
                    byResourceModal._element.querySelector('#from').value = medFrom
                    byResourceModal._element.querySelector('#to').value = medTo
                    byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal, medFrom, medTo)
                    byResourceModal.show()
                    return
                }

                byResourceModal._element.querySelector('#resourceMonth').value = new Date().toISOString().slice(0,7)
                byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal)
                byResourceModal.show()
            }

            if (showVisitsBtn){
                const reason = showVisitsBtn.getAttribute('data-reason')
                visitsByDischargeModal._element.querySelector('#dischargeReason').value = reason == "null" ? 'Not discharged' : reason;
                
                if (dischargedDate) {
                    visitsByDischargeModal._element.querySelector('#dischargeMonth').value = dischargedDate
                    dischargeReasonTable = getDischargeReasonTable('dischargeReasonTable', reason, visitsByDischargeModal, null, null, dischargedDate)
                    visitsByDischargeModal.show()
                    return
                }
                
                if (dischargedFrom && dischargedTo ){
                    visitsByDischargeModal._element.querySelector('#from').value = medFrom
                    visitsByDischargeModal._element.querySelector('#to').value = medTo
                    dischargeReasonTable = getDischargeReasonTable('dischargeReasonTable', reason, visitsByDischargeModal, dischargedFrom, dischargedTo)
                    visitsByDischargeModal.show()
                    return
                }

                visitsByDischargeModal._element.querySelector('#resourceMonth').value = new Date().toISOString().slice(0,7)
                dischargeReasonTable = getDischargeReasonTable('dischargeReasonTable', reason, visitsByDischargeModal)
                visitsByDischargeModal.show()
            }
        })
    })
})