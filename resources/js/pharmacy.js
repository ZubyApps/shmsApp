import { Offcanvas, Modal } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsPharmacy";


window.addEventListener('DOMContentLoaded', function () {
    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const addResultModal            = new Modal(document.getElementById('addResultModal'))

    const treatmentDetailsBtn       = document.querySelector('#treatmentDetailsBtn')

    const treatmentDiv              = document.querySelector('#treatmentDiv')

    const textareaHeight = 90;
    textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))


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
})