import { Modal } from "bootstrap";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors, openModals, displayList, getDatalistOptionId} from "./helpers"
import http from "./http";
import $ from 'jquery';
import jQuery from "jquery";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { getAddResourceStockTable, getResourceSubCategoryTable, getResourceSupplierTable, getResourceTable } from "./tables/resourcesTables";


window.addEventListener('DOMContentLoaded', function () {
    const newResourceSubCategoryModal           = new Modal(document.getElementById('newResourceSubCategoryModal'))
    const updateResourceSubCategoryModal        = new Modal(document.getElementById('updateResourceSubCategoryModal'))
    
    const newResourceModal                      = new Modal(document.getElementById('newResourceModal'))
    const updateResourceModal                   = new Modal(document.getElementById('updateResourceModal'))
    
    const newResourceSupplierModal              = new Modal(document.getElementById('newResourceSupplierModal'))
    const updateResourceSupplierModal           = new Modal(document.getElementById('updateResourceSupplierModal'))

    const newAddResourceStockModal              = new Modal(document.getElementById('newAddResourceStockModal'))

    const addResourceSubCategoryBtn             = document.querySelector('#addResourceSubCategoryBtn')
    const addResourceBtn                        = document.querySelector('#addResourceBtn')
    const addResourceSupplierBtn                = document.querySelector('#addResourceSupplierBtn')

    const createResourceSubCategoryBtn          = document.querySelector('#createResourceSubCategoryBtn')
    const saveResourceSubCategoryBtn            = document.querySelector('#saveResourceSubCategoryBtn')
    
    const createAddResourceStockBtn             = document.querySelector('#createAddResourceStockBtn')

    const createResourceBtn                     = document.querySelector('#createResourceBtn')
    const saveResourceBtn                       = document.querySelector('#saveResourceBtn')

    const createResourceSupplierBtn             = document.querySelector('#createResourceSupplierBtn')
    const saveResourceSupplierBtn               = document.querySelector('#saveResourceSupplierBtn')

    const newResourceCategoryInput              = document.querySelector('#newResourceCategory')
    const updateResourceCategoryInput           = document.querySelector('#updateResourceCategory')

    const newSupplierInput                      = document.querySelector('#newSupplierInput')
    const updateSupplierInput                   = document.querySelector('#updateSupplierInput')
    
    const newResourceSubCategoryDatalistEl      = document.querySelector('#newSubCategoryList')
    const updateResourceSubCategoryDatalistEl   = document.querySelector('#updateSubCategoryList')
    
    const newSupplierDatalistEl                 = document.querySelector('#newSupplierList')
    const updateSupplierDatalistEl              = document.querySelector('#updateSuppllierList')

    const newResourceSubCategoryInputEl         = document.querySelector('#newResourceSubCategory')
    const updateResourceSubCategoryInputEl      = document.querySelector('#updateResourceSubCategory')

    const resourcesTab                          = document.querySelector('#nav-resources-tab')
    const resourceSubCategoryTab                = document.querySelector('#nav-resourceSubCategory-tab')
    const addResourceStockTab                   = document.querySelector('#nav-addResourceStock-tab')
    const resourceSupplierTab                   = document.querySelector('#nav-resourceSupplier-tab')

    let resourceSubCategoryTable, addResourceStockTable, resourceSupplierTable
    
    const resourceTable = getResourceTable('resourceTable')

    resourcesTab.addEventListener('click', function () {resourceTable.draw()})

    resourceSubCategoryTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#resourceSubCategoryTable' )){
            $('#resourceSubCategoryTable').dataTable().fnDraw()
        } else {
            resourceSubCategoryTable = getResourceSubCategoryTable('resourceSubCategoryTable')
        }
    })
    addResourceStockTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#addResourceStockTable' )){
            $('#addResourceStockTable').dataTable().fnDraw()
        } else {
            addResourceStockTable = getAddResourceStockTable('#addResourceStockTable')
        }
    })
    resourceSupplierTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#resourceSupplierTable' )){
            $('#resourceSupplierTable').dataTable().fnDraw()
        } else {
            resourceSupplierTable = getResourceSupplierTable('resourceSupplierTable')
        }
    })

    document.querySelector('#resourceSubCategoryTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const resourceSubCategoryId = editBtn.getAttribute('data-id')
            http.get(`/resourcesubcategory/${ resourceSubCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateResourceSubCategoryModal, saveResourceSubCategoryBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Resource SubCategory?')) {
                const resourceSubCategory = deleteBtn.getAttribute('data-id')
                http.delete(`/resourcesubcategory/${resourceSubCategory}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            resourceSubCategoryTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    addResourceSubCategoryBtn.addEventListener('click', function () {
        newResourceSubCategoryModal.show()
    })

    createResourceSubCategoryBtn.addEventListener('click', function () {
        createResourceSubCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/resourcesubcategory', getDivData(newResourceSubCategoryModal._element), {"html": newResourceSubCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newResourceSubCategoryModal.hide()
                    clearDivValues(newResourceSubCategoryModal._element)
                    resourceSubCategoryTable.draw()
                }
            createResourceSubCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createResourceSubCategoryBtn.removeAttribute('disabled')
        })

    })

    saveResourceSubCategoryBtn.addEventListener('click', function (event) {
        const resourceSubCategoryId = event.currentTarget.getAttribute('data-id')
        saveResourceSubCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/resourcesubcategory/${resourceSubCategoryId}`, getDivData(updateResourceSubCategoryModal._element), {"html": updateResourceSubCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateResourceSubCategoryModal.hide()
                resourceSubCategoryTable.draw()
            }
            saveResourceSubCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error)
            saveResourceSubCategoryBtn.removeAttribute('disabled')
        })
    })


    //selecting subcategories based on category
    updateResourceCategoryInput.addEventListener('change', function() {
        if (updateResourceCategoryInput.value) {
            http.get(`/resourcecategory/list_subcategories/${updateResourceCategoryInput.value}`).then((response) => {
                    displayList(updateResourceSubCategoryDatalistEl, 'subCategoryOption' ,response.data)

            })
        }
    })

    newResourceCategoryInput.addEventListener('change', function() {
        if (newResourceCategoryInput.value) {
            http.get(`/resourcecategory/list_subcategories/${newResourceCategoryInput.value}`).then((response) => {
                    displayList(newResourceSubCategoryDatalistEl, 'subCategoryOption' ,response.data)

            })
        }
    })

    document.querySelector('#resourceTable').addEventListener('click', function (event) {
        const editBtn                   = event.target.closest('.updateBtn')
        const toggleActiveStatusBtn     = event.target.closest('.toggleActiveStatusBtn')
        const deleteBtn                 = event.target.closest('.deleteBtn')
        const addStockBtn               = event.target.closest('.addStockBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const resourceId = editBtn.getAttribute('data-id')
            let date = new Date().toISOString().split('T')[0]
            updateResourceModal._element.querySelector('[name="expiryDate"]').setAttribute('min', date)
            http.get(`/resources/${ resourceId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openResourceModal(updateResourceModal, saveResourceBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (addStockBtn) {
            addStockBtn.setAttribute('disabled', 'disabled')
            const resourceId = addStockBtn.getAttribute('data-id')
            http.get(`/resources/addstock/${ resourceId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openResourceModal(newAddResourceStockModal, createAddResourceStockBtn, response.data.data)
                    }
                    addStockBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (toggleActiveStatusBtn) {
            toggleActiveStatusBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to change the status of this Resource?')) {
            const resourceId = toggleActiveStatusBtn.getAttribute('data-id')
            http.post(`/resources/toggle/${ resourceId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        resourceTable.draw()
                    }
                    toggleActiveStatusBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
            }
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Resource?')) {
                const resource = deleteBtn.getAttribute('data-id')
                http.delete(`/resources/${resource}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            resourceTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    addResourceBtn.addEventListener('click', function () {
        let date = new Date().toISOString().split('T')[0]
        newResourceModal._element.querySelector('[name="expiryDate"]').setAttribute('min', date)
        newResourceModal.show()
    })

    createResourceBtn.addEventListener('click', function () {
        const resourceSubCategory = getDatalistOptionId(newResourceModal, newResourceSubCategoryInputEl, newResourceSubCategoryDatalistEl)

        createResourceBtn.setAttribute('disabled', 'disabled')
        let data = {...getDivData(newResourceModal._element), resourceSubCategory }

        http.post('/resources', {...data}, {"html": newResourceModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newResourceModal.hide()
                    clearDivValues(newResourceModal._element)
                    resourceTable.draw()
                }
            createResourceBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createResourceBtn.removeAttribute('disabled')
        })

    })

    saveResourceBtn.addEventListener('click', function (event) {
        const resourceId = event.currentTarget.getAttribute('data-id')
        saveResourceBtn.setAttribute('disabled', 'disabled')
        const resourceSubCategory = getDatalistOptionId(updateResourceModal, updateResourceSubCategoryInputEl, updateResourceSubCategoryDatalistEl)
        let data = {...getDivData(updateResourceModal._element), resourceSubCategory }

        http.post(`/resources/${resourceId}`, {...data}, {"html": updateResourceModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateResourceModal.hide()
                resourceTable.draw()
            }
            saveResourceBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error)
            saveResourceBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#addResourceStockTable').addEventListener('click', function (event) {
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this stock record?')) {
                const addResourceStockId = deleteBtn.getAttribute('data-id')
                http.delete(`/addresourcestock/${addResourceStockId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            addResourceStockTable.draw()
                            resourceTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    createAddResourceStockBtn.addEventListener('click', function (event) {
        const resourceId = event.currentTarget.getAttribute('data-id')
        const resourceSupplierId = getDatalistOptionId(newAddResourceStockModal, newSupplierInput, newSupplierDatalistEl)
        
        createAddResourceStockBtn.setAttribute('disabled', 'disabled')
        let data = {...getDivData(newAddResourceStockModal._element), resourceId, resourceSupplierId}

        http.post('/addresourcestock', {...data}, {"html": newAddResourceStockModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newAddResourceStockModal.hide()
                    clearDivValues(newAddResourceStockModal._element)
                    addResourceStockTable ? addResourceStockTable.draw() : ''
                    resourceTable.draw()
                }
            createAddResourceStockBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createAddResourceStockBtn.removeAttribute('disabled')
        })

    })

    newSupplierInput.addEventListener('keyup', function() {
        if (newSupplierInput.value) {
            http.get(`/resourcesupplier/list`, {params: {supplier: newSupplierInput.value}}).then((response) => {
                displayList(newSupplierDatalistEl, 'supplierOption', response.data)
            })
        }
    })
    
    updateSupplierInput.addEventListener('keyup', function() {
        if (updateSupplierInput.value) {
            http.get(`/resourcesupplier/list/${updateSupplierInput.value}`).then((response) => {
                    displayList(updateSupplierDatalistEl, 'supplierOption', response.data)
            })
        }
    })

    addResourceSupplierBtn.addEventListener('click', function () {
        newResourceSupplierModal.show()
    })

    document.querySelector('#resourceSupplierTable').addEventListener('click', function (event) {
        const editBtn  = event.target.closest('.editBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const resourceSupplierId = editBtn.getAttribute('data-id')
            http.get(`/resourcesupplier/${ resourceSupplierId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openResourceModal(updateResourceSupplierModal, saveResourceSupplierBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Supplier?')) {
                const addResourceStockId = deleteBtn.getAttribute('data-id')
                http.delete(`/resourcesupplier/${addResourceStockId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            resourceSupplierTable.draw()
                            resourceTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    createResourceSupplierBtn.addEventListener('click', function (event) {
        createResourceSupplierBtn.setAttribute('disabled', 'disabled')

        http.post('/resourcesupplier', {...getDivData(newResourceSupplierModal._element)}, {"html": newResourceSupplierModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newResourceSupplierModal.hide()
                    clearDivValues(newResourceSupplierModal._element)
                }
            resourceSupplierTable.draw()
            createResourceSupplierBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createResourceSupplierBtn.removeAttribute('disabled')
        })

    })

    saveResourceSupplierBtn.addEventListener('click', function (event) {
        const resourceSupplierId = event.currentTarget.getAttribute('data-id')
        saveResourceSupplierBtn.setAttribute('disabled', 'disabled')

        http.post(`/resourcesupplier/${resourceSupplierId}`, {...getDivData(updateResourceSupplierModal._element)}, {"html": updateResourceSupplierModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateResourceSupplierModal.hide()
                resourceSupplierTable.draw()
            }
            saveResourceSupplierBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error)
            saveResourceSupplierBtn.removeAttribute('disabled')
        })
    })

    newResourceSubCategoryModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newResourceSubCategoryModal._element)
        resourceSubCategoryTable.draw()
    })

    newResourceModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newResourceModal._element)
        resourceTable.draw()
    })

    newAddResourceStockModal._element.addEventListener('hidden.bs.modal', function () {
        clearDivValues(newAddResourceStockModal._element)
        clearValidationErrors(newAddResourceStockModal._element)
        addResourceStockTable.draw()
    })

    newResourceSupplierModal._element.addEventListener('hidden.bs.modal', function () {
        clearDivValues(newResourceSupplierModal._element)
        clearValidationErrors(newResourceSupplierModal._element)
        resourceSupplierTable.draw()
    })

})

function openResourceModal(modal, button, {id, resourceSubCategoryId, resourceCategoryId, ...data}) {
 
    for (let name in data) {
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)

        nameInput.value = data[name]
    }

    if (modal._element.id === 'updateResourceModal'){    
        modal._element.querySelector('#updateResourceSubCategory').setAttribute('data-id', resourceSubCategoryId)
        const dataListEl = modal._element.querySelector('#updateSubCategoryList')

        http.get(`/resourcecategory/list_subcategories/${resourceCategoryId}`).then((response) => {
            displayList(dataListEl, 'subCategoryOption', response.data)
        })
    }

    button.setAttribute('data-id', id)
    modal.show()
}