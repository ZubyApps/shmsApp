import { Modal } from "bootstrap"
import { clearDivValues, clearValidationErrors, getDivData, handleValidationErrors } from "./helpers"
import http from "./http"



window.addEventListener('DOMContentLoaded', function(){

    const registerPatientModal  = new Modal(document.getElementById('registerPatientModal'))
    const submitDiv             = document.querySelector('#submitDiv')
    const submitPatientBtn      = document.querySelector('#submitPatientBtn')
    const phone                 = document.querySelector('#phone')
    const nextOfKinPhone        = document.querySelector('#nextOfKinPhone')


    const params = new URL(document.location.toString()).searchParams
    const objParams = Object.fromEntries(params)

    

    // populateDiv(registerPatientModal, submitPatientBtn, objParams)

    registerPatientModal.show()

    nextOfKinPhone.addEventListener('input', function () {
        if (phone.value === nextOfKinPhone.value) {
            const message = {"nextOfKinPhone": ["This number must be different from Patient's phone number"]}
                            
            handleValidationErrors(message, submitDiv)
        } else {
            clearValidationErrors(submitDiv)
        }
    })

    submitPatientBtn.addEventListener('click', function () {
        // const sponsor = getPatientSponsorDatalistOptionId(newPatientModal, newPatientSponsorInputEl, newPatientSponsorDatalistEl)
        const id = submitPatientBtn.getAttribute('data-id')

        submitPatientBtn.setAttribute('disabled', 'disabled')
        // let data = {...getDivData(submitDiv) }
    
        http.post(`/submitpatient/${id}`, {...getDivData(submitDiv)}, {"html": submitDiv})
        .then((response) => {
            if (response){
                clearDivValues(submitDiv)
                submitPatientBtn.removeAttribute('disabled')
                window.history.replaceState({}, document.title, "/" + "registerp" )
                registerPatientModal.hide()
            }
        })
        .catch((error) => {
            console.log(error)
            submitPatientBtn.removeAttribute('disabled')
        })
    })
})

function populateDiv(modal, button, {id, expires, signature, ...data}) {

    for (let name in data) {
        
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)

        nameInput.value = data[name]
    }
    
    button.setAttribute('data-id', id)
    user ? modal.show() : ''
}