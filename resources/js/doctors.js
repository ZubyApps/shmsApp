import { Modal } from "bootstrap"
import * as ECT from '@whoicd/icd11ect'
import '@whoicd/icd11ect/style.css'

window.addEventListener('DOMContentLoaded', function(){
    const newConsultationModal             = new Modal(document.getElementById('newConsultationModal'))

    const consultBtn                        = document.querySelector('.consultBtn')
    const knownConditionsInput              = document.querySelector('.knownConditions')

    const saveBtn                           = document.querySelector('#saveBtn')
    const addKnownClinicalInfoBtn           = document.querySelector('#addKnownClincalInfoBtn')
    const addVitalsignsBtn                  = document.querySelector('#addVitalsignsBtn')

    const knownClinicanInfoDiv              = document.querySelector('.knownClinicalInfoDiv')
    const addVitalsignsDiv                  = document.querySelector('.addVitalsignsDiv')

    const diagnosisInput                    = document.querySelector('.diagnosis')

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

    saveBtn.addEventListener('click', function() {
        console.log(getConsultationFormData(newConsultationModal))
    })
      
      // ICD11 handler
    ECT.Handler.configure(mySettings, myCallbacks)

    addKnownClinicalInfoBtn.addEventListener('click', function () {
        const tagName = knownClinicanInfoDiv.querySelectorAll('input, select, textarea')
            tagName.forEach(tag => {
                tag.toggleAttribute('disabled')
            })
        addKnownClinicalInfoBtn.textContent === "Done" ? addKnownClinicalInfoBtn.innerHTML = `<i class="bi bi-wrench-adjustable"></i>
        Change` : addKnownClinicalInfoBtn.textContent = "Done"
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