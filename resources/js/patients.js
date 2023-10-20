import { Modal } from "bootstrap"
import { MaskInput } from "maska"
import { getDivData, clearDivValues, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId } from "./helpers"
import http from "./http"


window.addEventListener('DOMContentLoaded', function(){
    const newPatientModal           = new Modal(document.getElementById('newPatientModal'))
    const updatePatientModal        = new Modal(document.getElementById('updatePatientModal'))
    const initiatePatientModal      = new Modal(document.getElementById('initiatePatientModal'))

    const mask                      = new MaskInput(".newCardNumber", {tokens: {A: { pattern: /[A-Z]/, transform: (chr) => chr.toUpperCase() }}})
    const mask1                     = new MaskInput(".oldCardNumber", {tokens: {A: { pattern: /[A-Z]/, transform: (chr) => chr.toUpperCase() }}})
    const mask2                     = new MaskInput(".ancCardNumber")

    const newPatientBtn                 = document.getElementById('newPatient')
    const initiatePatientVisitBtn       = document.getElementById('initiate')

    const patientTypeInput                 = document.querySelector('.patientType')
    const patientTypeInputAncOption        = document.querySelector('.ancOption')

    const staffIdDiv                        = document.querySelector('.staffIdDiv')
    const sex                               = document.querySelector('.sex')

    const sponsorCategoryArray              = ['self', 'family']
    const sponsorCategoryInput              = document.querySelector('.sponsorCategory')
    const sponsorCategoryInputFamilyOption  = document.querySelector('.familyOption')
    const sponsorNameDiv                    = document.querySelector('.sponsorNameDiv')
    const sponsorNameInput                  = document.querySelector('.sponsorName')

    const registrationBillDiv               = document.querySelector('.registrationBillDiv')
    const selfRegistrationBillInput         = document.querySelector('.selfRegistrationBill')
    const familyRegistrationBillInput       = document.querySelector('.familyRegistrationBill')
    const familyRegistrationBillOption      = document.querySelector('.familyRegistrationBillOption')
    const ancRegistrationBillInput          = document.querySelector('.ancRegistrationBill') 

    const allPatientInputsDiv               = document.querySelector('.allPatientInputsDiv')

    const newPatientSponsorInputEl          = document.querySelector('#sponsor')
    const newPatientSponsorDatalistEl       = document.querySelector('#sponsorList')

    const newCardNumber                     = document.querySelector('.newCardNumber')
    const oldCardNumber                     = document.querySelector('.oldCardNumber')
    const ancCardNumber                     = document.querySelector('.ancCardNumber')

    const registerBtn                       = document.querySelector('#registerBtn')
    const saveBtn                           = document.querySelector('#saveBtn')

    newPatientBtn.addEventListener('click', function() {
        newPatientModal.show()
    })

    newPatientModal._element.addEventListener('hidden.bs.modal', function () {
        
    })

    initiatePatientVisitBtn.addEventListener('click', function() {
        initiatePatientModal.show()
    })

    sponsorCategoryInput.addEventListener('change', function() {
            if (sponsorCategoryInput.value) {
                http.get(`/sponsor/list/${sponsorCategoryInput.value}`,).then((response) => {
                    displayList(newPatientSponsorDatalistEl, response.data)
                })
            }

        if (sponsorCategoryArray.includes(getSelctedText(sponsorCategoryInput).text.toLowerCase())){             
            staffIdDiv.classList.add('d-none')

            if (getSelctedText(sponsorCategoryInput).text.toLowerCase() === 'self'){
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
                        if (getSelctedText(sponsorCategoryInput).text.toLowerCase() === 'family'){
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
                    sponsorNameInput.setAttribute('name', 'sponsorName')
                    getSelctedText(sponsorCategoryInput).text.toLowerCase() === 'family' ? '' : patientTypeInputAncOption.removeAttribute('disabled')
                }
        } else{

            // if (getSelctedText(patientTypeInput).text.toLowerCase() === 'anc'){
            //     familyRegistrationBillInput.classList.add('d-none')
            //     familyRegistrationBillInput.removeAttribute('name')
            // } 
            sponsorNameDiv.classList.remove('d-none')
            sponsorNameInput.setAttribute('name', 'sponsorName')
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
                    oldCardNumber.hasAttribute('name') ? oldCardNumber.removeAttribute('name') : ''
                    ancCardNumber.hasAttribute('name') ? ancCardNumber.removeAttribute('name') : ''

                    !oldCardNumber.classList.contains('d-none') ? oldCardNumber.classList.add('d-none') : ''
                    oldCardNumber.value = ''
                    !ancCardNumber.classList.contains('d-none') ? ancCardNumber.classList.add('d-none') : ''
                    ancCardNumber.value = ''
                    newCardNumber.classList.contains('d-none') ? newCardNumber.classList.remove('d-none'): ''

                    if (getSelctedText(sponsorCategoryInput).text.toLowerCase() === 'family'){
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
                    newCardNumber.hasAttribute('name') ? newCardNumber.removeAttribute('name') : ''
                    ancCardNumber.hasAttribute('name') ? ancCardNumber.removeAttribute('name') : ''

                    !newCardNumber.classList.contains('d-none') ? newCardNumber.classList.add('d-none')  : ''
                    newCardNumber.value = ''
                    !ancCardNumber.classList.contains('d-none') ? ancCardNumber.classList.add('d-none') : ''
                    ancCardNumber.value = ''
                    oldCardNumber.classList.contains('d-none') ? oldCardNumber.classList.remove('d-none'): ''
                    
                    selfRegistrationBillInput.classList.add('d-none')
                    ancRegistrationBillInput.classList.add('d-none')
                    selfRegistrationBillInput.removeAttribute('name')
                    ancRegistrationBillInput.removeAttribute('name')

                    if (getSelctedText(sponsorCategoryInput).text.toLowerCase() === 'self'){
                         registrationBillDiv.classList.add('d-none')
                    }

                    if (sponsorCategoryInput.value === 'Family')
                    {
                        familyRegistrationBillOption.setAttribute('disabled', 'disabled')
                    } 

                    sponsorCategoryInputFamilyOption.removeAttribute('disabled')
                    sex.removeAttribute('disabled')
                    break;
                case 'ANC': 
                    ancCardNumber.setAttribute('name', 'cardNumber')
                    newCardNumber.hasAttribute('name') ? newCardNumber.removeAttribute('name') : ''
                    oldCardNumber.hasAttribute('name') ? oldCardNumber.removeAttribute('name') : ''

                    !newCardNumber.classList.contains('d-none') ? newCardNumber.classList.add('d-none'): ''
                    newCardNumber.value = ''
                    !oldCardNumber.classList.contains('d-none') ? oldCardNumber.classList.add('d-none'): ''
                    oldCardNumber.value = ''
                    ancCardNumber.classList.contains('d-none') ? ancCardNumber.classList.remove('d-none'):''

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
            familyRegistrationBillOption.removeAttribute('disabled', 'disabled')
            allPatientInputsDiv.classList.add('d-none')
            newCardNumber.value = ''
            oldCardNumber.value = ''
            ancCardNumber.value = ''

        }
    })

    registerBtn.addEventListener('click', function () {
        const sponsor = getDatalistOptionId(newPatientSponsorInputEl, newPatientSponsorDatalistEl)
    
        let data = {...getDivData(newPatientModal._element), sponsor }
        console.log(data)

        http.post('/patients', {...data}, {"html": newPatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newPatientModal._element.hide()
                clearDivValues(newPatientModal._element)
            }
        })
        .catch((error) => {
            alert(error.response.data.message)
        })
    })

    saveBtn.addEventListener('click', function () {
        console.log(getDivData(updatePatientModal._element))
    })

})
