import { Modal } from "bootstrap"
import * as ECT from '@whoicd/icd11ect'
import '@whoicd/icd11ect/style.css'

window.addEventListener('DOMContentLoaded', function(){
    const newConsultationModal             = new Modal(document.getElementById('newConsultationModal'))

    const consultBtn                        = document.querySelector('.consultBtn')
    const knownConditionsInput              = document.querySelector('.knownConditions')

    const saveConsultationBtn               = document.querySelector('#saveConsultationBtn')
    const addKnownClinicalInfoBtn           = document.querySelector('#addKnownClincalInfoBtn')
    const addVitalsignsBtn                  = document.querySelector('#addVitalsignsBtn')

    const knownClinicanInfoDiv              = document.querySelector('.knownClinicalInfoDiv')
    const addVitalsignsDiv                  = document.querySelector('.addVitalsignsDiv')
    const consultationDiv                   = document.querySelector('.consultationDiv')
    const investigationAndManagementDiv     = document.querySelector('.investigationAndManagmentDiv')

    const diagnosisInput                    = document.querySelector('.selectedDiagnosis')

    const mySettings = {apiServerUrl: "https://icd11restapi-developer-test.azurewebsites.net"}

      const myCallbacks = {
        selectedEntityFunction: (selectedEntity) => { 
          diagnosisInput.value += selectedEntity.code + '-' + selectedEntity.selectedText     
          ECT.Handler.clear("1")    
        }
      }

    consultBtn.addEventListener('click', function() {
        newConsultationModal.show()
    })

    knownConditionsInput.addEventListener('dblClick', function() {
        knownConditionsInput.removeAttribute('readonly')
    })

    saveConsultationBtn.addEventListener('click', function() {
        console.log(getConsultationDivData(consultationDiv))
        const tagNames = consultationDiv.querySelectorAll('input, select, textarea')
        addAttribute(tagNames, 'disabled')
        saveConsultationBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? saveConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : saveConsultationBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'
        investigationAndManagementDiv.classList.remove('d-none')
    })
      
      // ICD11 handler
    ECT.Handler.configure(mySettings, myCallbacks)

    addKnownClinicalInfoBtn.addEventListener('click', function () {
        const tagName = knownClinicanInfoDiv.querySelectorAll('input, select, textarea')
            // tagName.forEach(tag => {
            //     tag.toggleAttribute('disabled')
            // })
        addAttribute(tagName, 'disabled', '')
        addKnownClinicalInfoBtn.textContent === "Done" ? addKnownClinicalInfoBtn.innerHTML = `<i class="bi bi-arrow-up-circle"></i>
        Update` : addKnownClinicalInfoBtn.textContent = "Done"
    })

    addVitalsignsBtn.addEventListener('click', function () {
        addVitalsignsDiv.classList.toggle('d-none')
    })

})

function getConsultationFormData(modal) {
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

function getConsultationDivData(div) {
    let data     = {}
    const fields = [
        ...div.getElementsByTagName('input'),
        ...div.getElementsByTagName('select'),
        ...div.getElementsByTagName('textarea')
    ]

    fields.forEach(select => {
        select.hasAttribute('name') ?
        data[select.name] = select.value : ''
    })
    
    return data
}

function addAttribute(element, attribute, value) {
    element.forEach(tag => {
        tag.toggleAttribute(attribute)
    })
}