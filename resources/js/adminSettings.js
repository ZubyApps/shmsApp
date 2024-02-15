import { Modal } from "bootstrap";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors, openModals} from "./helpers"
import http from "./http";
import jQuery from "jquery";
import $ from 'jquery';
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
import { getPayMethodTable, getResourceCategoryTable, getResourceStockDateTable, getSponsorCategoryTable } from "./tables/settingsTables";


window.addEventListener('DOMContentLoaded', function () {
    const newSponsorCategoryModal          = new Modal(document.getElementById('newSponsorCategoryModal'))
    const updateSponsorCategoryModal       = new Modal(document.getElementById('updateSponsorCategoryModal'))
    
    const newResourceStockDateModal          = new Modal(document.getElementById('newResourceStockDateModal'))
    const updateResourceStockDateModal       = new Modal(document.getElementById('updateResourceStockDateModal'))

    const newResourceCategoryModal          = new Modal(document.getElementById('newResourceCategoryModal'))
    const updateResourceCategoryModal       = new Modal(document.getElementById('updateResourceCategoryModal'))
    
    const newPayMethodModal                 = new Modal(document.getElementById('newPayMethodModal'))
    const editPayMethodModal                = new Modal(document.getElementById('editPayMethodModal'))

    const addSponsorCategoryBtn             = document.querySelector('#addSponsnorCategoryBtn')
    const addResourceStockDateBtn           = document.querySelector('#addResourceStockDateBtn')
    const addResourceCategoryBtn            = document.querySelector('#addResourceCategoryBtn')
    const addPayMethodBtn                   = document.querySelector('#addPayMethodBtn')

    const createSponsorCategoryBtn          = document.querySelector('#createSponsorCategoryBtn')
    const saveSponsorCategoryBtn            = document.querySelector('#saveSponsorCategoryBtn')

    const createResourceStockDateBtn        = document.querySelector('#createResourceStockDateBtn')
    const saveResourceStockDateBtn          = document.querySelector('#saveResourceStockDateBtn')
    
    const createResourceCategoryBtn         = document.querySelector('#createResourceCategoryBtn')
    const saveResourceCategoryBtn           = document.querySelector('#saveResourceCategoryBtn')
    
    const createPayMethodBtn                = document.querySelector('#createPayMethodBtn')
    const savePayMethodBtn                  = document.querySelector('#savePayMethodBtn')
    
    const sponsorCategoryTab                = document.querySelector('#nav-sponsorCategory-tab')
    const resourceStockDateTab              = document.querySelector('#nav-resourceStockDate-tab')
    const resourceCategoryTab               = document.querySelector('#nav-resourceCategory-tab')
    const payMethodTab                      = document.querySelector('#nav-payMethod-tab') 

    let resourceStockDateTable, resourceCategoryTable, payMethodTable

    const sponsorCategoryTable = getSponsorCategoryTable('sponsorCategoryTable')

    sponsorCategoryTab.addEventListener('click', function() {
        sponsorCategoryTable.draw()
    })

    resourceStockDateTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#resourceStockDateTable' )){
            $('#resourceStockDateTable').dataTable().fnDraw()
        } else {
            resourceStockDateTable = getResourceStockDateTable('resourceStockDateTable')
        }
    })

    resourceCategoryTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#resourceCategoryTable' )){
            $('#resourceCategoryTable').dataTable().fnDraw()
        } else {
            resourceCategoryTable = getResourceCategoryTable('resourceCategoryTable')
        }
    })

    payMethodTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#payMethodTable' )){
            $('#payMethodTable').dataTable().fnDraw()
        } else {
            payMethodTable = getPayMethodTable('payMethodTable')
        }
    })

    document.querySelector('#sponsorCategoryTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const sponsorCategoryId = editBtn.getAttribute('data-id')
            http.get(`/sponsorcategory/${ sponsorCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateSponsorCategoryModal, saveSponsorCategoryBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
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
        newSponsorCategoryModal.show()
    })

    createSponsorCategoryBtn.addEventListener('click', function () {
        createSponsorCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/sponsorcategory', getDivData(newSponsorCategoryModal._element), {"html": newSponsorCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newSponsorCategoryModal.hide()
                    clearDivValues(newSponsorCategoryModal._element)
                    sponsorCategoryTable.draw()
                }
            createSponsorCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createSponsorCategoryBtn.removeAttribute('disabled')
        })

    })

    saveSponsorCategoryBtn.addEventListener('click', function (event) {
        const sponsorCategoryId = event.currentTarget.getAttribute('data-id')
        saveSponsorCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/sponsorcategory/${sponsorCategoryId}`, getDivData(updateSponsorCategoryModal._element), {"html": updateSponsorCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateSponsorCategoryModal.hide()
                sponsorCategoryTable.draw()
            }
            saveSponsorCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            saveSponsorCategoryBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#resourceStockDateTable').addEventListener('click', function (event) {
        const resetBtn    = event.target.closest('.resetResourceStockBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (resetBtn) {
            resetBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to reset all stock values?')) {

                if (confirm('Are you very sure?. This cannot be reversed')) {

                    if (confirm('Last chance to make sure!')) {

                        const resourceStockDateId = resetBtn.getAttribute('data-id')
                        
                        http.post(`/resourcestockdate/resetstock/${resourceStockDateId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                resourceStockDateTable.draw()
                                alert('You have reset all stock')
                            }
                            resetBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error.response.data.message)
                            resetBtn.removeAttribute('disabled')
                        })
                        
                    }
                }
            }
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Date?')) {
                const resourceStockDateId = deleteBtn.getAttribute('data-id')
                http.delete(`/resourcestockdate/${resourceStockDateId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            resourceStockDateTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
            
        }

    })

    addResourceStockDateBtn.addEventListener('click', function () {
        newResourceStockDateModal.show()
    })

    createResourceStockDateBtn.addEventListener('click', function () {
        createResourceStockDateBtn.setAttribute('disabled', 'disabled')
        http.post('/resourcestockdate', getDivData(newResourceStockDateModal._element), {"html": newResourceStockDateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newResourceStockDateModal.hide()
                    clearDivValues(newResourceStockDateModal._element)
                    resourceStockDateTable.draw()
                }
            createResourceStockDateBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createResourceStockDateBtn.removeAttribute('disabled')
        })

    })

    saveResourceStockDateBtn.addEventListener('click', function (event) {
        const resourceStockDateId = event.currentTarget.getAttribute('data-id')
        saveResourceStockDateBtn.setAttribute('disabled', 'disabled')
        http.post(`/resourcestockdate/${resourceStockDateId}`, getDivData(updateResourceStockDateModal._element), {"html": updateResourceStockDateModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateResourceStockDateModal.hide()
                resourceStockDateTable.draw()
            }
            saveResourceStockDateBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            saveResourceStockDateBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#resourceCategoryTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const resourceCategoryId = editBtn.getAttribute('data-id')
            http.get(`/resourcecategory/${ resourceCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateResourceCategoryModal, saveResourceCategoryBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Resource Category?')) {
                const resourceCategory = deleteBtn.getAttribute('data-id')
                http.delete(`/resourcecategory/${resourceCategory}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            resourceCategoryTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    addResourceCategoryBtn.addEventListener('click', function () {
        newResourceCategoryModal.show()
    })

    createResourceCategoryBtn.addEventListener('click', function () {
        createResourceCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/resourcecategory', getDivData(newResourceCategoryModal._element), {"html": newResourceCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newResourceCategoryModal.hide()
                    clearDivValues(newResourceCategoryModal._element)
                    resourceCategoryTable.draw()
                }
            createResourceCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createResourceCategoryBtn.removeAttribute('disabled')
        })

    })

    saveResourceCategoryBtn.addEventListener('click', function (event) {
        const resourceCategoryId = event.currentTarget.getAttribute('data-id')
        saveResourceCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/resourcecategory/${resourceCategoryId}`, getDivData(updateResourceCategoryModal._element), {"html": updateResourceCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateResourceCategoryModal.hide()
                resourceCategoryTable.draw()
            }
            saveResourceCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            saveResourceCategoryBtn.removeAttribute('disabled')
        })
    })

    addPayMethodBtn.addEventListener('click', function () {
        newPayMethodModal.show()
    })

    document.querySelector('#payMethodTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const payMethodId = editBtn.getAttribute('data-id')
            http.get(`/paymethod/${ payMethodId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editPayMethodModal, savePayMethodBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Pay Method?')) {
                const payMethodId = deleteBtn.getAttribute('data-id')
                http.delete(`/paymethod/${payMethodId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            payMethodTable ? payMethodTable.draw() : ''
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    createPayMethodBtn.addEventListener('click', function () {
        createPayMethodBtn.setAttribute('disabled', 'disabled')
        http.post('/paymethod', getDivData(newPayMethodModal._element), {"html": newPayMethodModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newPayMethodModal.hide()
                    clearDivValues(newPayMethodModal._element)
                    payMethodTable ? payMethodTable.draw() : ''
                }
                createPayMethodBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createPayMethodBtn.removeAttribute('disabled')
        })
    })

    savePayMethodBtn.addEventListener('click', function (event) {
        const payMethodId = event.currentTarget.getAttribute('data-id')
        savePayMethodBtn.setAttribute('disabled', 'disabled')
        http.patch(`/paymethod/${payMethodId}`, getDivData(editPayMethodModal._element), {"html": editPayMethodModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                editPayMethodModal.hide()
                payMethodTable ? payMethodTable.draw() : ''
            }
            savePayMethodBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            savePayMethodBtn.removeAttribute('disabled')
        })
    })

    newSponsorCategoryModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorCategoryModal._element)
        sponsorCategoryTable.draw()
    })

    newResourceStockDateModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newResourceStockDateModal._element)
        resourceStockDateTable ? resourceStockDateTable.draw() : ''
    })

    newResourceCategoryModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newResourceCategoryModal._element)
        resourceCategoryTable ? resourceCategoryTable.draw() : ''
    })
})
