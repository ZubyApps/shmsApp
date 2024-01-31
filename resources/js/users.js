import { Modal} from "bootstrap";
import { clearDivValues, clearItemsList, getDivData, removeAttributeLoop, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, dispatchEvent, clearValidationErrors, openModals } from "./helpers"
import http from "./http";
import jQuery, { error } from "jquery";
import { getAllStaffTable } from "./tables/usersTables";

window.addEventListener('DOMContentLoaded', function () {

    const newStaffModal     = new Modal(document.getElementById('newStaffModal'))
    const editStaffModal    = new Modal(document.getElementById('editStaffModal'))
    const designationModal  = new Modal(document.getElementById('designationModal'))

    const newStaffBtn       = document.getElementById('newStaffBtn')
    const registerStaffBtn  = document.getElementById('registerStaffBtn')
    const saveStaffBtn      = document.getElementById('saveStaffBtn')
    const designateBtn      = document.getElementById('designateBtn')
        
    newStaffBtn.addEventListener('click', function() {
        newStaffModal.show()
    })

    const allStaffTable = getAllStaffTable('allStaffTable')

    registerStaffBtn.addEventListener('click', function () {
        registerStaffBtn.setAttribute('disabled', 'disabled')
        http.post('/users', getDivData(newStaffModal._element), {"html": newStaffModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newStaffModal.hide()
                clearDivValues(newStaffModal._element)
                allStaffTable.draw()
            }
            registerStaffBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            registerStaffBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    saveStaffBtn.addEventListener('click', function (event) {
        const staffId = event.currentTarget.getAttribute('data-id')
        saveStaffBtn.setAttribute('disabled', 'disabled')
        http.patch(`/users/${staffId}`, getDivData(editStaffModal._element), {"html": editStaffModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                editStaffModal.hide()
                clearDivValues(editStaffModal._element)
                clearValidationErrors(editStaffModal._element)
                allStaffTable.draw()
            }
            saveStaffBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveStaffBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    document.querySelector('#allStaffTable').addEventListener('click', function (event) {
        const editBtn                    = event.target.closest('.updateBtn')
        const deleteUserBtn              = event.target.closest('.deleteUserBtn')
        const designationBtn             = event.target.closest('.designationBtn')
        const deleteDesignationBtn       = event.target.closest('.deleteDesignationBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const staffId = editBtn.getAttribute('data-id')
            http.get(`/users/${ staffId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(editStaffModal, saveStaffBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                })
        }

        if (deleteUserBtn){
            deleteUserBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Staff?')) {
                const staffId = deleteUserBtn.getAttribute('data-id')
                http.delete(`/users/${staffId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            allStaffTable.draw()
                        }
                        deleteUserBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        if (error.response.status === 403){alert(error.response.data.message); deleteUserBtn.removeAttribute('disabled')}
                        console.log(error)
                    })
            }  
        }

        if (designationBtn){
            designationBtn.setAttribute('disabled', 'disabled')
            designationModal._element.querySelector('#designateBtn').setAttribute('data-id', designationBtn.getAttribute('data-id'))
            designationModal._element.querySelector('#fullName').value = designationBtn.getAttribute('data-name')
            designationModal.show()
            setTimeout(()=>{designationBtn.removeAttribute('disabled')}, 1500)
        }

        if (deleteDesignationBtn){
            deleteDesignationBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to remove this Designation?')) {
                const id = deleteDesignationBtn.getAttribute('data-id')
                http.delete(`/users/designate/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            allStaffTable.draw()
                        }
                        deleteDesignationBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        if (error.response.status === 403){alert(error.response.data.message); deleteDesignationBtn.removeAttribute('disabled')}
                        console.log(error)
                    })
            }
        }
    })

    designateBtn.addEventListener('click', function (event) {
        const staffId = event.currentTarget.getAttribute('data-id')
        designateBtn.setAttribute('disabled', 'disabled')
        http.post(`/users/designate/${staffId}`, getDivData(designationModal._element), {"html": designationModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                designationModal.hide()
                clearDivValues(designationModal._element)
                clearValidationErrors(designationModal._element)
                allStaffTable.draw()
            }
            designateBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            designateBtn.removeAttribute('disabled')
            if (error.response.status === 403){alert(error.response.data.message); designateBtn.removeAttribute('disabled')}
            console.log(error)
            // alert(error.response.status)
        })
    })

    designationModal.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(designationModal._element)
    })
})