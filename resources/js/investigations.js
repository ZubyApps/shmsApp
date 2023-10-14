import { Offcanvas, Modal } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsInvestigations";


window.addEventListener('DOMContentLoaded', function () {
    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const addResultModal            = new Modal(document.getElementById('addResultModal'))

    const treatmentDetailsBtn       = document.querySelector('#treatmentDetailsBtn')

    const treatmentDiv              = document.querySelector('#treatmentDiv')
    const resultDiv                 = document.querySelector('#resultDiv')

    const addResultBtn              = document.querySelectorAll('#addResultBtn')
    const saveResultBtn             = document.querySelector('#saveResultBtn')

     // Auto textarea adjustment
     const textareaHeight = 90;
     textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))

    addResultBtn.forEach(btn => {
        btn.addEventListener('click', function() {
            addResultModal.show()
        })
    })

    treatmentDetailsBtn.addEventListener('click', function () {

        let iteration   = 0
        let count       = 0
        consultationDetails.data.forEach(line => {
            iteration++

            if (iteration > 1) {
                count++
                treatmentDiv.innerHTML += review(iteration, getOrdinal, count, consultationDetails, line)
            } else {
                treatmentDiv.innerHTML += InitialRegularConsultation(iteration, consultationDetails, line)
            } 
             
            //treatmentDiv.querySelector('#deliveryBtn').setAttribute('data-id', line.deliveryNote.id ?? '')
        })


        reviewDetailsModal.show()
    })

    reviewDetailsModal._element.addEventListener('hide.bs.modal', function () {
        treatmentDiv.innerHTML = ''
    })

    

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const addResultBtn           = event.target.closest('#addResultBtn')

        if (addResultBtn) {
            addResultModal.show()
        }
    })

    saveResultBtn.addEventListener('click', function () {
        console.log(getDivData(resultDiv))
    })
})