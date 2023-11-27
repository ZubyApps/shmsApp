import { Offcanvas, Modal, Toast } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData} from "./helpers"
import $ from 'jquery';
// import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsNurses";
import http from "./http";
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import { getWaitingTable, getAllPatientsVisitTable, getNurseTreatmentByConsultation} from "./tables/nursesTables";
import { getVitalSignsTableByVisit, getLabTableByConsultation} from "./tables/doctorstables";


window.addEventListener('DOMContentLoaded', function () {
    const medicationCanvasTable     = new Offcanvas(document.getElementById('offcanvasWithBothOptions'))

    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const newDeliveryNoteModal      = new Modal(document.getElementById('newDeliveryNoteModal'))
    const updateDeliveryNoteModal   = new Modal(document.getElementById('updateDeliveryNoteModal'))
    const chartMedicationModal      = new Modal(document.getElementById('chartMedicationModal'))
    const vitalsignsModal           = new Modal(document.getElementById('vitalsignsModal'))

    const addVitalsignsDiv          = document.querySelectorAll('#addVitalsignsDiv')
    const medicationChartDiv        = chartMedicationModal._element.querySelector('#chartMedicationDiv')
    
    const waitingBtn                = document.querySelector('#waitingBtn')
    const addVitalsignsBtn          = document.querySelectorAll('#addVitalsignsBtn')
    const saveMedicationChartBtn    = chartMedicationModal._element.querySelector('#saveMedicationChartBtn')
    

    const blinkTable                = document.querySelector('.thisRow')

    const detailsBtn                = document.querySelector('.detailsBtn')

    const treatmentDiv              = document.querySelector('#treatmentDiv')
    
    var colourBlink;
    
    medicationCanvasTable._element.addEventListener('shown.bs.offcanvas', function () {
        colourBlink = setInterval(toggleClass, 500)
    })

    medicationCanvasTable._element.addEventListener('hidden.bs.offcanvas', function () {
        clearInterval(colourBlink)
    })

    function toggleClass () {
        blinkTable.classList.toggle('table-danger')
    }

    const allPatientsTable = getAllPatientsVisitTable('#allPatientsTable')
    const waitingTable = getWaitingTable('#waitingTable')

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
        allPatientsTable.draw()
    })

    document.querySelector('#allPatientsTable').addEventListener('click', function (event) {
        const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
        const vitalsignsBtn             = event.target.closest('.vitalSignsBtn')

        if (consultationDetailsBtn) {
            consultationDetailsBtn.setAttribute('disabled', 'disabled')

            const visitId       = consultationDetailsBtn.getAttribute('data-id')
            const patientType   = consultationDetailsBtn.getAttribute('data-patientType')

            http.get(`/consultation/consultations/${visitId}`)
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    let iteration = 0
                    let count = 0

                const consultations = response.data.consultations.data
                const patientBio = response.data.bio

                openNurseModals(reviewDetailsModal, treatmentDiv, patientBio)

                chartMedicationModal._element.querySelector('#patient').value = patientBio.patientId
                chartMedicationModal._element.querySelector('#sponsor').value = patientBio.sponsorName

                const viewer = 'nurse'
                consultations.forEach(line => {
                    iteration++
                    
                    iteration > 1 ? count++ : ''
        
                    if (patientType === 'ANC') {
                        treatmentDiv.innerHTML += AncPatientReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                    } else {
                        treatmentDiv.innerHTML += regularReviewDetails(iteration, getOrdinal, count, consultations.length, line, viewer)
                    }
                })

                getVitalSignsTableByVisit('#vitalSignsTableNurses', visitId, reviewDetailsModal)
                reviewDetailsModal.show()

                }
                consultationDetailsBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                consultationDetailsBtn.removeAttribute('disabled')
                alert(error)
                console.log(error)
            })
        }
        
        if (vitalsignsBtn) {
            const tableId = '#'+vitalsignsModal._element.querySelector('.vitalsTable').id
            const visitId = vitalsignsBtn.getAttribute('data-id')
            vitalsignsModal._element.querySelector('#patient').value = vitalsignsBtn.getAttribute('data-patient')
            vitalsignsModal._element.querySelector('#sponsor').value = vitalsignsBtn.getAttribute('data-sponsor')
            vitalsignsModal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)

            getVitalSignsTableByVisit(tableId, visitId, vitalsignsModal)
            vitalsignsModal.show()
        }

    })

    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const vitalsignsBtn = event.target.closest('.vitalSignsBtn')
        if (vitalsignsBtn) {
            const tableId = '#'+vitalsignsModal._element.querySelector('.vitalsTable').id
            const visitId = vitalsignsBtn.getAttribute('data-id')
            vitalsignsModal._element.querySelector('#patient').value = vitalsignsBtn.getAttribute('data-patient')
            vitalsignsModal._element.querySelector('#sponsor').value = vitalsignsBtn.getAttribute('data-sponsor')
            vitalsignsModal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)

            getVitalSignsTableByVisit(tableId, visitId, vitalsignsModal)
            vitalsignsModal.show()
        }
    })

    document.querySelector('.nurseTreatmentTable').addEventListener('click', function (event) {
        console.log(event)
    })    

    reviewDetailsModal._element.addEventListener('hide.bs.modal', function () {
        treatmentDiv.innerHTML = ''
    })

    vitalsignsModal._element.addEventListener('hide.bs.modal', function() {
        waitingTable.draw()
        allPatientsTable.draw()
    })

     // manipulating all vital signs div
    addVitalsignsBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            addVitalsignsDiv.forEach(div => {
                if (div.dataset.div === addBtn.dataset.btn) {
                    addBtn.setAttribute('disabled', 'disabled')
                    const visitId = addBtn.getAttribute('data-id')
                    const tableId = div.parentNode.parentNode.querySelector('.vitalsTable').id
                    let data = {...getDivData(div), visitId}

                    http.post('/vitalsigns', {...data}, {"html": div})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            new Toast(div.querySelector('#vitalSignsToast'), {delay:2000}).show()
                            clearDivValues(div)
                        }
                        if ($.fn.DataTable.isDataTable( '#'+tableId )){
                            $('#'+tableId).dataTable().fnDraw()
                        }
                        addBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        console.log(error)
                        addBtn.removeAttribute('disabled')
                    })                   
                }
            })
        })
    })

    document.querySelectorAll('#vitalSignsTableNurses, #vitalSignsTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn  = event.target.closest('.deleteBtn')
    
            if (deleteBtn){
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/vitalsigns/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                if ($.fn.DataTable.isDataTable( '#'+table.id )){
                                $('#'+table.id).dataTable().fnDraw()
                            }
                            }
                            deleteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteBtn.removeAttribute('disabled')
                        })
                }
                
            }
        })
    })

    // review consultation loops
    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseBtn                           = event.target.closest('.collapseBtn')
        const addInvestigationBtn                   = event.target.closest('#addInvestigationBtn')
        // const addVitalsignsBtn                      = event.target.closest('#addVitalsignsBtn')
        const saveInvestigationBtn                  = event.target.closest('#saveInvestigationBtn')
        const chartMedicationBtn                    = event.target.closest('#chartMedicationBtn')
        const newDeliveryNoteBtn                    = event.target.closest('#newDeliveryNoteBtn')
        const updateDeliveryNoteBtn                 = event.target.closest('#updateDeliveryNoteBtn')
        const saveWardAndBedBtn                     = event.target.closest('#saveWardAndBedBtn')
        const wardAndBedDiv                         = document.querySelectorAll('#wardAndBedDiv')
        const addVitalsignsDiv                      = document.querySelectorAll('#addVitalsignsDiv')
        const investigationDiv                      = document.querySelectorAll('.investigationDiv')

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const investigationTableId  = gotoDiv.querySelector('.investigationTable').id
            const treatmentTableId      = gotoDiv.querySelector('.nurseTreatmentTable').id
            const conId                 = gotoDiv.querySelector('.investigationTable').dataset.id
            const viewer                = 'nurse'
            if ($.fn.DataTable.isDataTable( '#'+investigationTableId )){
                $('#'+investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable( '#'+treatmentTableId )){
                $('#'+treatmentTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "nurses" )
                getLabTableByConsultation(investigationTableId, conId, reviewDetailsModal._element, viewer)
                getNurseTreatmentByConsultation(treatmentTableId, conId, reviewDetailsModal._element)
            }
            setTimeout(goto, 300)
        }

        if (addInvestigationBtn) {
            investigationDiv.forEach(div => {
                if (div.dataset.div === addInvestigationBtn.dataset.btn) {
                    div.classList.toggle('d-none')
                    getItemsFromInput(div.querySelector('#item'), items)
                }
                
            })
        }

        if (saveInvestigationBtn) {
            investigationDiv.forEach(div => {
                if (div.dataset.div === saveInvestigationBtn.dataset.btn) {
                console.log(getDivData(div))
                clearDivValues(div)
                }
            })
        }

        if (chartMedicationBtn) {
            chartMedicationModal.show()
        }

        if (newDeliveryNoteBtn) {
            newDeliveryNoteModal.show()
        }

        if (updateDeliveryNoteBtn) {
            updateDeliveryNoteModal.show()
        }

        // if (addVitalsignsBtn) {
        //     console.log(addVitalsignsBtn)
        //     addVitalsignsDiv.forEach(div => {
        //         if (div.dataset.div === addVitalsignsBtn.dataset.btn) {
        //             if (div.classList.contains('d-none')){
        //                 div.classList.remove('d-none')
        //             } else {
        //                 console.log(getDivData(div))
        //                 div.classList.add('d-none')
        //                 clearDivValues(div)
        //             }
        //         }
        //     })
        // }

        if (saveWardAndBedBtn) {
            wardAndBedDiv.forEach(div => {
                if (div.dataset.div === saveWardAndBedBtn.dataset.btn) {
                console.log(getDivData(div))
                clearDivValues(div)
                }
            })
        }
    })

    saveMedicationChartBtn.addEventListener('click', function () {
        console.log(getDivData(medicationChartDiv))
        clearDivValues(medicationChartDiv)
    }) 
})

function getItemsFromInput(input, data) {
    input.addEventListener('keyup', function() {
        let records = data.filter(d => d.name.toLocaleLowerCase().includes(input.value.toLocaleLowerCase()) ? d : '')
        displayItemsList(input.parentNode, records)
    })
}

function displayItemsList(div, data) {

    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', 'itemsOption')
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)

        let previousItems = div.querySelectorAll('#itemsOption')
            let optionsList = []
            previousItems.forEach(node => {
               optionsList.push(node.dataset.id)
            })
            div.querySelector('#item').setAttribute('list', 'itemsList')
            div.querySelector('datalist').setAttribute('id', 'itemsList')
            !optionsList.includes(option.dataset.id) ? div.querySelector('#itemsList').appendChild(option) : ''
        })
}

function openNurseModals(modal, button, {id,visitId, ...data}) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        
        nameInput.value = data[name]
    }
    
    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)

    // button.setAttribute('data-id', id)
    // modal.show()
}
