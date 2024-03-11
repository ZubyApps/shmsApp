// import {Modal } from "bootstrap";
// import http from "./http";
// import $ from 'jquery';
// import { getBySponsorTable, getDistribution1Table, getDistribution2Table, getFrequencyTable, getRegBySponsorTable } from "./tables/reportTables";

// window.addEventListener('DOMContentLoaded', function () {
//     const bySponsorModal    = new Modal(document.getElementById('bySponsorModal'))

//     const datesDiv                          = document.querySelector('.datesDiv')

//     const distribution1Tab          = document.querySelector('#nav-distribution1-tab')
//     const distribution2Tab          = document.querySelector('#nav-distribution2-tab')
//     const frequencyTab              = document.querySelector('#nav-frequency-tab')
//     const registrationsTab          = document.querySelector('#nav-registrations-tab')
//     const searchRegWithDatesBtn     = document.querySelector('.searchRegWithDatesBtn')

//     const distribution1Table = getDistribution1Table('distribution1Table')
//     let bySponsorTable, frequencyTable, distribution2Table, registrationTable

//     distribution1Tab.addEventListener('click', function() {
//         distribution1Table.draw()
//     })

//     distribution2Tab.addEventListener('click', function() {
//         if ($.fn.DataTable.isDataTable( '#distribution2Table' )){
//             $('#distribution2Table').dataTable().fnDraw()
//         } else {
//             distribution2Table = getDistribution2Table('distribution2Table')
//         }
//         distribution2Table.draw()
//     })

//     frequencyTab.addEventListener('click', function() {
//         if ($.fn.DataTable.isDataTable( '#frequencyTable' )){
//             $('#frequencyTable').dataTable().fnDraw()
//         } else {
//             frequencyTable = getFrequencyTable('frequencyTable')
//         }
//     })
    
//     registrationsTab.addEventListener('click', function() {
//         if ($.fn.DataTable.isDataTable( '#registerationsTable' )){
//             $('#registerationsTable').dataTable().fnDraw()
//         } else {
//             registrationTable = getRegBySponsorTable('registerationsTable')
//         }
//     })

//     searchRegWithDatesBtn.addEventListener('click', function () {
//         // if (!datesDiv.querySelector('#startDate').value && !datesDiv.querySelector('#endDate').value){
//         //     return alert('Please pick valid dates')
//         // }
//         if ($.fn.DataTable.isDataTable( '#registerationsTable' )){
//             $('#registerationsTable').dataTable().fnDestroy()
//         }
//         registrationTable = getRegBySponsorTable('registerationsTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
//     })

//     document.querySelectorAll('#distribution2Table, #registerationsTable').forEach(table => {
//         table.addEventListener('click', function (event) {
//             const showPatientsBtn   = event.target.closest('.showPatientsBtn')
    
//             if (showPatientsBtn){
//                 const id = showPatientsBtn.getAttribute('data-id')
//                 bySponsorModal._element.querySelector('#sponsor').value = showPatientsBtn.getAttribute('data-sponsor') 
//                 bySponsorModal._element.querySelector('#category').value = showPatientsBtn.getAttribute('data-category')
//                 bySponsorTable = getBySponsorTable('bySponsorTable', id, bySponsorModal, datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value)
//                 bySponsorModal.show()
//             }
//         })
//     })
// })