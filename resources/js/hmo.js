import { Offcanvas, Modal } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsHmo";


window.addEventListener('DOMContentLoaded', function () {
    const medicationCanvasTable     = new Offcanvas(document.getElementById('offcanvasWithBothOptions'))
    const medicationCanvasTable2    = new Offcanvas(document.getElementById('offcanvasWithBothOptions2'))
    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const approvalModal             = new Modal(document.getElementById('approvalModal'))

    const verifyModal               = new Modal(document.getElementById('verifyModal'))
    const codeTextDiv               = verifyModal._element.querySelector('#codeTextDiv')
    const verifyBtn                 = document.querySelector('#verifyBtn')
    const verifyPatientBtn          = document.querySelector('#verifyPatientBtn')
    const treatmentDetailsBtn       = document.querySelector('#treatmentDetailsBtn')
    const approvalBtn               = document.querySelector('#approvalBtn')
    const approveBtn                = document.querySelector('#approveBtn')
    const approveDiv                = document.querySelector('#approveDiv')

    const treatmentDiv              = document.querySelector('#treatmentDiv')


    verifyBtn.addEventListener('click', function  () {
        verifyModal.show()
    })

    verifyPatientBtn.addEventListener('click', function () {
        codeTextDiv.querySelector('#status').value === '' ? 
        alert(`Please fill the STATUS field atleast`) : 
        (console.log(getDivData(codeTextDiv)), verifyModal.hide(), clearDivValues(codeTextDiv))
        
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

    approvalBtn.addEventListener('click', function () {
        approvalModal.show()
    })

    approveBtn.addEventListener('click', function (){
        console.log(getDivData(approveDiv))
        approvalModal.hide()
        clearDivValues(approveDiv)
    })

    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const approvalBtn           = event.target.closest('#approvalBtn')

        if (approvalBtn) {
            approvalModal.show()
        }
    })

})