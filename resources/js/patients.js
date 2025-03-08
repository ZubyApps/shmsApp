import { Modal } from "bootstrap"
import { getDivData, clearDivValues, clearValidationErrors, displayList, openModals, getPatientSponsorDatalistOptionId, getOrdinal, displayItemsList, getDatalistOptionId } from "./helpers"
import http from "./http"
import $ from 'jquery';
import { getAgeAggregateTable, getAllPatientsTable, getNewRegisteredPatientsTable, getPatientsBySponsorTable, getPrePatientsTable, getSexAggregateTable, getSponsorsTable, getVisitsSummaryTable, getVisitsTable } from "./tables/patientsTables";
import { AncPatientReviewDetails, regularReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getAncVitalSignsTable } from "./tables/nursesTables";
import { getLabTableByConsultation, getMedicationsByFilter, getOtherPrescriptionsByFilter, getVitalSignsTableByVisit } from "./tables/doctorstables";
import { getAppointmentsTable } from "./tables/appointmentsTables";
$.fn.dataTable.ext.errMode = 'throw';

window.addEventListener('DOMContentLoaded', function(){
    const newSponsorModal                   = new Modal(document.getElementById('newSponsorModal'))
    const updateSponsorModal                = new Modal(document.getElementById('updateSponsorModal'))
    const newPatientModal                   = new Modal(document.getElementById('newPatientModal'))
    const updatePatientModal                = new Modal(document.getElementById('updatePatientModal'))
    const initiatePatientModal              = new Modal(document.getElementById('initiatePatientModal'))
    const patientsBySponsorModal            = new Modal(document.getElementById('patientsBySponsorModal'))
    const treatmentDetailsModal             = new Modal(document.getElementById('treatmentDetailsModal'))
    const ancTreatmentDetailsModal          = new Modal(document.getElementById('ancTreatmentDetailsModal'))
    const appointmentModal                  = new Modal(document.getElementById('appointmentModal'))
    const sponsorTariffModal                = new Modal(document.getElementById('sponsorTariffModal'))

    const regularTreatmentDiv               = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv                   = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')
    const datesDiv                          = document.querySelector('.datesDiv')
    const newRegisterationsDiv              = document.querySelector('.newRegisterationsDiv')
    const visistSummaryDiv                  = document.querySelector('.visistSummaryDiv')
    const appointmentDetailsDiv             = appointmentModal._element.querySelector('#appointmentDetails')

    const newSponsorBtn                     = document.getElementById('newSponsor')
    const createSponsorBtn                  = document.querySelector('#createSponsorBtn')
    const saveSponsorBtn                    = document.querySelector('#saveSponsorBtn')
    const newPatientBtn                     = document.getElementById('newPatient')
    const registerPatientBtn                = document.querySelector('#registerPatientBtn')
    const savePatientBtn                    = document.querySelector('#savePatientBtn')
    const confirmVisitBtn                   = document.querySelector('#confirmVisitBtn')
    const searchNewRegPatientsByMonthBtn    = document.querySelector('.searchNewRegPatientsByMonthBtn')
    const searchVisitsByMonthBtn            = document.querySelector('.searchVisitsByMonthBtn')
    const sendLinkBtn                       = document.querySelector('#sendLinkBtn')
    const saveAppointmentBtn                = document.querySelector('#saveAppointmentBtn')
    const saveSellingPriceBtn               = sponsorTariffModal._element.querySelector('#saveSellPriceBtn')
    
    const newPatientSponsorInputEl          = document.querySelector('#newPatientSponsor')
    const updatePatientSponsorInputEl       = document.querySelector('#updatePatientSponsor')

    const newPatientSponsorDatalistEl       = document.querySelector('#newSponsorList')
    const updatePatientSponsorDatalistEl    = document.querySelector('#updateSponsorList')

    const resourceInput                     = sponsorTariffModal._element.querySelector('#resource')

    const searchVisitsWithDatesBtn          = document.querySelector('.searchVisitsWithDatesBtn')

    const patientsTab                       = document.querySelector('#nav-patients-tab')
    const sponsorsTab                       = document.querySelector('#nav-sponsors-tab')
    const visitsTab                         = document.querySelector('#nav-visits-tab')
    const summariesTab                      = document.querySelector('#nav-summaries-tab')
    const prePatientsTab                    = document.querySelector('#nav-prePatients-tab')
    const appointmentsTab                   = document.querySelector('#nav-appointments-tab')

    let sponsorsTable, visitsTable, newRegPatientsTable, sexAggregateTable, patientsBySponsorTable, visitsSummaryTable, prePatientsTable, appointmentsTable

    const allPatientsTable = getAllPatientsTable('#allPatientsTable')
    $('#allPatientsTable, #sponsorsTable, #visitsTable, #newRegPatientsTable, #sexAggregateTable, #patientsBySponsorTable, #visitsSummaryTable').on('error.dt', function(e, settings, techNote, message) {techNote == 7 ? window.location.reload() : ''})
    newSponsorBtn.addEventListener('click', function() {
        newSponsorModal.show()
    })

    newPatientBtn.addEventListener('click', function() {
        let date = new Date().toISOString().split('T')[0]
        newPatientModal._element.querySelector('[name="dateOfBirth"]').setAttribute('max', date)
        newPatientModal.show()
    })

    patientsTab.addEventListener('click', function() {
        allPatientsTable.draw()
    })

    sponsorsTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#sponsorsTable' )){
            $('#sponsorsTable').dataTable().fnDraw()
        } else {
            sponsorsTable = getSponsorsTable('sponsorsTable')
        }
    })

    visitsTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#visitsTable' )){
            $('#visitsTable').dataTable().fnDraw()
        } else {
            visitsTable = getVisitsTable('visitsTable')
        }
    })

    appointmentsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#appointmentsTable' )){
            $('#appointmentsTable').dataTable().fnDraw()
        } else {
            appointmentsTable = getAppointmentsTable('appointmentsTable')
        }
    })

    summariesTab.addEventListener('click', function() {
        newRegisterationsDiv.querySelector('#regMonth').value == '' ? newRegisterationsDiv.querySelector('#regMonth').value = new Date().toISOString().slice(0,7) : ''
        visistSummaryDiv.querySelector('#visitSummaryMonth').value == '' ? visistSummaryDiv.querySelector('#visitSummaryMonth').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#newRegPatientsTable' )){
            $('#newRegPatientsTable').dataTable().fnDraw()
        } else {
            newRegPatientsTable = getNewRegisteredPatientsTable('newRegPatientsTable')
        }
        if ($.fn.DataTable.isDataTable( '#sexAggregateTable' )){
            $('#sexAggregateTable').dataTable().fnDraw()
        } else {
            sexAggregateTable = getSexAggregateTable('sexAggregateTable')
        }
        if ($.fn.DataTable.isDataTable( '#ageAggregateTable' )){
            $('#ageAggregateTable').dataTable().fnDraw()
        } else {
            patientsBySponsorTable = getAgeAggregateTable('ageAggregateTable')
        }
        if ($.fn.DataTable.isDataTable( '#visitsSummaryTable' )){
            $('#visitsSummaryTable').dataTable().fnDraw()
        } else {
            visitsSummaryTable = getVisitsSummaryTable('visitsSummaryTable')
        }
    })

    searchNewRegPatientsByMonthBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#newRegPatientsTable' )){
            $('#newRegPatientsTable').dataTable().fnDestroy()
        }
        newRegPatientsTable = getNewRegisteredPatientsTable('newRegPatientsTable', newRegisterationsDiv.querySelector('#regMonth').value)
    })

    searchVisitsByMonthBtn.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#visitsSummaryTable' )){
            $('#visitsSummaryTable').dataTable().fnDestroy()
        }
        visitsSummaryTable = getVisitsSummaryTable('visitsSummaryTable', visistSummaryDiv.querySelector('#visitSummaryMonth').value)
    })

    prePatientsTab.addEventListener('click', function() {
        if ($.fn.DataTable.isDataTable( '#prePatientsTable' )){
            $('#prePatientsTable').dataTable().fnDraw()
        } else {
            prePatientsTable = getPrePatientsTable('prePatientsTable')
        }
    })

    document.querySelector('#sponsorsTable').addEventListener('click', function (event) {
        const editBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')
        const sponsorTariffBtn  = event.target.closest('.sponsorTariffBtn')
        const deleteTariffBtn  = event.target.closest('.deleteTariffBtn')

        if (editBtn) {
            editBtn.setAttribute('disabled', 'disabled')
            const sponsorId = editBtn.getAttribute('data-id')
            http.get(`/sponsors/${ sponsorId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateSponsorModal, saveSponsorBtn, response.data.data)
                    }
                    editBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error.response.data.data.message)
                })
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Sponsor?')) {
                const sponsorId = deleteBtn.getAttribute('data-id')
                http.delete(`/sponsors/${sponsorId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            sponsorsTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            } 
        }

        if (sponsorTariffBtn){
            const sponsorId = sponsorTariffBtn.getAttribute('data-id')
            const sponsor = sponsorTariffBtn.getAttribute('data-sponsor')

            sponsorTariffModal._element.querySelector('#sponsorName').value = sponsor
            saveSellingPriceBtn.setAttribute('data-id', sponsorId)
            sponsorTariffModal.show()
        }

        if (deleteTariffBtn){
            const resourceId = deleteTariffBtn.getAttribute('data-id')
            const sponsorId = deleteTariffBtn.getAttribute('data-sponsor')

            deleteTariffBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Tariff?')) {
                http.delete(`/resources/remove/sellingprice/${sponsorId}/${resourceId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            sponsorsTable.draw()
                        }
                        deleteTariffBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        deleteTariffBtn.removeAttribute('disabled')
                        alert(error)
                    })
            }
        }
    })

    resourceInput.addEventListener('input', function () {
        const datalistEl = sponsorTariffModal._element.querySelector(`#resourceList`)
            if (resourceInput.value <= 2) {
            datalistEl.innerHTML = ''
            }
            if (resourceInput.value.length > 2) {
                http.get(`/resources/list2`, {params: {resource: resourceInput.value}}).then((response) => {
                    displayItemsList(datalistEl, response.data, 'itemOption')
                })
            }
    })

    saveSellingPriceBtn.addEventListener('click', function () {
        const sponsorId = this.getAttribute('data-id')
        const resourceId = getDatalistOptionId(sponsorTariffModal._element, resourceInput, sponsorTariffModal._element.querySelector(`#resourceList`))
        saveSellingPriceBtn.setAttribute('disabled', 'disabled')
        http.post(`/resources/sellingprice/${sponsorId}/${resourceId}`, getDivData(sponsorTariffModal._element), {"html": sponsorTariffModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                clearDivValues(sponsorTariffModal._element)
                sponsorsTable.draw(false)
                sponsorTariffModal.hide()
            }
            saveSellingPriceBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveSellingPriceBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })

    createSponsorBtn.addEventListener('click', function () {
        createSponsorBtn.setAttribute('disabled', 'disabled')
        http.post('/sponsors', getDivData(newSponsorModal._element), {"html": newSponsorModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newSponsorModal.hide()
                clearDivValues(newSponsorModal._element)
                newSponsorModal._element.querySelector('.allSponsorInputsDiv').classList.add('d-none')
                newSponsorModal._element.querySelector('.registrationBillDiv1').classList.add('d-none')
                sponsorsTable.draw()
            }
            createSponsorBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            createSponsorBtn.removeAttribute('disabled')
            alert(error.response.data.data.message)
        })
    })

    saveSponsorBtn.addEventListener('click', function (event) {
        const sponsorCategoryId = event.currentTarget.getAttribute('data-id')
        saveSponsorBtn.setAttribute('disabled', 'disabled')
        http.post(`/sponsors/${sponsorCategoryId}`, getDivData(updateSponsorModal._element), {"html": updateSponsorModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateSponsorModal.hide()
                sponsorsTable ? sponsorsTable.draw(false) : ''
            }
            saveSponsorBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveSponsorBtn.removeAttribute('disabled')
            alert(error.response.data.message)
        })
    })

    document.querySelector('#allPatientsTable').addEventListener('click', function (event) {
        const updateBtn    = event.target.closest('.updateBtn')
        const deleteBtn  = event.target.closest('.deleteBtn')
        const initiateVisitBtn  = event.target.closest('.initiateVisitBtn')
        const appointmentBtn  = event.target.closest('.appointmentBtn')

        if (updateBtn) {
            updateBtn.setAttribute('disabled', 'disabled')
            const patientId = updateBtn.getAttribute('data-id')
            http.get(`/patients/${ patientId }`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openPatientModal(updatePatientModal, savePatientBtn, response.data.data)
                    }
                    updateBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    updateBtn.removeAttribute('disabled')
                })
        }

        if (initiateVisitBtn) {
            initiateVisitBtn.setAttribute('disabled', 'disabled')
            initiatePatientModal._element.querySelector('#patientId').value = initiateVisitBtn.getAttribute('data-patient')
            initiatePatientModal._element.querySelector('#confirmVisitBtn').setAttribute('data-id', initiateVisitBtn.getAttribute('data-id'))
            initiatePatientModal.show()
            initiateVisitBtn.removeAttribute('disabled')
            setTimeout(function(){confirmVisitBtn.focus()}, 1000)
            
        }

        if (deleteBtn){
            deleteBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Patient?')) {
                const id = deleteBtn.getAttribute('data-id')
                http.delete(`/patients/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            allPatientsTable.draw()
                        }
                        deleteBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteBtn.removeAttribute('disabled')
                    })
            }
        }

        if (appointmentBtn){
            appointmentBtn.setAttribute('disabled', 'disabled')
            appointmentModal._element.querySelector('#patient').value = appointmentBtn.getAttribute('data-patient')
            appointmentModal._element.querySelector('#sponsorName').value = appointmentBtn.getAttribute('data-sponsor')
            appointmentModal._element.querySelector('#saveAppointmentBtn').setAttribute('data-id', appointmentBtn.getAttribute('data-id'))
            appointmentModal.show()
            appointmentBtn.removeAttribute('disabled')
        }
    })

    saveAppointmentBtn.addEventListener('click', function () {
        const id = this.getAttribute('data-id')
        saveAppointmentBtn.setAttribute('disabled', 'disabled')

        http.post(`/appointments/${id}`, getDivData(appointmentDetailsDiv), {html:appointmentDetailsDiv})
        .then((response) => {
            if (response) {
                clearDivValues(appointmentDetailsDiv)
                clearValidationErrors(appointmentDetailsDiv)
                appointmentModal.hide()
            }
            saveAppointmentBtn.removeAttribute('disabled')
        })
        .catch((response) => {
            console.log(response)
            saveAppointmentBtn.removeAttribute('disabled')
        })
    })

    document.querySelectorAll('#prePatientsTable, #appointmentsTable').forEach(table => {

        table.addEventListener('click', function (event) {
            const confirmBtn    = event.target.closest('.confirmBtn')
            const deleteBtn     = event.target.closest('.deleteBtn')
            const deleteApBtn   = event.target.closest('.deleteApBtn')
    
            if (confirmBtn) {
                confirmBtn.setAttribute('disabled', 'disabled')
                const prePatientId = confirmBtn.getAttribute('data-id')
                http.get(`/patients/prepatients/${ prePatientId }`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openPatientModal(updatePatientModal, savePatientBtn, response.data.data)
                            savePatientBtn.innerHTML = 'Confirm'
                            savePatientBtn.setAttribute('data-prepatient', response.data.data.id)
                            savePatientBtn.removeAttribute('data-id')
                        }
                        confirmBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        confirmBtn.removeAttribute('disabled')
                    })
            }
    
            if (deleteBtn){
                deleteBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this Pre-Patient?')) {
                    const id = deleteBtn.getAttribute('data-id')
                    http.delete(`/patients/prepatient/${id}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                prePatientsTable.draw()
                            }
                            deleteBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            alert(error)
                            deleteBtn.removeAttribute('disabled')
                        })
                }
                
            }
    
            if (deleteApBtn){
                deleteApBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this Appointment?')) {
                    const id = deleteApBtn.getAttribute('data-id')
                    http.delete(`/appointments/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                           appointmentsTable ? appointmentsTable.draw() : ''
                        }
                        deleteApBtn.removeAttribute('disabled')
                    })
                    .catch((error) => { console.log(error)
                        deleteApBtn.removeAttribute('disabled')
                    })
                }  
            }
        })
    })

    sendLinkBtn.addEventListener('click', function () {
        const sponsor = getPatientSponsorDatalistOptionId(newPatientModal, newPatientSponsorInputEl, newPatientSponsorDatalistEl)
        sendLinkBtn.setAttribute('disabled', 'disabled')
        let data = {...getDivData(newPatientModal._element), sponsor }

        http.post('/patients/generatelink', {...data}, {"html": newPatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                alert('Link sent')
                newPatientModal.hide()
            }
        })
        .catch((error) => {
            sendLinkBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    registerPatientBtn.addEventListener('click', function () {
        const sponsor = getPatientSponsorDatalistOptionId(newPatientModal, newPatientSponsorInputEl, newPatientSponsorDatalistEl)
        registerPatientBtn.setAttribute('disabled', 'disabled')
        let data = {...getDivData(newPatientModal._element), sponsor }

        http.post('/patients', {...data}, {"html": newPatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newPatientModal.hide()
                clearDivValues(newPatientModal._element)
                newPatientModal._element.querySelector('.allPatientInputsDiv').classList.add('d-none')
                newPatientModal._element.querySelector('.familyRegistrationBillOption')
                allPatientsTable.draw()
            }
            registerPatientBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            console.log(error.response.data.message)
            registerPatientBtn.removeAttribute('disabled')
        })
    })

    savePatientBtn.addEventListener('click', function (event) {
        const patient    = event.currentTarget.getAttribute('data-id')
        const prePatient = event.currentTarget.getAttribute('data-prepatient')
        savePatientBtn.setAttribute('disabled', 'disabled')

        let sponsor = getPatientSponsorDatalistOptionId(updatePatientModal, updatePatientSponsorInputEl, updatePatientSponsorDatalistEl)
        let data = {...getDivData(updatePatientModal._element), sponsor, prePatient }

        http.post(`/patients${patient ? '/'+patient : ''}`, {...data}, {"html": updatePatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updatePatientModal.hide()
                allPatientsTable.draw(false)
                prePatientsTable ? prePatientsTable.draw() : ''
            }
            savePatientBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            savePatientBtn.removeAttribute('disabled')
        })
    })

    confirmVisitBtn.addEventListener('click', function () {

        confirmVisitBtn.setAttribute('disabled', 'disabled')
        const patientId = confirmVisitBtn.getAttribute('data-id')
        const doctorId  = initiatePatientModal._element.querySelector('#doctor').value

        http.post(`/visits/${patientId}`, {doctor :doctorId}, {"html": initiatePatientModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                initiatePatientModal.hide()
                allPatientsTable.draw()
            }
            confirmVisitBtn.removeAttribute('disabled')
        }).catch((error) => {
            confirmVisitBtn.removeAttribute('disabled')
            console.log(error)
        })
    })

    searchVisitsWithDatesBtn.addEventListener('click', function () {
        if (!datesDiv.querySelector('#startDate').value && !datesDiv.querySelector('#endDate').value){
            return alert('Please pick valid dates')
        }
        if ($.fn.DataTable.isDataTable( '#visitsTable' )){
            $('#visitsTable').dataTable().fnDestroy()
        }
        visitsTable = getVisitsTable('visitsTable', datesDiv.querySelector('#startDate').value, datesDiv.querySelector('#endDate').value, datesDiv.querySelector('#filterListBy').value)
    })

    document.querySelector('#newRegPatientsTable').addEventListener('click', function (event) {
        const showPatientsBtn   = event.target.closest('.showPatientsBtn')
        const date              = newRegisterationsDiv.querySelector('#regMonth').value
        if (showPatientsBtn){
            const id = showPatientsBtn.getAttribute('data-id')
            patientsBySponsorModal._element.querySelector('#sponsor').value = showPatientsBtn.getAttribute('data-sponsor') + ' - ' + showPatientsBtn.getAttribute('data-category')
            patientsBySponsorModal._element.querySelector('#regMonth').value = date
            patientsBySponsorTable = getPatientsBySponsorTable('patientsBySponsorTable', id, patientsBySponsorModal, date)
            patientsBySponsorModal.show()
        }
    })

    document.querySelector('#visitsTable').addEventListener('click', function (event) {
        const consultationDetailsBtn    = event.target.closest('.consultationDetailsBtn')
        const viewer                    = 'hmo'
        
        if (consultationDetailsBtn) {
            consultationDetailsBtn.setAttribute('disabled', 'disabled')

            const [visitId, patientType, ancRegId] = [consultationDetailsBtn.getAttribute('data-id'), consultationDetailsBtn.getAttribute('data-patientType'), consultationDetailsBtn.getAttribute('data-ancregid')]
            const isAnc = patientType === 'ANC'
            const [modal, div, displayFunction, vitalSignsTable, id, suffixId] = isAnc ? [ancTreatmentDetailsModal, ancTreatmentDiv, AncPatientReviewDetails, getAncVitalSignsTable, ancRegId, 'AncConDetails'] : [treatmentDetailsModal, regularTreatmentDiv, regularReviewDetails, getVitalSignsTableByVisit, visitId, 'ConDetails']
            http.get(`/consultation/consultations/${visitId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        let iteration = 0
                        let count = 0

                        const consultations = response.data.consultations.data
                        const patientBio = response.data.bio

                        openHmoModals(modal, div, patientBio)

                        consultations.forEach(line => {
                            iteration++

                            iteration > 1 ? count++ : ''

                            div.innerHTML += displayFunction(iteration, getOrdinal, count, consultations.length, line, viewer)
                        })

                        vitalSignsTable(`#vitalSignsTableNurses${suffixId}`, id, modal)
                        modal.show()

                    }
                    consultationDetailsBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    consultationDetailsBtn.removeAttribute('disabled')
                    console.log(error)
                })
        }
    })

    document.querySelectorAll('#treatmentDiv').forEach(div => {
        div.addEventListener('click', function (event) {
            const collapseConsultationBtn   = event.target.closest('.collapseConsultationBtn')
            const viewer = 'hmo'
    
            if (collapseConsultationBtn) {
                const gotoDiv = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
                const investigationTableId = gotoDiv.querySelector('.investigationTable').id
                const treatmentTableId = gotoDiv.querySelector('.treatmentTable').id
                const otherPrescriptionsTableId = gotoDiv.querySelector('.otherPrescriptionsTable').id
                const conId = gotoDiv.querySelector('.investigationTable').dataset.id
    
                if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                    $('#' + investigationTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#' + treatmentTableId)) {
                    $('#' + treatmentTableId).dataTable().fnDestroy()
                }
                if ($.fn.DataTable.isDataTable('#' + otherPrescriptionsTableId)) {
                    $('#' + otherPrescriptionsTableId).dataTable().fnDestroy()
                }
    
                const goto = () => {
                    location.href = collapseConsultationBtn.getAttribute('data-goto')
                    window.history.replaceState({}, document.title, "/" + "patients")
                    getLabTableByConsultation(investigationTableId, treatmentDetailsModal._element, viewer, conId, null)
                    getMedicationsByFilter(treatmentTableId, conId, treatmentDetailsModal._element)
                    getOtherPrescriptionsByFilter(otherPrescriptionsTableId, conId, treatmentDetailsModal._element, null, null)
                }
                setTimeout(goto, 300)
            }
        })
    })

    

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function(event) {
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
            visitsTable ? visitsTable.draw(false) : ''
        })
    })
    
    newSponsorModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorModal._element)
        createSponsorBtn.removeAttribute('disabled')
    })

    updateSponsorModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorModal._element)
        saveSponsorBtn.removeAttribute('disabled')
    })

    newPatientModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newPatientModal._element)
        sendLinkBtn.hasAttribute('disabled') ? clearDivValues(newPatientModal._element) : ''
        sendLinkBtn.removeAttribute('disabled')
        registerPatientBtn.removeAttribute('disabled')
    })

    updatePatientModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(updatePatientModal._element)
        savePatientBtn.removeAttribute('disabled')
    })

    initiatePatientModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(initiatePatientModal._element)
        clearDivValues(initiatePatientModal._element)
    })
})

function openPatientModal(modal, button, {id, sponsorId, sponsorCategoryId, ...data}) {
    for (let name in data) {
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)

        nameInput.value = data[name]
    }

    if (modal._element.id === 'updatePatientModal'){    
        modal._element.querySelector('#updatePatientSponsor').setAttribute('data-id', sponsorId)
        const dataListEl = modal._element.querySelector('#updateSponsorList')
        let date = new Date().toISOString().split('T')[0]
        modal._element.querySelector('[name="dateOfBirth"]').setAttribute('max', date)

        http.get(`/sponsorcategory/list_sponsors/${sponsorCategoryId}`).then((response) => {
            displayList(dataListEl, 'sponsorOption', response.data)
        })
    }

    button.setAttribute('data-id', id)
    modal.show()
}

function openHmoModals(modal, button, { id, visitId, ancRegId, patientType, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}
