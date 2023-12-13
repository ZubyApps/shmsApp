import { Offcanvas, Modal } from "bootstrap";
import http from "./http";
import { clearDivValues, clearItemsList, getOrdinal, getDivData, clearValidationErrors} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsHmo";
import { getVerificationTable, getWaitingTable } from "./tables/hmoTables";


window.addEventListener('DOMContentLoaded', function () {
    // const medicationCanvasTable     = new Offcanvas(document.getElementById('offcanvasWithBothOptions'))
    // const medicationCanvasTable2    = new Offcanvas(document.getElementById('offcanvasWithBothOptions2'))
    const waitingListCanvas         = new Offcanvas(document.getElementById('waitingListOffcanvas2'))
    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const approvalModal             = new Modal(document.getElementById('approvalModal'))
    const verifyModal               = new Modal(document.getElementById('verifyModal'))

    const waitingBtn                = document.querySelector('#waitingBtn')
    const codeTextDiv               = verifyModal._element.querySelector('#codeTextDiv')
    const verifyBtn                 = verifyModal._element.querySelector('#verifyBtn')
    // const verifyPatientBtn          = document.querySelector('#verifyPatientBtn')
    const treatmentDetailsBtn       = document.querySelector('#treatmentDetailsBtn')
    const approvalBtn               = document.querySelector('#approvalBtn')
    const approveBtn                = document.querySelector('#approveBtn')
    const approveDiv                = document.querySelector('#approveDiv')

    const treatmentDiv              = document.querySelector('#treatmentDiv')


    const waitingTable = getWaitingTable('waitingTable')
    const verificationTable = getVerificationTable('verificationTable')

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    // verifyBtn.addEventListener('click', function  () {
    //     verifyModal.show()
    // })

    // verifyPatientBtn.addEventListener('click', function () {
    //     codeTextDiv.querySelector('#status').value === '' ? 
    //     alert(`Please fill the STATUS field atleast`) : 
    //     (console.log(getDivData(codeTextDiv)), verifyModal.hide(), clearDivValues(codeTextDiv))
        
    // })

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

    document.querySelector('#verificationTable').addEventListener('click', function (event) {
        const verifyPatientBtn = event.target.closest('.verifyPatientBtn')

        if (verifyPatientBtn) {
            verifyBtn.setAttribute('data-id', verifyPatientBtn.getAttribute('data-id'))
            verifyBtn.setAttribute('data-table', verifyPatientBtn.getAttribute('data-table'))
            verifyModal._element.querySelector('#patientId').value = verifyPatientBtn.getAttribute('data-patient')
            verifyModal._element.querySelector('#sponsorName').value = verifyPatientBtn.getAttribute('data-sponsor')
            verifyModal._element.querySelector('#staffId').value = verifyPatientBtn.getAttribute('data-staffid')
            verifyModal._element.querySelector('#phoneNumber').value = verifyPatientBtn.getAttribute('data-phone')
            verifyModal.show()
        }
    })

    verifyBtn.addEventListener('click', function () {
        verifyBtn.setAttribute('disabled', 'disabled')
        const visitId = verifyBtn.getAttribute('data-id')

        let data = { ...getDivData(codeTextDiv), visitId }

        http.post(`/visits/verify/${visitId}`,  { ...data }, { "html": codeTextDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {

                clearDivValues(codeTextDiv)
                clearValidationErrors(codeTextDiv)

            }
            verifyBtn.removeAttribute('disabled')
            verifyModal.hide()
        })
        .catch((error) => {
            console.log(error)
            verifyBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const removeBtn  = event.target.closest('.removeBtn')

        if (removeBtn){
            removeBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Visit?')) {
                const visitId = removeBtn.getAttribute('data-id')
                http.delete(`/visits/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        waitingTable.draw()
                    }
                    removeBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    console.log(error)
                    removeBtn.removeAttribute('disabled')
                })
            }  
        }
    
    })

    reviewDetailsModal._element.addEventListener('hide.bs.modal', function () {
        treatmentDiv.innerHTML = ''
    })

    verifyModal._element.addEventListener('hide.bs.modal', function () {
        verificationTable.draw()
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        verificationTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
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