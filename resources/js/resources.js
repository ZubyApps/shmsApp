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
    
    const newAddResourceStockModal              = new Modal(document.getElementById('newAddResourceStockModal'))
    const updateAddResourceStockModal           = new Modal(document.getElementById('updateAddResourceStockModal'))

    const addResourceSubCategoryBtn             = document.querySelector('#addResourceSubCategoryBtn')
    const addResourceBtn                        = document.querySelector('#addResourceBtn')
    const addResourceStockBtn                   = document.querySelector('#addResourceStockBtn')

    const createResourceSubCategoryBtn          = document.querySelector('#createResourceSubCategoryBtn')
    const saveResourceSubCategoryBtn            = document.querySelector('#saveResourceSubCategoryBtn')
    
    const createAddResourceStockBtn             = document.querySelector('#createResourceSubCategoryBtn')
    const saveAddResourceStockBtn               = document.querySelector('#saveResourceSubCategoryBtn')

    const createResourceBtn                     = document.querySelector('#createResourceBtn')
    const saveResourceBtn                       = document.querySelector('#saveResourceBtn')

    const newResourceCategoryInput              = document.querySelector('#newResourceCategory')
    const updateResourceCategoryInput           = document.querySelector('#updateResourceCategory')

    const newResourceSubCategoryDatalistEl      = document.querySelector('#newSubCategoryList')
    const updateResourceSubCategoryDatalistEl   = document.querySelector('#updateSubCategoryList')

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
        // lengthMenu:[20, 40, 80, 200],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            }
        ],
        rowCallback: (row, data) => {
            if ( data.isActive) {
                row.classList.add('fw-semibold')
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
                                <a class="btn toggleActiveStatusBtn"  href="#" data-id="${ row.id }">
                                ${!row.isActive ? '<i class="bi bi-x-square-fill text-danger  tooltip-test" title="activate"></i>' : '<i class="bi bi-check-square-fill text-primary  tooltip-test" title="activate"></i>'}
                                </a>
                            </div>
                            <div class="dropdown">
                                <a class="text-primary tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                <i class="bi bi-gear fs-4" role="button"></i>
                                </a>
                                    <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item addStockBtn tooltip-test" title="remove" href="#" data-id="${ row.id }">
                                            <i class="bi bi-plus-square text-primary"></i> Add stock
                                        </a>
                                        <a class="dropdown-item updateBtn tooltip-test" title="remove" href="#" data-id="${ row.id }">
                                        <i class="bi bi-pencil-fill text-primary"></i> update
                                        </a>
                                        <a class="dropdown-item deleteBtn tooltip-test" title="delete" href="#" data-id="${ row.id }">
                                            <i class="bi bi-x-circle-fill text-primary"></i> Remove
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
                                    <a class="btn toggleActiveStatusBtn"  href="#" data-id="${ row.id }">
                                    ${!row.isActive ? '<i class="bi bi-x-square-fill text-danger tooltip-test fs-4" title="activate"></i>' : '<i class="bi bi-check-square-fill text-primary tooltip-test fs-4" title="activate"></i>'}
                                    </a>
                                </div>
                                <div class="dropdown">
                                <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                <i class="bi bi-gear fs-6" role="button"></i>
                                </a>
                                    <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item addStockBtn tooltip-test" title="remove" href="#" data-id="${ row.id }">
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
        const toggleActiveStatusBtn  = event.target.closest('.toggleActiveStatusBtn')
        const deleteBtn                 = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const resourceId = editBtn.getAttribute('data-id')
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
        ajax:  '/resourcesubcategory/load',
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

    document.querySelector('#addResourceStockTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const addResourceStockId = editBtn.getAttribute('data-id')
            http.get(`/addresourcestock/${ addResourceStockId }`)
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
                const addResourceStockId = deleteBtn.getAttribute('data-id')
                http.delete(`/addresourcestock/${addResourceStockId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            addResourceStockIdTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    addResourceStockBtn.addEventListener('click', function () {
        newAddResourceStockModal.show()
    })

    createAddResourceStockBtn.addEventListener('click', function () {
        createAddResourceStockBtn.setAttribute('disabled', 'disabled')
        http.post('/addresourcestock', getDivData(newAddResourceStockModal._element), {"html": newAddResourceStockModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                    newAddResourceStockModal.hide()
                    clearDivValues(newResourceSubCategoryModal._element)
                    addResourceStockTable.draw()
                }
            createAddResourceStockBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error.response.data.message)
            createAddResourceStockBtn.removeAttribute('disabled')
        })

    })

    saveAddResourceStockBtn.addEventListener('click', function (event) {
        const addResourceStockId = event.currentTarget.getAttribute('data-id')
        saveAddResourceStockBtn.setAttribute('disabled', 'disabled')
        http.post(`/addresourcestock/${addResourceStockId}`, getDivData(updateAddResourceStockModal._element), {"html": updateAddResourceStockModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateAddResourceStockModal.hide()
                addResourceStockTable.draw()
            }
            saveAddResourceStockBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            alert(error)
            saveAddResourceStockBtn.removeAttribute('disabled')
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