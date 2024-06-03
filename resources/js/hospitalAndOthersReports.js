import {Modal } from "bootstrap";
import $ from 'jquery';
import { getByResourceTable, getHospitalAndOthersTable } from "./tables/hospitalAndOthersReportTables";


window.addEventListener('DOMContentLoaded', function () {
    const byResourceModal           = new Modal(document.getElementById('byResourceModal'))

    const datesDiv                  = document.querySelector('.datesDiv')

    const summaryTab                = document.querySelector('#nav-summary-tab')

    const searchWithDatesBtn        = document.querySelector('.searchWithDatesBtn')
    const searchHospitalAndOthersByMonthBtn = document.querySelector('.searchHospitalAndOthersByMonthBtn')

    let hospitalAndOthersTable, byResourceTable
    hospitalAndOthersTable = getHospitalAndOthersTable('summaryTable')
    datesDiv.querySelector('#hospitalAndOthersMonth').value = new Date().toISOString().slice(0,7)

    summaryTab.addEventListener('click', function() {
        hospitalAndOthersTable.draw()
    })

    searchWithDatesBtn.addEventListener('click', function () {
        datesDiv.querySelector('#hospitalAndOthersMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        hospitalAndOthersTable = getHospitalAndOthersTable('summaryTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
    })

    searchHospitalAndOthersByMonthBtn.addEventListener('click', function () {
        datesDiv.querySelector('#startDate').value = ''; datesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#summaryTable' )){
            $('#summaryTable').dataTable().fnDestroy()
        }
        hospitalAndOthersTable = getHospitalAndOthersTable('summaryTable', null, null, datesDiv.querySelector('#hospitalAndOthersMonth').value)
    })

    document.querySelector('#summaryTable').addEventListener('click', function (event) {
        const showPatientsBtn    = event.target.closest('.showPatientsBtn')
        const from               = datesDiv.querySelector('#startDate').value
        const to                 = datesDiv.querySelector('#endDate').value
        const date               = datesDiv.querySelector('#hospitalAndOthersMonth').value

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
            byResourceTable = getByResourceTable('byResourceTable', id, byResourceModal, datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
            byResourceModal.show()
        }
    })
})