import { Offcanvas, Modal } from "bootstrap";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors, openModals} from "./helpers"
import http from "./http";
//import {DataTable, pdfmake, jszip} from "./datatables"
import jQuery from "jquery";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-select-bs5';
import 'datatables.net-staterestore-bs5';


window.addEventListener('DOMContentLoaded', function () {
    const newSponsorCatgegoryModal          = new Modal(document.getElementById('newSponsorCategoryModal'))
    const updateSponsorCatgegoryModal       = new Modal(document.getElementById('updateSponsorCategoryModal'))

    const addSponsorCategoryBtn             = document.querySelector('#addSponsnorCategoryBtn')

    const createSponsorCategoryBtn          = document.querySelector('#createSponsorCategoryBtn')
    const saveSponsorCategoryBtn            = document.querySelector('#saveSponsorCategoryBtn')

    const sponsorCategoryTable = new DataTable('#sponsorCategoryTable', {
        serverSide: true,
        ajax:  '/sponsorcategory/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "name"},
            {data: "description"},
            {data: "consultationFee"},
            {data: "payClass"},
            {data: row => () =>{
                if (row.approval == 'false'){
                    return 'No'
                } else {
                    return 'Yes'
                }
                }},
            {data: row => row.billMatrix + "%"},
            {data: row => () => { 
                if (row.balanceRequired == 'false'){
                    return 'No'
                } else {
                    return 'Yes'
                }
            }
            },
            {data: "createdAt"},
            {
                sortable: false,
                data: row => `
                <div class="d-flex flex-">
                    <button class=" btn btn-outline-primary updateBtn" data-id="${ row.id }">
                    <i class="bi bi-pencil-fill"></i>
                    <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn" data-id="${ row.id }">
                    <i class="bi bi-trash3-fill"></i>
                </button>
                </div>
            `}
        ]
    });

    document.querySelector('#sponsorCategoryTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const sponsorCategoryId = editBtn.getAttribute('data-id')
            http.get(`/sponsorcategory/${ sponsorCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateSponsorCatgegoryModal, saveSponsorCategoryBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error.response.data.data.message)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Category?')) {
                const sponsorCategoryId = deleteBtn.getAttribute('data-id')
                http.delete(`/sponsorcategory/${sponsorCategoryId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            sponsorCategoryTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
            
        }
    })

    addSponsorCategoryBtn.addEventListener('click', function () {
        newSponsorCatgegoryModal.show()
    })

    createSponsorCategoryBtn.addEventListener('click', function () {
        createSponsorCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/sponsorcategory', getDivData(newSponsorCatgegoryModal._element), {"html": newSponsorCatgegoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newSponsorCatgegoryModal.hide()
                    clearDivValues(newSponsorCatgegoryModal._element)
                    sponsorCategoryTable.draw()
                }
            createSponsorCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
        })

    })
        
        
    saveSponsorCategoryBtn.addEventListener('click', function (event) {
        const sponsorCategoryId = event.currentTarget.getAttribute('data-id')
        saveSponsorCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/sponsorcategory/${sponsorCategoryId}`, getDivData(updateSponsorCatgegoryModal._element), {"html": updateSponsorCatgegoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateSponsorCatgegoryModal.hide()
                sponsorCategoryTable.draw()
            }
            saveSponsorCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
        })
    })

    newSponsorCatgegoryModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorCatgegoryModal._element)
        sponsorCategoryTable.draw()
    })
})
