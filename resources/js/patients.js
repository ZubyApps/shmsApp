import { Modal } from "bootstrap"
import { MaskInput } from "maska"


window.addEventListener('DOMContentLoaded', function(){
    const newPatientModal           = new Modal(document.getElementById('newPatientModal'))
    const updatePatientModal        = new Modal(document.getElementById('updatePatientModal'))
    const newSponsorModal           = new Modal(document.getElementById('newSponsorModal'))
    const initiatePatientModal      = new Modal(document.getElementById('initiatePatientModal'))

    const mask                      = new MaskInput(".newCardNumber", {tokens: {A: { pattern: /[A-Z]/, transform: (chr) => chr.toUpperCase() }}})
    const mask1                     = new MaskInput(".oldCardNumber", {tokens: {A: { pattern: /[A-Z]/, transform: (chr) => chr.toUpperCase() }}})
    const mask2                     = new MaskInput(".ancCardNumber")

    const newPatientBtn                 = document.getElementById('newPatient')
    const newSponsorBtn                 = document.getElementById('newSponsor')
    const initiatePatientVisitBtn       = document.getElementById('initiate')

    const cardTypeInput                 = document.querySelector('.cardType')
    const cardTypeInputAncOption        = document.querySelector('.ancOption')

    const staffIdDiv                        = document.querySelector('.staffIdDiv')
    const sex                               = document.querySelector('.sex')

    const sponsorCategoryArray              = ['Self', 'Family']
    const sponsorCategoryInput              = document.querySelector('.sponsorCategory')
    const sponsorCategoryInput1             = document.querySelector('.sponsorCategory1')
    const sponsorCategoryInputFamilyOption  = document.querySelector('.familyOption')
    const sponsorNameDiv                    = document.querySelector('.sponsorNameDiv')
    const sponsorNameInput                  = document.querySelector('.sponsorName')

    const registrationBillDiv               = document.querySelector('.registrationBillDiv')
    const registrationBillDiv1              = document.querySelector('.registrationBillDiv1')
    const selfRegistrationBillInput         = document.querySelector('.selfRegistrationBill')
    const familyRegistrationBillInput       = document.querySelector('.familyRegistrationBill')
    const familyRegistrationBillOption      = document.querySelector('.familyRegistrationBillOption')
    const ancRegistrationBillInput          = document.querySelector('.ancRegistrationBill') 

    const allPatientInputsDiv               = document.querySelector('.allPatientInputsDiv')
    const allSponsorInputsDiv               = document.querySelector('.allSponsorInputsDiv')

    const newCardNumber                     = document.querySelector('.newCardNumber')
    const oldCardNumber                     = document.querySelector('.oldCardNumber')
    const ancCardNumber                     = document.querySelector('.ancCardNumber')

    const registerBtn                       = document.querySelector('#registerBtn')
    const saveBtn                           = document.querySelector('#saveBtn')

    newPatientBtn.addEventListener('click', function() {
        newPatientModal.show()
    })

    newSponsorBtn.addEventListener('click', function() {
        newSponsorModal.show()
    })

    newPatientModal._element.addEventListener('hidden.bs.modal', function () {
        
    })

    // updatePatientModal._element.addEventListener('show.bs.modal', function () {
    //     allInputsDiv.classList.add('d-none')
    // })

    initiatePatientVisitBtn.addEventListener('click', function() {
        initiatePatientModal.show()
    })

    sponsorCategoryInput1.addEventListener('change', function () {
        if (sponsorCategoryInput1.value){
                sponsorCategoryInput1.value === 'family' ? 
                registrationBillDiv1.classList.remove('d-none') : 
                registrationBillDiv1.classList.add('d-none')
                allSponsorInputsDiv.classList.remove('d-none')
        }
        else {allSponsorInputsDiv.classList.add('d-none') 
        registrationBillDiv1.classList.add('d-none')}
    })

    sponsorCategoryInput.addEventListener('change', function() {
        if (sponsorCategoryArray.includes(sponsorCategoryInput.value)){             
            registrationBillDiv.classList.remove('d-none')
            staffIdDiv.classList.add('d-none')

            if (sponsorCategoryInput.value === 'Self'){
                 sponsorNameDiv.classList.add('d-none')
                 sponsorNameInput.removeAttribute('name')
                 cardTypeInputAncOption.removeAttribute('disabled')

                        if (cardTypeInput.value === 'ANC'){
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
                        if (sponsorCategoryInput.value === 'Family'){
                            ancRegistrationBillInput.classList.add('d-none')
                            selfRegistrationBillInput.classList.add('d-none')
                            familyRegistrationBillInput.classList.remove('d-none')
                            ancRegistrationBillInput.removeAttribute('name')
                            selfRegistrationBillInput.removeAttribute('name')
                            familyRegistrationBillInput.setAttribute('name', 'registerationBill')
                            cardTypeInputAncOption.setAttribute('disabled', 'disabled')
                            cardTypeInput.value === 'Register.Old' ? 
                            familyRegistrationBillOption.setAttribute('disabled', 'disabled') : ''
                        }
                sponsorNameDiv.classList.remove('d-none')
                sponsorNameInput.setAttribute('name', 'sponsorName')
                sponsorCategoryInput.value === 'Family' ? '' : cardTypeInputAncOption.removeAttribute('disabled')
            }
        } else{

            sponsorNameDiv.classList.remove('d-none')
            sponsorNameInput.setAttribute('name', 'sponsorName')
            registrationBillDiv.classList.add('d-none')
            ancRegistrationBillInput.removeAttribute('name')
            familyRegistrationBillInput.removeAttribute('name')
            selfRegistrationBillInput.removeAttribute('name') 
            cardTypeInputAncOption.removeAttribute('disabled')
            staffIdDiv.classList.remove('d-none')
        }

    })

    cardTypeInput.addEventListener('change', function(){
        if (cardTypeInput.value) {
            allPatientInputsDiv.classList.remove('d-none')

            switch (cardTypeInput.value) {
                case 'Regular.New': 
                    newCardNumber.setAttribute('name', 'cardNumber')
                    oldCardNumber.hasAttribute('name') ? oldCardNumber.removeAttribute('name') : ''
                    ancCardNumber.hasAttribute('name') ? ancCardNumber.removeAttribute('name') : ''

                    !oldCardNumber.classList.contains('d-none') ? oldCardNumber.classList.add('d-none') : ''
                    oldCardNumber.value = ''
                    !ancCardNumber.classList.contains('d-none') ? ancCardNumber.classList.add('d-none') : ''
                    ancCardNumber.value = ''
                    newCardNumber.classList.contains('d-none') ? newCardNumber.classList.remove('d-none'): ''

                    if (sponsorCategoryInput.value === 'Family'){
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
                    familyRegistrationBillInput.classList.remove('d-none')
                    selfRegistrationBillInput.removeAttribute('name')
                    ancRegistrationBillInput.removeAttribute('name')
                    familyRegistrationBillInput.setAttribute('name', 'registrationBill')

                    if (sponsorCategoryInput.value === 'Self'){
                        !registrationBillDiv.classList.contains('d-none') ? registrationBillDiv.classList.add('d-none') : ''
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
        console.log(getPatientFormData(newPatientModal))
    })

    saveBtn.addEventListener('click', function () {
        console.log(getPatientFormData(updatePatientModal))
    })

})

function getPatientFormData(modal) {
    let data     = {}
    const fields = [
        ...modal._element.getElementsByTagName('input'),
        ...modal._element.getElementsByTagName('select'),
        ...modal._element.getElementsByTagName('textarea')
    ]

    fields.forEach(select => {
        select.hasAttribute('name') ?
        data[select.name] = select.value : ''
    })
    
    return data
}