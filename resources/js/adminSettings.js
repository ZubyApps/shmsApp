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
import { getExpenseCategoryTable, getMarkedForTable, getMedicationCategoryTable, getOtherSettingsTable, getPayMethodTable, getResourceCategoryTable, getResourceStockDateTable, getSponsorCategoryTable, getUnitDescriptionTable, getWardTable } from "./tables/settingsTables";


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

    const newPayMethodModal                = new Modal(document.getElementById('newPayMethodModal'))
    const editPayMethodModal               = new Modal(document.getElementById('editPayMethodModal'))

    const newUnitDescriptionModal          = new Modal(document.getElementById('newUnitDescriptionModal'))
    const editUnitDescriptionModal         = new Modal(document.getElementById('editUnitDescriptionModal'))

    const newMarkedForModal                = new Modal(document.getElementById('newMarkedForModal'))
    const editMarkedForModal               = new Modal(document.getElementById('editMarkedForModal'))

    const newWardModal                     = new Modal(document.getElementById('newWardModal'))
    const editWardModal                    = new Modal(document.getElementById('editWardModal'))

    // const addSponsorCategoryBtn             = document.querySelector('#addSponsnorCategoryBtn')
    const addResourceStockDateBtn           = document.querySelector('#addResourceStockDateBtn')
    // const addResourceCategoryBtn            = document.querySelector('#addResourceCategoryBtn')
    const addPayMethodBtn                   = document.querySelector('#addPayMethodBtn')
    const addExpenseCategoryBtn             = document.querySelector('#addExpenseCategoryBtn')
    const addMedicationCategoryBtn          = document.querySelector('#addMedicationCategoryBtn')
    const addUnitDescriptionBtn             = document.querySelector('#addUnitDescriptionBtn')
    const addMarkedForBtn                   = document.querySelector('#addMarkedForBtn')
    const addWardBtn                        = document.querySelector('#addWardBtn')

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

    const createUnitDescriptionBtn          = document.querySelector('#createUnitDescriptionBtn')
    const saveUnitDescriptionBtn            = document.querySelector('#saveUnitDescriptionBtn')

    const createMarkedForBtn                = document.querySelector('#createMarkedForBtn')
    const saveMarkedForBtn                  = document.querySelector('#saveMarkedForBtn')

    const createWardBtn                     = document.querySelector('#createWardBtn')
    const saveWardBtn                       = document.querySelector('#saveWardBtn')
    
    const sponsorCategoryTab                = document.querySelector('#nav-sponsorCategory-tab')
    const resourceStockDateTab              = document.querySelector('#nav-resourceStockDate-tab')
    const resourceCategoryTab               = document.querySelector('#nav-resourceCategory-tab')
    const payMethodTab                      = document.querySelector('#nav-payMethod-tab') 
    const expenseCategoryTab                = document.querySelector('#nav-expenseCategory-tab')
    const medicationCategoryTab             = document.querySelector('#nav-medicationCategory-tab')
    const unitDescriptionTab                = document.querySelector('#nav-unitDescription-tab')
    const markedForTab                      = document.querySelector('#nav-markedFor-tab')
    const wardTab                           = document.querySelector('#nav-ward-tab')
    const otherSettingsTab                  = document.querySelector('#nav-otherSettings-tab')

    let resourceStockDateTable, resourceCategoryTable, payMethodTable, expenseCategoryTable, medicationCategoryTable, unitDescriptionTable, markedForTable, wardTable, otherSettingsTable

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

    unitDescriptionTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#unitDescriptionTable' )){
            $('#unitDescriptionTable').dataTable().fnDraw()
        } else {
            unitDescriptionTable = getUnitDescriptionTable('unitDescriptionTable')
        }
    })

    markedForTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#markedForTable' )){
            $('#markedForTable').dataTable().fnDraw()
        } else {
            markedForTable = getMarkedForTable('markedForTable')
        }
    })

    wardTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#wardTable' )){
            $('#wardTable').dataTable().fnDraw()
        } else {
            wardTable = getWardTable('wardTable')
        }
    })

    otherSettingsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#otherSettingsTable' )){
            $('#otherSettingsTable').dataTable().fnDraw()
        } else {
            otherSettingsTable = getOtherSettingsTable('#otherSettingsTable')
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

    addUnitDescriptionBtn.addEventListener('click', function () {
        newUnitDescriptionModal.show()
    })

    document.querySelector('#unitDescriptionTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')
        // const updateAllBtn  = event.target.closest('.updateAll')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const unitdescriptionId = editBtn.getAttribute('data-id')
            http.get(`/unitdescription/${ unitdescriptionId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editUnitDescriptionModal, saveUnitDescriptionBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Unit Description?')) {
                const unitdescriptionId = deleteBtn.getAttribute('data-id')
                http.delete(`/unitdescription/${unitdescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            unitDescriptionTable ? unitDescriptionTable.draw() : ''
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
        }

        // if (updateAllBtn){
        //     updateAllBtn.setAttribute('disabled', 'disabled')
        //     const unitdescriptionId = updateAllBtn.getAttribute('data-id')
        //     http.patch(`/unitdescription/updateall/${ unitdescriptionId }`)
        //         .then((response) => {
        //             if (response.status >= 200 || response.status <= 300) {
        //                 unitDescriptionTable ? unitDescriptionTable.draw() : ''
        //             }
        //             updateAllBtn.removeAttribute('disabled')
        //         })
        //         .catch((error) => {
        //             updateAllBtn.removeAttribute('disabled')
        //             console.log(error)
        //         })
        // }
    })

    createUnitDescriptionBtn.addEventListener('click', function () {
        createUnitDescriptionBtn.setAttribute('disabled', 'disabled')
        http.post('/unitdescription', getDivData(newUnitDescriptionModal._element), {"html": newUnitDescriptionModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newUnitDescriptionModal.hide()
                    clearDivValues(newUnitDescriptionModal._element)
                    unitDescriptionTable ? unitDescriptionTable.draw() : ''
                }
                createUnitDescriptionBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createUnitDescriptionBtn.removeAttribute('disabled')
            console.log(error)
        })

    })

    saveUnitDescriptionBtn.addEventListener('click', function (event) {
        const unitdescriptionId = event.currentTarget.getAttribute('data-id')
        saveUnitDescriptionBtn.setAttribute('disabled', 'disabled')
        http.post(`/unitdescription/${unitdescriptionId}`, getDivData(editUnitDescriptionModal._element), {"html": editUnitDescriptionModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                editUnitDescriptionModal.hide()
                unitDescriptionTable ? unitDescriptionTable.draw() : ''
            }
            saveUnitDescriptionBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveUnitDescriptionBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    addMarkedForBtn.addEventListener('click', function () {
        newMarkedForModal.show()
    })

    document.querySelector('#markedForTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const markedForId = editBtn.getAttribute('data-id')
            http.get(`/markedfor/${ markedForId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editMarkedForModal, saveMarkedForBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Mark?')) {
                const markedForId = deleteBtn.getAttribute('data-id')
                http.delete(`/markedfor/${markedForId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            markedForTable ? markedForTable.draw() : ''
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

    createMarkedForBtn.addEventListener('click', function () {
        createMarkedForBtn.setAttribute('disabled', 'disabled')
        http.post('/markedfor', getDivData(newMarkedForModal._element), {"html": newMarkedForModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newMarkedForModal.hide()
                    clearDivValues(newMarkedForModal._element)
                    markedForTable ? markedForTable.draw() : ''
                }
                createMarkedForBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createMarkedForBtn.removeAttribute('disabled')
            console.log(error)
        })

    })

    saveMarkedForBtn.addEventListener('click', function (event) {
        const markedForId = event.currentTarget.getAttribute('data-id')
        saveMarkedForBtn.setAttribute('disabled', 'disabled')
        http.post(`/markedfor/${markedForId}`, getDivData(editMarkedForModal._element), {"html": editMarkedForModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                editMarkedForModal.hide()
            }
            saveMarkedForBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveMarkedForBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    addWardBtn.addEventListener('click', function () {
        newWardModal.show()
    })

    document.querySelector('#wardTable').addEventListener('click', function (event) {
        const editBtn       = event.target.closest('.updateBtn')
        const deleteBtn     = event.target.closest('.deleteBtn')
        const clearWardBtn  = event.target.closest('.clearWardBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const wardId = editBtn.getAttribute('data-id')
            http.get(`/ward/${ wardId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editWardModal, saveWardBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    editBtn.removeAttribute('disabled')
                    console.log(error)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Ward?')) {
                const wardId = deleteBtn.getAttribute('data-id')
                http.delete(`/ward/${wardId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            wardTable ? wardTable.draw() : ''
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
        }

        if (clearWardBtn){
            clearWardBtn.setAttribute('disabled', 'disabled')
            const wardId = clearWardBtn.getAttribute('data-id')
            http.patch(`/ward/clear/${ wardId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        wardTable ? wardTable.draw() : ''
                    }
                    clearWardBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    clearWardBtn.removeAttribute('disabled')
                    console.log(error)
                })
        }
    })

    createWardBtn.addEventListener('click', function () {
        createWardBtn.setAttribute('disabled', 'disabled')
        http.post('/ward', getDivData(newWardModal._element), {"html": newWardModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newWardModal.hide()
                    clearDivValues(newWardModal._element)
                    wardTable ? wardTable.draw() : ''
                }
                createWardBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createWardBtn.removeAttribute('disabled')
            console.log(error)
        })

    })

    saveWardBtn.addEventListener('click', function (event) {
        const wardId = event.currentTarget.getAttribute('data-id')
        saveWardBtn.setAttribute('disabled', 'disabled')
        http.post(`/ward/${wardId}`, getDivData(editWardModal._element), {"html": editWardModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                editWardModal.hide()
                wardTable ? wardTable.draw() : ''
            }
            saveWardBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveWardBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    document.querySelector('#otherSettingsTable').addEventListener('click', function (event) {
        const optionSpan    = event.target.closest('.optionSpan')
        
        if (optionSpan){
            const name          = optionSpan.getAttribute('data-name') 
            const div           = optionSpan.parentElement
            const optionSelect  = div.querySelector('.optionSelect')
            optionSpan.classList.add('d-none')
            optionSelect.classList.remove('d-none')
            const urlSuffix           = name == 'Pre Search' ? 'presearch' : name == 'Nursing Performance Benchmark' ? 'nursingbenchmark' : 'feverBenchmark'
            optionSelect.addEventListener('blur', function () {
                http.put(`/admin/settings/${urlSuffix}`, {value: optionSelect.value}, {'html' : div})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        otherSettingsTable ? otherSettingsTable.draw() : ''
                    }
                })
                .catch((error) => {
                    console.log(error)
                    otherSettingsTable ? otherSettingsTable.draw() : ''

                })
            })
        }
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

    newUnitDescriptionModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newUnitDescriptionModal._element)
        unitDescriptionTable ? unitDescriptionTable.draw() : ''
    })

    newMarkedForModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newMarkedForModal._element)
        markedForTable ? markedForTable.draw() : ''
    })

    newWardModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newWardModal._element)
        wardTable ? wardTable.draw() : ''
    })
})
