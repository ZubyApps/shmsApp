import { Offcanvas, Modal, Toast } from "bootstrap";
import { clearDivValues, clearValidationErrors, getOrdinal, loadingSpinners,getDivData } from "./helpers"
import $ from 'jquery';
import http from "./http";
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations"
import { getWaitingTable, getAllRegularPatientsVisitTable, getAncPatientsVisitTable, getNurseTreatmentByConsultation, getMedicationChartByPrescription, getUpcomingMedicationsTable } from "./tables/nursesTables";
import { getVitalSignsTableByVisit, getLabTableByConsultation, getInpatientsVisitTable } from "./tables/doctorstables";

window.addEventListener('DOMContentLoaded', function () {
    const upcomingMedicationsCanvas = new Offcanvas(document.getElementById('upcomingMedicationsoffcanvas'))
    const waitingListCanvas         = new Offcanvas(document.getElementById('waitingListOffcanvas2'))

    const reviewDetailsModal        = new Modal(document.getElementById('treatmentDetailsModal'))
    const newDeliveryNoteModal      = new Modal(document.getElementById('newDeliveryNoteModal'))
    const updateDeliveryNoteModal   = new Modal(document.getElementById('updateDeliveryNoteModal'))
    const chartMedicationModal      = new Modal(document.getElementById('chartMedicationModal'))
    const vitalsignsModal           = new Modal(document.getElementById('vitalsignsModal'))
    const giveMedicationModal       = new Modal(document.getElementById('giveMedicationModal'))

    const addVitalsignsDiv          = document.querySelectorAll('#addVitalsignsDiv')
    const medicationChartDiv        = chartMedicationModal._element.querySelector('#chartMedicationDiv')
    const medicationChartTable      = chartMedicationModal._element.querySelector('#medicationChartTable')
    const giveMedicationDiv         = giveMedicationModal._element.querySelector('#giveMedicationDiv')
    const treatmentDiv              = document.querySelector('#treatmentDiv')

    const waitingBtn                = document.querySelector('#waitingBtn')
    const addVitalsignsBtn          = document.querySelectorAll('#addVitalsignsBtn')
    const saveMedicationChartBtn    = chartMedicationModal._element.querySelector('#saveMedicationChartBtn')
    const saveGivenMedicationBtn    = giveMedicationModal._element.querySelector('.saveGivenMedicationBtn')
    const [allRegularPatientsTab, inPatientsTab, ancPatientsTab]  = [document.querySelector('#nav-allRegularPatients-tab'), document.querySelector('#nav-inPatients-tab'), document.querySelector('#nav-ancPatients-tab')]
    
    const heightEl                 = document.querySelectorAll('#height') 

    heightEl.forEach(heightInput => {
        heightInput.addEventListener('input',  function (e){
            const div = heightInput.parentElement.parentElement.parentElement
            console.log(heightInput.dataset.id, div.id)
            if (heightInput.dataset.id == div.id){
                div.querySelector('#bmi').value = (div.querySelector('#weight').value.split('k')[0]/div.querySelector('#height').value.split('m')[0]**2).toFixed(2)
            }
        })
    })


    const blinkTable = document.querySelector('.thisRow')


    var colourBlink;

    // medicationCanvasTable._element.addEventListener('shown.bs.offcanvas', function () {
    //     colourBlink = setInterval(toggleClass, 500)
    // })

    // medicationCanvasTable._element.addEventListener('hidden.bs.offcanvas', function () {
    //     clearInterval(colourBlink)
    // })

    // function toggleClass() {
    //     blinkTable.classList.toggle('table-danger')
    // }
    let inPatientsVisitTable, ancPatientsVisitTable

    const allRegularPatientsTable = getAllRegularPatientsVisitTable('allRegularPatientsTable')
    const waitingTable = getWaitingTable('waitingTable')
    const upcomingMedicationsTable = getUpcomingMedicationsTable('upcomingMedicationsTable', upcomingMedicationsCanvas._element, 'offcanvas')

    allRegularPatientsTab.addEventListener('click', function() {allRegularPatientsTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getInpatientsVisitTable('#inPatientsVisitTable')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getAncPatientsVisitTable('#ancPatientsVisitTable')
        }
    })

    waitingBtn.addEventListener('click', function () {
        waitingTable.draw()
    })

    waitingListCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        allRegularPatientsTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    upcomingMedicationsCanvas._element.addEventListener('show.bs.offcanvas', function () {
        upcomingMedicationsTable.draw()
    })

    upcomingMedicationsCanvas._element.addEventListener('hide.bs.offcanvas', function () {
        upcomingMedicationsTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    document.querySelectorAll('#allRegularPatientsTable, #inPatientsVisitTable, #ancPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn = event.target.closest('.consultationDetailsBtn')
            const vitalsignsBtn = event.target.closest('.vitalSignsBtn')
    
            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
    
                const visitId = consultationDetailsBtn.getAttribute('data-id')
                const patientType = consultationDetailsBtn.getAttribute('data-patientType')
    
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
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        consultationDetailsBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
    
            if (vitalsignsBtn) {
                const tableId = '#' + vitalsignsModal._element.querySelector('.vitalsTable').id
                const visitId = vitalsignsBtn.getAttribute('data-id')
                vitalsignsModal._element.querySelector('#patient').value = vitalsignsBtn.getAttribute('data-patient')
                vitalsignsModal._element.querySelector('#sponsor').value = vitalsignsBtn.getAttribute('data-sponsor')
                vitalsignsModal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
    
                getVitalSignsTableByVisit(tableId, visitId, vitalsignsModal)
                vitalsignsModal.show()
            }
    
        })
    })

    document.querySelector('#waitingTable').addEventListener('click', function (event) {
        const vitalsignsBtn = event.target.closest('.vitalSignsBtn')
        if (vitalsignsBtn) {
            const tableId = '#' + vitalsignsModal._element.querySelector('.vitalsTable').id
            const visitId = vitalsignsBtn.getAttribute('data-id')
            vitalsignsModal._element.querySelector('#patient').value = vitalsignsBtn.getAttribute('data-patient')
            vitalsignsModal._element.querySelector('#sponsor').value = vitalsignsBtn.getAttribute('data-sponsor')
            vitalsignsModal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)

            getVitalSignsTableByVisit(tableId, visitId, vitalsignsModal)
            vitalsignsModal.show()
        }
    })

    // document.querySelector('.nurseTreatmentTable').addEventListener('click', function (event) {
    //     console.log(event)
    // })

    reviewDetailsModal._element.addEventListener('hide.bs.modal', function () {
        treatmentDiv.innerHTML = ''
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    vitalsignsModal._element.addEventListener('hide.bs.modal', function () {
        waitingTable.draw()
        inPatientsVisitTable ? inPatientsVisitTable.draw() : ''
        ancPatientsVisitTable ? ancPatientsVisitTable.draw() : ''
    })

    // manipulating all vital signs div
    addVitalsignsBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            addVitalsignsDiv.forEach(div => {
                if (div.dataset.div === addBtn.dataset.btn) {
                    addBtn.setAttribute('disabled', 'disabled')
                    const visitId = addBtn.getAttribute('data-id')
                    const tableId = div.parentNode.parentNode.querySelector('.vitalsTable').id
                    let data = { ...getDivData(div), visitId }

                    http.post('/vitalsigns', { ...data }, { "html": div })
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                new Toast(div.querySelector('#vitalSignsToast'), { delay: 2000 }).show()
                                clearDivValues(div)
                            }
                            if ($.fn.DataTable.isDataTable('#' + tableId)) {
                                $('#' + tableId).dataTable().fnDraw()
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
            const deleteBtn = event.target.closest('.deleteBtn')

            if (deleteBtn) {
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/vitalsigns/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable('#' + table.id)) {
                                    $('#' + table.id).dataTable().fnDraw()
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

    document.querySelectorAll('#medicationChartTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const deleteBtn = event.target.closest('.deleteBtn')

            if (deleteBtn) {
                deleteBtn.setAttribute('disabled', 'disabled')
                const treatmentTableId = saveMedicationChartBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/medicationchart/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                if ($.fn.DataTable.isDataTable('#' + table.id)) {
                                    $('#' + table.id).dataTable().fnDraw()
                                }
                                if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                    $('#' + treatmentTableId).dataTable().fnDraw()
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

    document.querySelector('#upcomingMedicationsTable').addEventListener('click', function (event) {
        const giveMedicationBtn = event.target.closest('#giveMedicationBtn')
        if (giveMedicationBtn) {
            saveGivenMedicationBtn.setAttribute('data-id', giveMedicationBtn.getAttribute('data-id'))
            saveGivenMedicationBtn.setAttribute('data-table', giveMedicationBtn.getAttribute('data-table'))
            giveMedicationModal._element.querySelector('#patient').value = giveMedicationBtn.getAttribute('data-patient')
            giveMedicationModal._element.querySelector('#treatment').value = giveMedicationBtn.getAttribute('data-treatment')
            giveMedicationModal._element.querySelector('#prescription').value = giveMedicationBtn.getAttribute('data-prescription')
            giveMedicationModal._element.querySelector('#dose').value = giveMedicationBtn.getAttribute('data-dose')
            giveMedicationModal.show()
        }
    })

    // review consultation loops
    document.querySelector('#treatmentDiv').addEventListener('click', function (event) {
        const collapseBtn = event.target.closest('.collapseBtn')
        const giveMedicationBtn = event.target.closest('#giveMedicationBtn')
        const chartMedicationBtn = event.target.closest('#chartMedicationBtn')
        const newDeliveryNoteBtn = event.target.closest('#newDeliveryNoteBtn')
        const updateDeliveryNoteBtn = event.target.closest('#updateDeliveryNoteBtn')
        const saveWardAndBedBtn = event.target.closest('#saveWardAndBedBtn')
        const wardAndBedDiv = document.querySelectorAll('#wardAndBedDiv')
        const deleteGivenBtn = event.target.closest('#deleteGivenBtn')

        if (collapseBtn) {
            const gotoDiv = document.querySelector(collapseBtn.getAttribute('data-goto'))
            const investigationTableId = gotoDiv.querySelector('.investigationTable').id
            const treatmentTableId = gotoDiv.querySelector('.nurseTreatmentTable').id
            const conId = gotoDiv.querySelector('.investigationTable').dataset.id
            const viewer = 'nurse'

            if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                $('#' + investigationTableId).dataTable().fnDestroy()
            }
            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                $('#' + treatmentTableId).dataTable().fnDestroy()
            }

            const goto = () => {
                location.href = collapseBtn.getAttribute('data-goto')
                window.history.replaceState({}, document.title, "/" + "nurses")
                getLabTableByConsultation(investigationTableId, conId, reviewDetailsModal._element, viewer)
                getNurseTreatmentByConsultation(treatmentTableId, conId, reviewDetailsModal._element)
            }
            setTimeout(goto, 300)
        }

        if (chartMedicationBtn) {
            const prescriptionId = chartMedicationBtn.getAttribute('data-id')
            const tableId = chartMedicationBtn.getAttribute('data-table')
            const conId = chartMedicationBtn.getAttribute('data-consultation')
            const visitId = chartMedicationBtn.getAttribute('data-visit')
            chartMedicationModal._element.querySelector('#treatment').value = chartMedicationBtn.getAttribute('data-resource')
            chartMedicationModal._element.querySelector('#prescription').value = chartMedicationBtn.getAttribute('data-prescription')
            chartMedicationModal._element.querySelector('#prescribedBy').value = chartMedicationBtn.getAttribute('data-prescribedBy')
            chartMedicationModal._element.querySelector('#prescribed').value = chartMedicationBtn.getAttribute('data-prescribed')
            saveMedicationChartBtn.setAttribute('data-id', prescriptionId)
            saveMedicationChartBtn.setAttribute('data-table', tableId)
            saveMedicationChartBtn.setAttribute('data-consultation', conId)
            saveMedicationChartBtn.setAttribute('data-visit', visitId)

            getMedicationChartByPrescription(medicationChartTable.id, prescriptionId, chartMedicationModal._element)

            chartMedicationModal.show()
        }

        if (giveMedicationBtn) {
            saveGivenMedicationBtn.setAttribute('data-id', giveMedicationBtn.getAttribute('data-id'))
            saveGivenMedicationBtn.setAttribute('data-table', giveMedicationBtn.getAttribute('data-table'))
            giveMedicationModal._element.querySelector('#patient').value = giveMedicationBtn.getAttribute('data-patient')
            giveMedicationModal._element.querySelector('#treatment').value = giveMedicationBtn.getAttribute('data-treatment')
            giveMedicationModal._element.querySelector('#prescription').value = giveMedicationBtn.getAttribute('data-prescription')
            giveMedicationModal._element.querySelector('#dose').value = giveMedicationBtn.getAttribute('data-dose')
            giveMedicationModal.show()
        }

        if (deleteGivenBtn){
            deleteGivenBtn.setAttribute('disabled', 'disabled')
            const treatmentTableId = deleteGivenBtn.getAttribute('data-table')
            if (confirm('Are you sure you want to delete this Information?')) {
                const id = deleteGivenBtn.getAttribute('data-id')
                http.patch(`/medicationchart/removegiven/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            // if ($.fn.DataTable.isDataTable('#' + table.id)) {
                            //     $('#' + table.id).dataTable().fnDraw()
                            // }
                            if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                                $('#' + treatmentTableId).dataTable().fnDraw()
                            }
                        }
                        deleteGivenBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteGivenBtn.removeAttribute('disabled')
                    })
            }
        }

        if (newDeliveryNoteBtn) {
            newDeliveryNoteModal.show()
        }

        if (updateDeliveryNoteBtn) {
            updateDeliveryNoteModal.show()
        }

        if (saveWardAndBedBtn) {
            wardAndBedDiv.forEach(div => {
                if (div.dataset.div === saveWardAndBedBtn.dataset.btn) {
                    saveWardAndBedBtn.setAttribute('disabled', 'disabled')
                    const conId = saveWardAndBedBtn.dataset.id

                    http.post(`consultation/${conId}`, { ...getDivData(div) }, { "html": div })
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300) {
                                new Toast(div.querySelector('#saveUpdateAdmissionStatusToast'), { delay: 2000 }).show()
                                clearDivValues(div)
                                clearValidationErrors(div)
                            }
                            saveWardAndBedBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            console.log(error)
                            saveWardAndBedBtn.removeAttribute('disabled')
                        })
                }
            })
        }
    })

    saveMedicationChartBtn.addEventListener('click', function () {
        const prescriptionId = saveMedicationChartBtn.getAttribute('data-id')
        const treatmentTableId = saveMedicationChartBtn.getAttribute('data-table')
        const conId = saveMedicationChartBtn.getAttribute('data-consultation')
        const visitId = saveMedicationChartBtn.getAttribute('data-visit')

        saveMedicationChartBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(medicationChartDiv), prescriptionId, conId, visitId }

        http.post('/medicationchart', { ...data }, { "html": medicationChartDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {
                    new Toast(medicationChartDiv.querySelector('#saveMedicationChartToast'), { delay: 2000 }).show()

                    clearDivValues(medicationChartDiv)
                    clearValidationErrors(medicationChartDiv)

                    if ($.fn.DataTable.isDataTable('#' + medicationChartTable.id)) {
                        $('#' + medicationChartTable.id).dataTable().fnDraw()
                    }

                    if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                        $('#' + treatmentTableId).dataTable().fnDraw()
                    }

                }
                saveMedicationChartBtn.removeAttribute('disabled')
            })
            .catch((error) => {
                console.log(error)
                saveMedicationChartBtn.removeAttribute('disabled')
            })
    })

    saveGivenMedicationBtn.addEventListener('click', function () {
        const medicationChartId = saveGivenMedicationBtn.getAttribute('data-id')
        const treatmentTableId = saveGivenMedicationBtn.getAttribute('data-table')
        saveGivenMedicationBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(giveMedicationDiv), medicationChartId }

        http.patch(`/medicationchart/${medicationChartId}`, { ...data }, { "html": giveMedicationDiv })
            .then((response) => {
                if (response.status >= 200 || response.status <= 300) {

                    clearDivValues(giveMedicationDiv)
                    clearValidationErrors(giveMedicationDiv)

                    if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                        $('#' + treatmentTableId).dataTable().fnDraw()
                    }
                }
                saveGivenMedicationBtn.removeAttribute('disabled')
                giveMedicationModal.hide()
            })
            .catch((error) => {
                console.log(error)
                saveGivenMedicationBtn.removeAttribute('disabled')
            })
    })

    reviewDetailsModal._element.addEventListener('hidden.bs.modal', function () {
        allRegularPatientsTable.draw()
    })
})

function openNurseModals(modal, button, { id, visitId, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}
