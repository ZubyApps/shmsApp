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
        ajax: {url: '/sponsor/load', data: {
            'page': 2
        }},
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
                    <button class=" btn btn-outline-primary updateSponsorCategoryBtn" data-id="${ row.id }">
                    <i class="bi bi-pencil-fill"></i>
                </div>
            `}
        ]
    });

    document.querySelector('#sponsorCategoryTable').addEventListener('click', function (event) {
        const editSponsorCategoryBtn       = event.target.closest('.updateSponsorCategoryBtn')

        if (editSponsorCategoryBtn) {
            editSponsorCategoryBtn.setAttribute('disabled', 'disabled')
            const sponsorCategoryId = editSponsorCategoryBtn.getAttribute('data-id')
            http.get(`/sponsor/category/${ sponsorCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                    openModals(updateSponsorCatgegoryModal, saveSponsorCategoryBtn, response.data.data)
                    editSponsorCategoryBtn.removeAttribute('disabled')
                    }
                })
                .catch((error) => {
                    alert(error.response.data.data.message)
                })
        }
    })

    addSponsorCategoryBtn.addEventListener('click', function () {
        newSponsorCatgegoryModal.show()
    })

    createSponsorCategoryBtn.addEventListener('click', function () {
        createSponsorCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/sponsor/category', getDivData(newSponsorCatgegoryModal._element), {"html": newSponsorCatgegoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                createSponsorCategoryBtn.removeAttribute('disabled')
                newSponsorCatgegoryModal.hide()
                clearDivValues(newSponsorCatgegoryModal._element)
                    sponsorCategoryTable.draw()
                }
            })
            .catch((error) => {
                alert(error.response.data.message)
            })
        })
        
        
    saveSponsorCategoryBtn.addEventListener('click', function (event) {
        const sponsorCategoryId = event.currentTarget.getAttribute('data-id')
        saveSponsorCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/sponsor/category/${sponsorCategoryId}`, getDivData(updateSponsorCatgegoryModal._element), {"html": updateSponsorCatgegoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                saveSponsorCategoryBtn.removeAttribute('disabled')
                updateSponsorCatgegoryModal.hide()
                sponsorCategoryTable.draw()
            }
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
