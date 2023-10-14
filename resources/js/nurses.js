import { Offcanvas, Modal } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsNurses";


window.addEventListener('DOMContentLoaded', function () {
    const medicationCanvasTable = new Offcanvas(document.getElementById('offcanvasWithBothOptions'))

    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const newDeliveryNoteModal      = new Modal(document.getElementById('newDeliveryNoteModal'))
    const updateDeliveryNoteModal   = new Modal(document.getElementById('updateDeliveryNoteModal'))
    const chartMedicationModal      = new Modal(document.getElementById('chartMedicationModal'))
    
    const saveMedicationChartBtn    = chartMedicationModal._element.querySelector('#saveMedicationChartBtn')
    const medicationChartDiv        = chartMedicationModal._element.querySelector('#chartMedicationDiv')

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


    detailsBtn.addEventListener('click', function () {

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

    // review consultation loops
    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const addInvestigationBtn                   = event.target.closest('#addInvestigationBtn')
        const addVitalsignsBtn                      = event.target.closest('#addVitalsignsBtn')
        const saveInvestigationBtn                  = event.target.closest('#saveInvestigationBtn')
        const chartMedicationBtn                    = event.target.closest('#chartMedicationBtn')
        const newDeliveryNoteBtn                    = event.target.closest('#newDeliveryNoteBtn')
        const updateDeliveryNoteBtn                 = event.target.closest('#updateDeliveryNoteBtn')
        const saveWardAndBedBtn                     = event.target.closest('#saveWardAndBedBtn')
        const wardAndBedDiv                         = document.querySelectorAll('#wardAndBedDiv')
        const addVitalsignsDiv                      = document.querySelectorAll('#addVitalsignsDiv')
        const investigationDiv                      = document.querySelectorAll('.investigationDiv')

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

        if (addVitalsignsBtn) {
            addVitalsignsDiv.forEach(div => {
                if (div.dataset.div === addVitalsignsBtn.dataset.btn) {
                    if (div.classList.contains('d-none')){
                        div.classList.remove('d-none')
                    } else {
                        console.log(getDivData(div))
                        div.classList.add('d-none')
                        clearDivValues(div)
                    }
                }
            })
        }

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

// for (let name in line) {
                    
                //     if (typeof line[name] === 'object'){
                //         for (let name1 in line[name]) {
                //             //console.log(name1)
                //             const nameInput = treatmentDiv.querySelector(`[name="${ name1 }"]`)
                //             nameInput.value = line[name][name1]
                //             console.log(line[name][name1])
                //         }
                //     } else {
    
                //         const nameInput = treatmentDiv.querySelector(`[name="${ name }"]`)
                //         nameInput.value = line[name]
                //     }
                // }
