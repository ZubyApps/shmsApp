import {Modal } from "bootstrap";
import $ from 'jquery';
import { getBillOfficersActivityTable, getDoctorsActivityTable, getHmoOfficersActivityTable, getLabTechActivityTable, getNursesActivityTable, getNursesShiftPerformanceTable, getPharmacyTechActivityTable } from "./tables/usersReportTables";
import http from "./http";

window.addEventListener('DOMContentLoaded', function () {
    const doctorsDatesDiv           = document.querySelector('.doctorsDatesDiv')
    const nursesDatesDiv            = document.querySelector('.nursesDatesDiv')
    const labTechDatesDiv           = document.querySelector('.labTechDatesDiv')
    const pharmacyTechsDatesDiv     = document.querySelector('.pharmacyTechsDatesDiv')
    const hmoOfficersDatesDiv       = document.querySelector('.hmoOfficersDatesDiv')
    const billOfficersDatesDiv      = document.querySelector('.billOfficersDatesDiv')

    const doctorsActivityTab        = document.querySelector('#nav-doctorsActivity-tab')
    const nursesActivityTab         = document.querySelector('#nav-nursesActivity-tab')
    const labTechActivityTab        = document.querySelector('#nav-labTechActivity-tab')
    const pharmacyTechsActivityTab  = document.querySelector('#nav-pharmacyTechsActivity-tab')
    const hmoOfficersActivityTab    = document.querySelector('#nav-hmoOfficersActivity-tab')
    const billOfficersActivityTab   = document.querySelector('#nav-billOfficersActivity-tab')
    const nursesShiftPerfomanceTab  = document.querySelector('#nav-nursesShiftPerfomance-tab')

    const searchDoctorsWithDatesBtn = document.querySelector('.searchDoctorsWithDatesBtn')
    const searchDoctorsByMonthBtn   = document.querySelector('.searchDoctorsByMonthBtn')
    
    const searchNursesWithDatesBtn  = document.querySelector('.searchNursesWithDatesBtn')
    const searchNursesByMonthBtn    = document.querySelector('.searchNursesByMonthBtn')

    const searchLabTechWithDatesBtn  = document.querySelector('.searchLabTechWithDatesBtn')
    const searchLabTechByMonthBtn    = document.querySelector('.searchLabTechByMonthBtn')
    
    const searchPharmacyTechsWithDatesBtn  = document.querySelector('.searchPharmacyTechsWithDatesBtn')
    const searchPharmacyTechsByMonthBtn    = document.querySelector('.searchPharmacyTechsByMonthBtn')

    const searchHmoOfficersWithDatesBtn  = document.querySelector('.searchHmoOfficersWithDatesBtn')
    const searchHmoOfficersByMonthBtn    = document.querySelector('.searchHmoOfficersByMonthBtn')

    const searchBillOfficersWithDatesBtn  = document.querySelector('.searchBillOfficersWithDatesBtn')
    const searchBillOfficersByMonthBtn    = document.querySelector('.searchBillOfficersByMonthBtn')

    let doctorsActivityTable, nursesActivityTable, labTechActivityTable, pharmacyTechsActivityTable, hmoOfficersActivityTable, billOfficersActivityTable, nursesShiftPerfomanceTable
    doctorsActivityTable = getDoctorsActivityTable('doctorsActivityTable', 'Doctor')
    doctorsDatesDiv.querySelector('#doctorsActivityMonth').value = new Date().toISOString().slice(0,7)

    doctorsActivityTab.addEventListener('click', function() {
        doctorsActivityTable.draw()
    })

    nursesActivityTab.addEventListener('click', function() {
        nursesDatesDiv.querySelector('#nursesActivityMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#nursesActivityTable' )){
            $('#nursesActivityTable').dataTable().fnDraw()
        } else {
            nursesActivityTable = getNursesActivityTable('nursesActivityTable', 'Nurse')
        }
    })

    labTechActivityTab.addEventListener('click', function() {
        labTechDatesDiv.querySelector('#labTechActivityMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#labTechActivityTable' )){
            $('#labTechActivityTable').dataTable().fnDraw()
        } else {
            labTechActivityTable = getLabTechActivityTable('labTechActivityTable', 'Lab Tech')
        }
    })

    pharmacyTechsActivityTab.addEventListener('click', function() {
        pharmacyTechsDatesDiv.querySelector('#pharmacyTechsActivityMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#pharmacyTechsActivityTable' )){
            $('#pharmacyTechsActivityTable').dataTable().fnDraw()
        } else {
            pharmacyTechsActivityTable = getPharmacyTechActivityTable('pharmacyTechsActivityTable', 'Pharmacy Tech')
        }
    })

    hmoOfficersActivityTab.addEventListener('click', function() {
        hmoOfficersDatesDiv.querySelector('#hmoOfficersActivityMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#hmoOfficersActivityTable' )){
            $('#hmoOfficersActivityTable').dataTable().fnDraw()
        } else {
            hmoOfficersActivityTable = getHmoOfficersActivityTable('hmoOfficersActivityTable', 'HMO Officer')
        }
    })

    billOfficersActivityTab.addEventListener('click', function() {
        billOfficersDatesDiv.querySelector('#billOffersActivityMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#billOfficersActivityTable' )){
            $('#billOfficersActivityTable').dataTable().fnDraw()
        } else {
            billOfficersActivityTable = getBillOfficersActivityTable('billOfficersActivityTable', 'Bill Officer')
        }
    })

    nursesShiftPerfomanceTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#nursesShiftPerfomanceTable' )){
            $('#nursesShiftPerfomanceTable').dataTable().fnDraw()
        } else {
            nursesShiftPerfomanceTable = getNursesShiftPerformanceTable('nursesShiftPerfomanceTable', 'Nurse')
        }
    })

    //doctor
    searchDoctorsWithDatesBtn.addEventListener('click', function () {
        doctorsDatesDiv.querySelector('#doctorsActivityMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#doctorsActivityTable' )){
            $('#doctorsActivityTable').dataTable().fnDestroy()
        }
        doctorsActivityTable = getDoctorsActivityTable('doctorsActivityTable', 'Doctor', doctorsDatesDiv.querySelector('#startDate').value, doctorsDatesDiv.querySelector('#endDate').value)
    })

    searchDoctorsByMonthBtn.addEventListener('click', function () {
        doctorsDatesDiv.querySelector('#startDate').value = ''; doctorsDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#doctorsActivityTable' )){
            $('#doctorsActivityTable').dataTable().fnDestroy()
        }
        doctorsActivityTable = getDoctorsActivityTable('doctorsActivityTable', 'Doctor', null, null, doctorsDatesDiv.querySelector('#doctorsActivityMonth').value)
    })

    //nurse
    searchNursesWithDatesBtn.addEventListener('click', function () {
        nursesDatesDiv.querySelector('#nursesActivityMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#nursesActivityTable' )){
            $('#nursesActivityTable').dataTable().fnDestroy()
        }
        nursesActivityTable = getNursesActivityTable('nursesActivityTable', 'Nurse', nursesDatesDiv.querySelector('#startDate').value, nursesDatesDiv.querySelector('#endDate').value)
    })

    searchNursesByMonthBtn.addEventListener('click', function () {
        nursesDatesDiv.querySelector('#startDate').value = ''; nursesDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#nursesActivityTable' )){
            $('#nursesActivityTable').dataTable().fnDestroy()
        }
        nursesActivityTable = getNursesActivityTable('nursesActivityTable', 'Nurse', null, null, nursesDatesDiv.querySelector('#nursesActivityMonth').value)
    })

    //lab
    searchLabTechWithDatesBtn.addEventListener('click', function () {
        labTechDatesDiv.querySelector('#labTechActivityMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#labTechActivityTable' )){
            $('#labTechActivityTable').dataTable().fnDestroy()
        }
        labTechActivityTable = getLabTechActivityTable('labTechActivityTable', 'Lab Tech', labTechDatesDiv.querySelector('#startDate').value, labTechDatesDiv.querySelector('#endDate').value)
    })

    searchLabTechByMonthBtn.addEventListener('click', function () {
        labTechDatesDiv.querySelector('#startDate').value = ''; labTechDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#labTechActivityTable' )){
            $('#labTechActivityTable').dataTable().fnDestroy()
        }
        labTechActivityTable = getLabTechActivityTable('labTechActivityTable', 'Lab Tech', null, null, labTechDatesDiv.querySelector('#labTechActivityMonth').value)
    })

    //pharmacy
    searchPharmacyTechsWithDatesBtn.addEventListener('click', function () {
        pharmacyTechsDatesDiv.querySelector('#pharmacyTechsActivityMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#pharmacyTechsActivityTable' )){
            $('#pharmacyTechsActivityTable').dataTable().fnDestroy()
        }
        pharmacyTechsActivityTable = getPharmacyTechActivityTable('pharmacyTechsActivityTable', 'Pharmacy Tech', pharmacyTechsDatesDiv.querySelector('#startDate').value, pharmacyTechsDatesDiv.querySelector('#endDate').value)
    })

    searchPharmacyTechsByMonthBtn.addEventListener('click', function () {
        pharmacyTechsDatesDiv.querySelector('#startDate').value = ''; pharmacyTechsDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#pharmacyTechsActivityTable' )){
            $('#pharmacyTechsActivityTable').dataTable().fnDestroy()
        }
        pharmacyTechsActivityTable = getPharmacyTechActivityTable('pharmacyTechsActivityTable', 'Pharmacy Tech', null, null, pharmacyTechsDatesDiv.querySelector('#pharmacyTechsActivityMonth').value)
    })
    
    //Hmo
    searchHmoOfficersWithDatesBtn.addEventListener('click', function () {
        hmoOfficersDatesDiv.querySelector('#hmoOfficersActivityMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#hmoOfficersActivityTable' )){
            $('#hmoOfficersActivityTable').dataTable().fnDestroy()
        }
        hmoOfficersActivityTable = getHmoOfficersActivityTable('hmoOfficersActivityTable', 'HMO Officer', hmoOfficersDatesDiv.querySelector('#startDate').value, hmoOfficersDatesDiv.querySelector('#endDate').value)
    })

    searchHmoOfficersByMonthBtn.addEventListener('click', function () {
        hmoOfficersDatesDiv.querySelector('#startDate').value = ''; hmoOfficersDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#hmoOfficersActivityTable' )){
            $('#hmoOfficersActivityTable').dataTable().fnDestroy()
        }
        hmoOfficersActivityTable = getHmoOfficersActivityTable('hmoOfficersActivityTable', 'HMO Officer', null, null, hmoOfficersDatesDiv.querySelector('#hmoOfficersActivityMonth').value)
    })

    //bill
    searchBillOfficersWithDatesBtn.addEventListener('click', function () {
        billOfficersDatesDiv.querySelector('#billOffersActivityMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#billOfficersActivityTable' )){
            $('#billOfficersActivityTable').dataTable().fnDestroy()
        }
        billOfficersActivityTable = getBillOfficersActivityTable('billOfficersActivityTable', 'Bill Officer', billOfficersDatesDiv.querySelector('#startDate').value, billOfficersDatesDiv.querySelector('#endDate').value)
    })

    searchBillOfficersByMonthBtn.addEventListener('click', function () {
        billOfficersDatesDiv.querySelector('#startDate').value = ''; billOfficersDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#billOfficersActivityTable' )){
            $('#billOfficersActivityTable').dataTable().fnDestroy()
        }
        billOfficersActivityTable = getBillOfficersActivityTable('billOfficersActivityTable', 'Bill Officer', null, null, billOfficersDatesDiv.querySelector('#billOffersActivityMonth').value)
    })

    document.querySelectorAll('#nursesShiftPerfomanceTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const staffBtn = event.target.closest('.staffBtn')

            if (staffBtn) {
                const performanceId = staffBtn.getAttribute('data-id')
                staffBtn.classList.add('d-none')

                const staffInput = staffBtn.parentElement.querySelector('.staffInput')
                staffInput.classList.remove('d-none')
                staffInput.focus()

                staffInput.addEventListener('blur', function() {
                    http.patch(`/shiftperformance/staff/${performanceId}`, {staff: staffInput.value})
                    .then((response) => {
                        if (response.status == 200) {
                            nursesShiftPerfomanceTable.draw()
                        }
                    })
                        .catch((error) => {
                            console.log(error.response)                            
                        })
                    })
            }
        })
    })
})
