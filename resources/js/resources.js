import { Modal } from "bootstrap";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors, openModals, displayList, getDatalistOptionId} from "./helpers"
import http from "./http";
import jQuery from "jquery";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';


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

    const resourceSubCategoryTable = new DataTable('#resourceSubCategoryTable', {
        serverSide: true,
        ajax:  '/resourcesubcategory/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "name"},
            {data: "description"},
            {data: "category"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                    if (row.count < 1) {
                         return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                    } else {
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                            </div>
                        `
                    }
                           
                } 
                    }
        ]
    });

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


    const resourceTable = new DataTable('#resourceTable', {
        serverSide: true,
        ajax:  '/resources/load',
        orderMulti: true,
        search:true,
        fixedHeader: true,
        lengthMenu:[20, 40, 80, 200],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            }
        ],
        rowCallback: (row, data) => {
            if ( !data.isActive) {
                row.classList.add('table-danger')
            }

            return row
        },
        columns: [
            {data: "name"},
            {data: "flag"},
            {
                visible: false,
                data: "category"
            },
            {data: "subCategory"},
            {data: "unit"},
            {data: "purchasePrice"},
            {data: "sellingPrice"},
            {data: "reOrder"},
            {data: "stock"},
            {
                visible: false,
                data: "expiryDate"
            },
            {data: row => () => {
                return row.expired
            }},
            {
                visible: false,
                data: "createdBy"
            },
            {
                visible: false,
                data: "createdAt"
            },
            {
                sortable: false,
                data: row => () => {
                    if (row.count < 1) {
                         return `
                        <div class="d-flex flex-">
                            <div>
                                <a class="btn btn-outline-${!row.isActive ? 'danger' : 'primary'} toggleActiveStatusBtn"  href="#" data-id="${ row.id }">
                                ${!row.isActive ? '<i class="bi bi-x-square-fill  tooltip-test" title="activate"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="activate"></i>'}
                                </a>
                            </div>
                            <div class="dropdown ms-1">
                                <a class="text-${!row.isActive ? 'danger' : 'primary'} tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                <i class="bi bi-gear fs-4" role="button"></i>
                                </a>
                                    <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item addStockBtn tooltip-test" title="remove" href="#" data-id="${ row.id }">
                                            <i class="bi bi-plus-square text-primary"></i> Add stock
                                        </a>
                                        <a class="dropdown-item updateBtn tooltip-test" title="remove" href="#" data-id="${ row.id }">
                                        <i class="bi bi-pencil-fill text-primary"></i> Edit
                                        </a>
                                        <a class="dropdown-item deleteBtn tooltip-test" title="delete" href="#" data-id="${ row.id }">
                                            <i class="bi bi-x-circle-fill text-primary"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <div>
                                    <a class="btn btn-outline-${!row.isActive ? 'danger' : 'primary'} toggleActiveStatusBtn"  href="#" data-id="${ row.id }">
                                    ${!row.isActive ? '<i class="bi bi-x-square-fill tooltip-test fs-4" title="activate"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="activate"></i>'}
                                    </a>
                                </div>
                                <div class="dropdown ms-1">
                                <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                <i class="bi bi-gear fs-4" role="button"></i>
                                </a>
                                    <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item addStockBtn tooltip-test" title="Add stock" href="#" data-id="${ row.id }">
                                            <i class="bi bi-plus-square text-primary"></i> Add stock
                                        </a>
                                        <a class="dropdown-item updateBtn tooltip-test" title="update" href="#" data-id="${ row.id }">
                                        <i class="bi bi-pencil-fill text-primary"></i> Update
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        `
                    }
                           
                } 
                    }
        ]
    });

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

    const addResourceStockTable = new DataTable('#addResourceStockTable', {
        serverSide: true,
        ajax:  '/addresourcestock/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "resource"},
            {data: "qty"},
            {data: "purchasePrice"},
            {data: "sellingPrice"},
            {data: "expiryDate"},
            {data: "supplier"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                        return `
                            <div class="d-flex flex-">
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                } 
            }
        ]
    });

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
        console.log(resourceSupplierId)
        createAddResourceStockBtn.setAttribute('disabled', 'disabled')
        let data = {...getDivData(newAddResourceStockModal._element), resourceId, resourceSupplierId}

        http.post('/addresourcestock', {...data}, {"html": newAddResourceStockModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newAddResourceStockModal.hide()
                    clearDivValues(newAddResourceStockModal._element)
                    addResourceStockTable.draw()
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
        console.log(newSupplierInput.value)
        if (updateSupplierInput.value) {
            http.get(`/resourcesupplier/list/${updateSupplierInput.value}`).then((response) => {
                    displayList(updateSupplierDatalistEl, 'supplierOption', response.data)
            })
        }
    })

    addResourceSupplierBtn.addEventListener('click', function () {
        newResourceSupplierModal.show()
    })

    const resourceSupplierTable = new DataTable('#resourceSupplierTable', {
        serverSide: true,
        ajax:  '/resourcesupplier/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "company"},
            {data: "person"},
            {data: "phone"},
            {data: "email"},
            {data: "address"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary editBtn tooltip-test" title="update" data-id="${ row.id }">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                } 
            }
        ]
    });

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
                    resourceSupplierTable.draw()
                }
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