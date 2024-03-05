import { Modal} from "bootstrap";
import { clearDivValues, getDivData, clearValidationErrors, openModals } from "./helpers"
import http from "./http";
import jQuery, { error } from "jquery";
import { getActiveStaffTable, getAllStaffTable } from "./tables/usersTables";

window.addEventListener('DOMContentLoaded', function () {

    const newStaffModal     = new Modal(document.getElementById('newStaffModal'))
    const editStaffModal    = new Modal(document.getElementById('editStaffModal'))
    const designationModal  = new Modal(document.getElementById('designationModal'))

    const changePasswordDiv = editStaffModal._element.querySelector('.changePasswordDiv') 
    const passwordDiv       = editStaffModal._element.querySelector('.passwordDiv') 

    const newStaffBtn       = document.getElementById('newStaffBtn')
    const activeUsersBtn    = document.getElementById('activeUsersBtn')
    const registerStaffBtn  = document.getElementById('registerStaffBtn')
    const saveStaffBtn      = document.getElementById('saveStaffBtn')
    const designateBtn      = document.getElementById('designateBtn')
        
    newStaffBtn.addEventListener('click', function() {
        newStaffModal.show()
    })

    const allStaffTable = getAllStaffTable('allStaffTable')
    const activeStaffTable = getActiveStaffTable('activeStaffTable')

    activeUsersBtn.addEventListener('click', function () {
        activeStaffTable.draw()
    })

    document.querySelector('#activeStaffTable').addEventListener('click', function (event) {
        const logStaffOutBtn    = event.target.closest('.logStaffOutBtn')

        if(logStaffOutBtn){
            if (confirm('Are you sure you want to log this Staff out?')) {
                logStaffOutBtn.setAttribute('disabled', 'disabled')
                const staffId = logStaffOutBtn.getAttribute('data-id')
                http.post(`/users/logout/${staffId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        activeStaffTable.draw()
                    }
                    logStaffOutBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    logStaffOutBtn.removeAttribute('disabled')
                    console.log(error)
                })
            }
        }
    })

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
        const staffId = saveStaffBtn.getAttribute('data-id')
        console.log(staffId)
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
        const editBtn                    = event.target.closest('.updateUserBtn')
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
                    alert(error.response.data.message)
                    editBtn.removeAttribute('disabled')
                })
                editBtn.removeAttribute('disabled')
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
            console.log(designationBtn.getAttribute('data-id'))
            http.get(`/users/designation/${designationBtn.getAttribute('data-id')}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300){
                    openModals(designationModal, designationModal._element.querySelector('#designateBtn'), response.data.data)
                    designationModal.show()
                }
                designationBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                if (error.response.status === 403){alert(error.response.data.message); designationBtn.removeAttribute('disabled')}
                console.log(error)
            })
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
        console.log(staffId)
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
            console.log(error.response)
            if (error.response.status === 403){
                alert(error.response.data.message); 
                designateBtn.removeAttribute('disabled')
            }
            console.log(error)
        })
    })

    changePasswordDiv.addEventListener('click', function () {
        const radioBtn = changePasswordDiv.querySelector('#changePasswordRadioBtn')

        if (radioBtn.checked){
            passwordDiv.classList.remove('d-none')
        } else if (!radioBtn.checked){
            passwordDiv.classList.add('d-none')
        }

    })

    designationModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(designationModal._element)
    })
})