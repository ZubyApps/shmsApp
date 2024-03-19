import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getBySponsorTable, getDistribution1Table, getDistribution2Table, getFrequencyTable, getRegBySponsorTable } from "./tables/patientReportTables";
import { getByResourceTable, getMedServiceSummaryTable } from "./tables/medReportTables";

window.addEventListener('DOMContentLoaded', function () {
    const byResourceModal            = new Modal(document.getElementById('byResourceModal'))

    const datesDiv                  = document.querySelector('.datesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')
    const searchMedServiceByMonthBtn = document.querySelector('.searchMedServiceByMonthBtn')

    let medServicesTable, byResourceTable, frequencyTable, distribution2Table, registrationTable
    medServicesTable = getMedServiceSummaryTable('summaryTable')
    datesDiv.querySelector('#medServiceMonth').value = new Date().toISOString().slice(0,7)

    summaryTab.addEventListener('click', function() {
        medServicesTable.draw()
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