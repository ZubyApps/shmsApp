import { Modal } from "bootstrap";
import { clearDivValues, getDivData, clearValidationErrors, openModals} from "./helpers"
import http from "./http";
import $ from 'jquery';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-select-bs5';
import 'datatables.net-staterestore-bs5';
import { getExpenseCategoryTable, getMedicationCategoryTable, getPayMethodTable, getResourceCategoryTable, getResourceStockDateTable, getSponsorCategoryTable } from "./tables/settingsTables";


window.addEventListener('DOMContentLoaded', function () {
    const newSponsorCategoryModal          = new Modal(document.getElementById('newSponsorCategoryModal'))
    const updateSponsorCategoryModal       = new Modal(document.getElementById('updateSponsorCategoryModal'))
    
    const newResourceStockDateModal          = new Modal(document.getElementById('newResourceStockDateModal'))
    const updateResourceStockDateModal       = new Modal(document.getElementById('updateResourceStockDateModal'))

    const newResourceCategoryModal          = new Modal(document.getElementById('newResourceCategoryModal'))
    const updateResourceCategoryModal       = new Modal(document.getElementById('updateResourceCategoryModal'))
    
    const newExpenseCategoryModal          = new Modal(document.getElementById('newExpenseCategoryModal'))
    const updateExpenseCategoryModal       = new Modal(document.getElementById('updateExpenseCategoryModal'))
    
    const newMedicationCategoryModal       = new Modal(document.getElementById('newMedicationCategoryModal'))
    const updateMedicationCategoryModal    = new Modal(document.getElementById('updateMedicationCategoryModal'))

    const newPayMethodModal                 = new Modal(document.getElementById('newPayMethodModal'))
    const editPayMethodModal                = new Modal(document.getElementById('editPayMethodModal'))

    // const addSponsorCategoryBtn             = document.querySelector('#addSponsnorCategoryBtn')
    const addResourceStockDateBtn           = document.querySelector('#addResourceStockDateBtn')
    // const addResourceCategoryBtn            = document.querySelector('#addResourceCategoryBtn')
    const addPayMethodBtn                   = document.querySelector('#addPayMethodBtn')
    const addExpenseCategoryBtn             = document.querySelector('#addExpenseCategoryBtn')
    const addMedicationCategoryBtn           = document.querySelector('#addMedicationCategoryBtn')

    const createSponsorCategoryBtn          = document.querySelector('#createSponsorCategoryBtn')
    const saveSponsorCategoryBtn            = document.querySelector('#saveSponsorCategoryBtn')

    const createResourceStockDateBtn        = document.querySelector('#createResourceStockDateBtn')
    const saveResourceStockDateBtn          = document.querySelector('#saveResourceStockDateBtn')
    
    const createResourceCategoryBtn         = document.querySelector('#createResourceCategoryBtn')
    const saveResourceCategoryBtn           = document.querySelector('#saveResourceCategoryBtn')
    
    const createPayMethodBtn                = document.querySelector('#createPayMethodBtn')
    const savePayMethodBtn                  = document.querySelector('#savePayMethodBtn')
    
    const createExpenseCategoryBtn          = document.querySelector('#createExpenseCategoryBtn')
    const saveExpenseCategoryBtn            = document.querySelector('#saveExpenseCategoryBtn')

    const createMedicationCategoryBtn       = document.querySelector('#createMedicationCategoryBtn')
    const saveMedicationCategoryBtn         = document.querySelector('#saveMedicationCategoryBtn')
    
    const sponsorCategoryTab                = document.querySelector('#nav-sponsorCategory-tab')
    const resourceStockDateTab              = document.querySelector('#nav-resourceStockDate-tab')
    const resourceCategoryTab               = document.querySelector('#nav-resourceCategory-tab')
    const payMethodTab                      = document.querySelector('#nav-payMethod-tab') 
    const expenseCategoryTab                = document.querySelector('#nav-expenseCategory-tab')
    const medicationCategoryTab             = document.querySelector('#nav-medicationCategory-tab')

    let resourceStockDateTable, resourceCategoryTable, payMethodTable, expenseCategoryTable, medicationCategoryTable

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

    expenseCategoryTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#expenseCategoryTable' )){
            $('#expenseCategoryTable').dataTable().fnDraw()
        } else {
            expenseCategoryTable = getExpenseCategoryTable('expenseCategoryTable')
        }
    })

    medicationCategoryTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#medicationCategoryTable' )){
            $('#medicationCategoryTable').dataTable().fnDraw()
        } else {
            medicationCategoryTable = getMedicationCategoryTable('medicationCategoryTable')
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

    // addSponsorCategoryBtn.addEventListener('click', function () {
    //     newSponsorCategoryModal.show()
    // })

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

    // addResourceCategoryBtn.addEventListener('click', function () {
    //     newResourceCategoryModal.show()
    // })

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

    addExpenseCategoryBtn.addEventListener('click', function () {
        newExpenseCategoryModal.show()
    })

    document.querySelector('#expenseCategoryTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const expenseCategoryId = editBtn.getAttribute('data-id')
            http.get(`/expensecategory/${ expenseCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateExpenseCategoryModal, saveExpenseCategoryBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Expense Category?')) {
                const expenseCategory = deleteBtn.getAttribute('data-id')
                http.delete(`/expensecategory/${expenseCategory}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            expenseCategoryTable ? expenseCategoryTable.draw() : ''
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                    })
            }
        }
    })

    createExpenseCategoryBtn.addEventListener('click', function () {
        createExpenseCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/expensecategory', getDivData(newExpenseCategoryModal._element), {"html": newExpenseCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newExpenseCategoryModal.hide()
                    clearDivValues(newExpenseCategoryModal._element)
                    expenseCategoryTable ? expenseCategoryTable.draw() : ''
                }
                createExpenseCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            console.log(error.response.data.message)
            createExpenseCategoryBtn.removeAttribute('disabled')
        })

    })

    saveExpenseCategoryBtn.addEventListener('click', function (event) {
        const expenseCategoryId = event.currentTarget.getAttribute('data-id')
        saveExpenseCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/expensecategory/${expenseCategoryId}`, getDivData(updateExpenseCategoryModal._element), {"html": updateExpenseCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateExpenseCategoryModal.hide()
                expenseCategoryTable ? expenseCategoryTable.draw() : ''
            }
            saveExpenseCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            saveExpenseCategoryBtn.removeAttribute('disabled')
        })
    })

    addMedicationCategoryBtn.addEventListener('click', function () {
        newMedicationCategoryModal.show()
    })

    document.querySelector('#medicationCategoryTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const medicationCategoryId = editBtn.getAttribute('data-id')
            http.get(`/medicationcategory/${ medicationCategoryId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateMedicationCategoryModal, saveMedicationCategoryBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Mediaction Category?')) {
                const mediactionCategory = deleteBtn.getAttribute('data-id')
                http.delete(`/medicationcategory/${mediactionCategory}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            expenseCategoryTable ? expenseCategoryTable.draw() : ''
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
        }
    })

    createMedicationCategoryBtn.addEventListener('click', function () {
        createMedicationCategoryBtn.setAttribute('disabled', 'disabled')
        http.post('/medicationcategory', getDivData(newMedicationCategoryModal._element), {"html": newMedicationCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newMedicationCategoryModal.hide()
                    clearDivValues(newMedicationCategoryModal._element)
                    medicationCategoryTable ? medicationCategoryTable.draw() : ''
                }
                createMedicationCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createMedicationCategoryBtn.removeAttribute('disabled')
            console.log(error)
        })

    })

    saveMedicationCategoryBtn.addEventListener('click', function (event) {
        const medicationCategoryId = event.currentTarget.getAttribute('data-id')
        saveMedicationCategoryBtn.setAttribute('disabled', 'disabled')
        http.post(`/medicationcategory/${medicationCategoryId}`, getDivData(updateMedicationCategoryModal._element), {"html": updateMedicationCategoryModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateMedicationCategoryModal.hide()
                medicationCategoryTable ? medicationCategoryTable.draw() : ''
            }
            saveMedicationCategoryBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveMedicationCategoryBtn.removeAttribute('disabled')
            console.log(error)
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

    newMedicationCategoryModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newMedicationCategoryModal._element)
        medicationCategoryTable ? medicationCategoryTable.draw() : ''
    })
})
