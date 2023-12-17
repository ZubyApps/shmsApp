import { Modal } from "bootstrap"
import { MaskInput } from "maska"
import { getDivData, clearDivValues, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId, handleValidationErrors } from "../helpers"
import http from "../http"


window.addEventListener('DOMContentLoaded', function(){
    const mask                      = new MaskInput(".newCardNumber", {tokens: {A: { pattern: /[A-Z]/, transform: (chr) => chr.toUpperCase() }}})
    const mask1                     = new MaskInput(".oldCardNumber", {tokens: {A: { pattern: /[A-Z]/, transform: (chr) => chr.toUpperCase() }}})
    const mask2                     = new MaskInput(".ancCardNumber")

    const patientTypeInput                 = document.querySelector('.patientType')
    const patientTypeInputAncOption        = document.querySelector('.ancOption')

    const staffIdDiv                        = document.querySelector('.staffIdDiv')
    const sex                               = document.querySelector('.sex')

    const sponsorCategoryArray              = ['self', 'family']
    const sponsorCategoryInputFamilyOption  = document.querySelector('.familyOption')
    const sponsorNameDiv                    = document.querySelector('.sponsorNameDiv')
    const sponsorNameInput                  = document.querySelector('.sponsorName')

    const registrationBillDiv               = document.querySelector('.registrationBillDiv')
    const selfRegistrationBillInput         = document.querySelector('.selfRegistrationBill')
    const familyRegistrationBillInput       = document.querySelector('.familyRegistrationBill')
    const familyRegistrationBillOption      = document.querySelector('.familyRegistrationBillOption')
    const ancRegistrationBillInput          = document.querySelector('.ancRegistrationBill') 

    const allPatientInputsDiv               = document.querySelector('.allPatientInputsDiv')

    const newSponsorCategoryInput           = document.querySelector('#newSponsorCategory')
    const updateSponsorCategoryInput        = document.querySelector('#updateSponsorCategory')

    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')
    const updatePatientSponsorDatalistEl    = document.querySelector('#updateSponsorList')

    const newCardNumber                     = document.querySelector('.newCardNumber')
    const oldCardNumber                     = document.querySelector('.oldCardNumber')
    const ancCardNumber                     = document.querySelector('.ancCardNumber')

    const phone                             = document.querySelector('#phone')
    const nextOfKinPhone                    = document.querySelector('#nextOfKinPhone')

    updateSponsorCategoryInput.addEventListener('change', function() {
        if (updateSponsorCategoryInput.value) {
            http.get(`/sponsorcategory/list_sponsors/${updateSponsorCategoryInput.value}`).then((response) => {
                    displayList(updatePatientSponsorDatalistEl, 'sponsorOption' ,response.data)

            })
        }
    })
    
    nextOfKinPhone.addEventListener('input', function () {
        if (phone.value === nextOfKinPhone.value) {
            const message = {"nextOfKinPhone": ["This number must be different from Patient's phone number"]}
                            
            handleValidationErrors(message, allPatientInputsDiv)
        } else {
            clearValidationErrors(allPatientInputsDiv)
        }
    })

    newSponsorCategoryInput.addEventListener('change', function() {
            if (newSponsorCategoryInput.value) {
                http.get(`/sponsorcategory/list_sponsors/${newSponsorCategoryInput.value}`, {params: {category: newSponsorCategoryInput.value}})
                .then((response) => {
                        displayList(newPatientSponsorDatalistEl, 'sponsorOption' ,response.data)
                })
            }

        if (sponsorCategoryArray.includes(getSelctedText(newSponsorCategoryInput).text.toLowerCase())){             
            staffIdDiv.classList.add('d-none')

            if (getSelctedText(newSponsorCategoryInput).text.toLowerCase() === 'self'){
                 patientTypeInputAncOption.removeAttribute('disabled')
                 registrationBillDiv.classList.remove('d-none')

                        if (getSelctedText(patientTypeInput).text.toLowerCase() === 'anc'){
                            familyRegistrationBillInput.classList.add('d-none')
                            familyRegistrationBillInput.removeAttribute('name')
                        } else{
                            ancRegistrationBillInput.classList.add('d-none')
                            familyRegistrationBillInput.classList.add('d-none')
                            selfRegistrationBillInput.classList.remove('d-none')
                            ancRegistrationBillInput.removeAttribute('name')
                            familyRegistrationBillInput.removeAttribute('name')
                            selfRegistrationBillInput.setAttribute('name', 'registerationBill')
                        }
                 
                } else{
                        if (getSelctedText(newSponsorCategoryInput).text.toLowerCase() === 'family'){
                            ancRegistrationBillInput.classList.add('d-none')
                            selfRegistrationBillInput.classList.add('d-none')
                            familyRegistrationBillInput.classList.remove('d-none')
                            ancRegistrationBillInput.removeAttribute('name')
                            selfRegistrationBillInput.removeAttribute('name')
                            familyRegistrationBillInput.setAttribute('name', 'registerationBill')
                            patientTypeInputAncOption.setAttribute('disabled', 'disabled')
                            patientTypeInput.value === 'Register.Old' ? 
                            familyRegistrationBillOption.setAttribute('disabled', 'disabled') : ''
                            staffIdDiv.classList.add('d-none')
                            registrationBillDiv.classList.add('d-none')
                        }
                    sponsorNameDiv.classList.remove('d-none')
                    // sponsorNameInput.setAttribute('name', 'sponsorName')
                    getSelctedText(newSponsorCategoryInput).text.toLowerCase() === 'family' ? '' : patientTypeInputAncOption.removeAttribute('disabled')
                }
        } else{
            sponsorNameDiv.classList.remove('d-none')
            // sponsorNameInput.setAttribute('name', 'sponsorName')
            registrationBillDiv.classList.add('d-none')
            ancRegistrationBillInput.removeAttribute('name')
            familyRegistrationBillInput.removeAttribute('name')
            selfRegistrationBillInput.removeAttribute('name') 
            patientTypeInputAncOption.removeAttribute('disabled')
            staffIdDiv.classList.remove('d-none')
        }

    })

    patientTypeInput.addEventListener('change', function(){
        if (patientTypeInput.value) {
            allPatientInputsDiv.classList.remove('d-none')

            switch (patientTypeInput.value) {
                case 'Regular.New': 
                    newCardNumber.setAttribute('name', 'cardNumber')
                    newCardNumber.setAttribute('id', 'cardNumber')
                    oldCardNumber.hasAttribute('name') ? oldCardNumber.removeAttribute('name') : ''
                    ancCardNumber.hasAttribute('name') ? ancCardNumber.removeAttribute('name') : ''

                    oldCardNumber.classList.add('d-none')
                    oldCardNumber.value = ''
                    ancCardNumber.classList.add('d-none')
                    ancCardNumber.value = ''
                    newCardNumber.classList.remove('d-none')

                    if (getSelctedText(newSponsorCategoryInput).text.toLowerCase() === 'family'){
                        ancRegistrationBillInput.classList.add('d-none')
                        selfRegistrationBillInput.classList.add('d-none')
                        familyRegistrationBillInput.classList.remove('d-none')
                        selfRegistrationBillInput.removeAttribute('name')
                        ancRegistrationBillInput.removeAttribute('name')
                        familyRegistrationBillInput.setAttribute('name', 'registrationBill')
                    } else {
                        ancRegistrationBillInput.classList.add('d-none')
                        familyRegistrationBillInput.classList.add('d-none') 
                        selfRegistrationBillInput.classList.remove('d-none')
                        ancRegistrationBillInput.removeAttribute('name')
                        familyRegistrationBillInput.removeAttribute('name')
                        selfRegistrationBillInput.setAttribute('name', 'registrationBill')
                        sponsorCategoryInputFamilyOption.removeAttribute('disabled')
                    }
                    familyRegistrationBillOption.removeAttribute('disabled', 'disabled')
                    sex.removeAttribute('disabled')
                    break;
                case 'Regular.Old':
                    oldCardNumber.setAttribute('name', 'cardNumber')
                    oldCardNumber.setAttribute('id', 'cardNumber')
                    newCardNumber.removeAttribute('name')
                    newCardNumber.removeAttribute('id')
                    ancCardNumber.removeAttribute('name')
                    ancCardNumber.removeAttribute('id')

                    !newCardNumber.classList.contains('d-none') ? newCardNumber.classList.add('d-none')  : ''
                    newCardNumber.value = ''
                    !ancCardNumber.classList.contains('d-none') ? ancCardNumber.classList.add('d-none') : ''
                    ancCardNumber.value = ''
                    oldCardNumber.classList.contains('d-none') ? oldCardNumber.classList.remove('d-none'): ''
                    
                    selfRegistrationBillInput.classList.add('d-none')
                    ancRegistrationBillInput.classList.add('d-none')
                    selfRegistrationBillInput.removeAttribute('name')
                    ancRegistrationBillInput.removeAttribute('name')

                    if (getSelctedText(newSponsorCategoryInput).text.toLowerCase() === 'self'){
                         registrationBillDiv.classList.add('d-none')
                    }

                    if (newSponsorCategoryInput.value === 'Family')
                    {
                        familyRegistrationBillOption.setAttribute('disabled', 'disabled')
                    } 

                    sponsorCategoryInputFamilyOption.removeAttribute('disabled')
                    sex.removeAttribute('disabled')
                    break;
                case 'ANC': 
                    ancCardNumber.setAttribute('name', 'cardNumber')
                    newCardNumber.removeAttribute('name')
                    newCardNumber.removeAttribute('id')
                    oldCardNumber.removeAttribute('name')
                    oldCardNumber.removeAttribute('id')

                    newCardNumber.classList.add('d-none')
                    newCardNumber.value = ''
                    oldCardNumber.classList.add('d-none')
                    oldCardNumber.value = ''
                    ancCardNumber.classList.remove('d-none')

                    selfRegistrationBillInput.classList.add('d-none')
                    familyRegistrationBillInput.classList.add('d-none')
                    ancRegistrationBillInput.classList.remove('d-none')
                    selfRegistrationBillInput.removeAttribute('name')
                    familyRegistrationBillInput.removeAttribute('name')
                    ancRegistrationBillInput.setAttribute('name', 'registrationBill')
                    sponsorCategoryInputFamilyOption.setAttribute('disabled', 'disabled')
                    sex.setAttribute('disabled', 'disabled')
                    sex.value = 'Female'
                    break;
                default: ''
                    break;
            }
        } else{
            familyRegistrationBillOption.removeAttribute('disabled')
            allPatientInputsDiv.classList.add('d-none')
            newCardNumber.value = ''
            oldCardNumber.value = ''
            ancCardNumber.value = ''

        }
    })


})
