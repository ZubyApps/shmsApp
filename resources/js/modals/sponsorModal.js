import { Modal } from "bootstrap"
import http from "../http"
import { getDivData, clearDivValues, clearValidationErrors, getSelctedText } from "../helpers"


window.addEventListener('DOMContentLoaded', function(){
    const newSponsorModal           = new Modal(document.getElementById('newSponsorModal'))

    const newSponsorBtn             = document.getElementById('newSponsor')

    const categoryInput             = document.querySelector('#sponsorCategory1')

    const registrationBillDiv1      = document.querySelector('.registrationBillDiv1')
    
    const allSponsorInputsDiv       = document.querySelector('.allSponsorInputsDiv')

    const createSponsorBtn          = document.querySelector('#createSponsorBtn')

    categoryInput.addEventListener('change', function () {
        if (categoryInput.value){
            if (getSelctedText(categoryInput).text.toLowerCase() === 'family') {
                registrationBillDiv1.classList.remove('d-none')
                registrationBillDiv1.querySelector('.familyRegistrationBill').setAttribute('name', 'registrationBill')
            } else {
                registrationBillDiv1.classList.add('d-none')
                registrationBillDiv1.querySelector('.familyRegistrationBill').removeAttribute('name')
            }
                 
            allSponsorInputsDiv.classList.remove('d-none')
        }
        else {allSponsorInputsDiv.classList.add('d-none') 
        registrationBillDiv1.classList.add('d-none')}
    })

    newSponsorBtn.addEventListener('click', function() {
        newSponsorModal.show()
    })

    createSponsorBtn.addEventListener('click', function () {
        http.post('/sponsor', getDivData(newSponsorModal._element), {"html": newSponsorModal._element}).then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newSponsorModal.hide()
                clearDivValues(newSponsorModal._element)
            }
        })
    })

    newSponsorModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorModal._element)
    })

})
