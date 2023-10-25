import { Modal } from "bootstrap"
import http from "../http"
import { getDivData, clearDivValues, clearValidationErrors, getSelctedText } from "../helpers"


window.addEventListener('DOMContentLoaded', function(){
    const categoryInput             = document.querySelector('#sponsorCategory1')

    const registrationBillDiv1      = document.querySelector('.registrationBillDiv1')
    
    const allSponsorInputsDiv       = document.querySelector('.allSponsorInputsDiv')

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
})
