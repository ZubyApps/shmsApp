import { Modal } from "bootstrap"

window.addEventListener('DOMContentLoaded', function(){
    const newConsolutationModal             = new Modal(document.getElementById('newConsultationModal'))

    const consultBtn                        = document.querySelector('.consultBtn')
    const knownConditionsInput               = document.querySelector('.knownConditions')

    consultBtn.addEventListener('click', function() {
        newConsolutationModal.show()
    })

    knownConditionsInput.addEventListener('dblClick', function() {
        knownConditionsInput.removeAttribute('readonly')
    })
})