import { Offcanvas, Modal } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, getOrdinal, getDivData, textareaHeightAdjustment, loadingSpinners, clearValidationErrors, openModals, populatePatientSponsor, displayItemsList, getDatalistOptionId, handleValidationErrors, getDatalistOptionStock} from "./helpers"
import { regularReviewDetails, AncPatientReviewDetails } from "./dynamicHTMLfiles/consultations";
import { getPatientsVisitsByFilterTable, getInpatientsInvestigationsTable, getOutpatientsInvestigationTable } from "./tables/investigationTables";
import { getLabTableByConsultation } from "./tables/doctorstables";
import { getBulkRequestTable } from "./tables/pharmacyTables";
import html2pdf  from "html2pdf.js"
import { debounce } from "chart.js/helpers";
$.fn.dataTable.ext.errMode = 'throw';

window.addEventListener('DOMContentLoaded', function () {
    const treatmentDetailsModal         = new Modal(document.getElementById('treatmentDetailsModal'))
    const ancTreatmentDetailsModal      = new Modal(document.getElementById('ancTreatmentDetailsModal'))
    const addResultModal                = new Modal(document.getElementById('addResultModal'))
    const updateResultModal             = new Modal(document.getElementById('updateResultModal'))
    const investigationsModal           = new Modal(document.getElementById('investigationsModal'))
    const bulkRequestModal              = new Modal(document.getElementById('bulkRequestModal'))
    const labResultModal                = new Modal(document.getElementById('labResultModal'))
    const removeTestModal               = new Modal(document.getElementById('removeTestModal'))
    const inpatientsInvestigationsList  = new Offcanvas(document.getElementById('offcanvasInvestigations'))
    const outpatientsInvestigationsList = new Offcanvas(document.getElementById('offcanvasOutpatientsInvestigations'))

    const regularTreatmentDiv       = treatmentDetailsModal._element.querySelector('#treatmentDiv')
    const ancTreatmentDiv           = ancTreatmentDetailsModal._element.querySelector('#treatmentDiv')
    const addResultDiv              = addResultModal._element.querySelector('#resultDiv')
    const removalReasonDiv          = removeTestModal._element.querySelector('#removalReasonDiv')
    const updateResultDiv           = updateResultModal._element.querySelector('#resultDiv')

    const createResultBtn           = addResultModal._element.querySelector('#createResultBtn')
    const saveRemovalReasonBtn      = removeTestModal._element.querySelector('#saveRemovalReasonBtn')
    const saveResultBtn             = updateResultModal._element.querySelector('#saveResultBtn')
    const bulkRequestBtn            = document.querySelector('#newBulkRequestBtn')
    const requestBulkBtn            = bulkRequestModal._element.querySelector('#requestBulkBtn')
    const downloadResultBtn         = labResultModal._element.querySelector('#downloadResultBtn')

    const inpatientsInvestigationCount      = document.querySelector('#inpatientsInvestigationCount')
    const outpatientsInvestigationCount      = document.querySelector('#outpatientsInvestigationCount')

    const itemInput                 = bulkRequestModal._element.querySelector('#item')

    const outPatientsTab            = document.querySelector('#nav-outPatients-tab')
    const inPatientsTab             = document.querySelector('#nav-inPatients-tab')
    const ancPatientsTab            = document.querySelector('#nav-ancPatients-tab')
    const bulkRequestsTab           = document.querySelector('#nav-bulkRequests-tab')
    const [outPatientsView, inPatientsView, ancPatientsView] = [document.querySelector('#nav-outPatients-view'), document.querySelector('#nav-inPatients-view'), document.querySelector('#nav-ancPatients-view')]

    const testListDiv               = labResultModal._element.querySelector('.testListDiv')
    const multipleTestsListDiv      = labResultModal._element.querySelector('.multipleTestsListDiv')

     // Auto textarea adjustment
     const textareaHeight = 90;
     textareaHeightAdjustment(textareaHeight, document.getElementsByTagName("textarea"))

    let inPatientsVisitTable, ancPatientsVisitTable, bulkRequestsTable

    const inpatientsInvestigationsTable = getInpatientsInvestigationsTable('inpatientInvestigationsTable')
    const outpatientInvestigationTable = getOutpatientsInvestigationTable('outpatientInvestigationsTable')

    const outPatientsVisitsTable = getPatientsVisitsByFilterTable('#outPatientsVisitTable', 'Outpatient')
    $('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable, #inpatientInvestigationsTable, #outpatientInvestigationsTable, #investigationsTable').on('error.dt', function(e, settings, techNote, message) {techNote == 7 ? window.location.reload() : ''})

    inpatientsInvestigationsTable.on('draw.init', function() {
        const count = inpatientsInvestigationsTable.rows().count()
        if (count > 0 ){
            inpatientsInvestigationCount.innerHTML = count
        } else {
            inpatientsInvestigationCount.innerHTML = ''
        }
    })

    outpatientInvestigationTable.on('draw.init', function() {
        const count = outpatientInvestigationTable.rows().count()
        if (count > 0 ){
            outpatientsInvestigationCount.innerHTML = count
        } else {
            outpatientsInvestigationCount.innerHTML = ''
        }
    })

    const refreshMainTables = debounce(() => {
        outPatientsView.checkVisibility() ? outPatientsVisitsTable.draw(false) : '';
        ancPatientsView.checkVisibility() ? ancPatientsVisitTable ? ancPatientsVisitTable.draw(false) : '' : ''
        inPatientsView.checkVisibility() ? inPatientsVisitTable ? inPatientsVisitTable.draw(false) : '' : ''
    }, 100)

    outPatientsTab.addEventListener('click', function() {outPatientsVisitsTable.draw()})

    inPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#inPatientsVisitTable' )){
            $('#inPatientsVisitTable').dataTable().fnDraw()
        } else {
            inPatientsVisitTable = getPatientsVisitsByFilterTable('#inPatientsVisitTable', 'Inpatient')
        }
    })

    ancPatientsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#ancPatientsVisitTable' )){
            $('#ancPatientsVisitTable').dataTable().fnDraw()
        } else {
            ancPatientsVisitTable = getPatientsVisitsByFilterTable('#ancPatientsVisitTable', 'ANC')
        }
    })

    bulkRequestsTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#bulkRequestsTable' )){
            $('#bulkRequestsTable').dataTable().fnDraw()
        } else {
            bulkRequestsTable = getBulkRequestTable('bulkRequestsTable', 'lab')
        }
    })

    document.querySelectorAll('#offcanvasInvestigations, #offcanvasOutpatientsInvestigations').forEach(canvas => {
        canvas.addEventListener('show.bs.offcanvas', function () {
            canvas.id === 'offcanvasInvestigations' ? inpatientsInvestigationsTable.draw() : outpatientInvestigationTable.draw();
        })

    })

    document.querySelectorAll('#offcanvasInvestigations, #offcanvasOutpatientsInvestigations').forEach(canvas => {
        canvas.addEventListener('hide.bs.offcanvas', function () {
            refreshMainTables()
        })
    })

    document.querySelectorAll('#outPatientsVisitTable, #inPatientsVisitTable, #ancPatientsVisitTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const consultationDetailsBtn = event.target.closest('.consultationDetailsBtn')
            const investigationsBtn = event.target.closest('.investigationsBtn')
            const viewer = 'lab'

            if (consultationDetailsBtn) {
                consultationDetailsBtn.setAttribute('disabled', 'disabled')
                const btnHtml = consultationDetailsBtn.innerHTML
                consultationDetailsBtn.innerHTML = loadingSpinners()
    
                const [visitId, patientType, ancRegId] = [consultationDetailsBtn.getAttribute('data-id'), consultationDetailsBtn.getAttribute('data-patientType'), consultationDetailsBtn.getAttribute('data-ancregid')]
                const isAnc = patientType === 'ANC'
                const [modal, div, displayFunction] = isAnc ? [ancTreatmentDetailsModal, ancTreatmentDiv, AncPatientReviewDetails] : [treatmentDetailsModal, regularTreatmentDiv, regularReviewDetails]
                
                http.get(`/consultation/consultations/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            let iteration = 0
                            let count = 0
    
                            const consultations = response.data.consultations.data
                            const patientBio = response.data.bio
    
                            openLabModals(modal, div, patientBio)
    
                            addResultModal._element.querySelector('#patient').value = patientBio.patientId
                            updateResultModal._element.querySelector('#patient').value = patientBio.patientId
                            addResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
                            updateResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
    
                            
                            consultations.forEach(line => {
                                iteration++
    
                                iteration > 1 ? count++ : ''

                                div.innerHTML += displayFunction(iteration, getOrdinal, count, consultations.length, line, viewer)

                                if(isAnc){
                                    const goto = () => {                                    
                                        getLabTableByConsultation('investigationTable'+line.id, modal._element, 'lab', line.id, '')
                                    }
                                    setTimeout(goto, 300)
                                }
                            })
    
                            modal.show()
    
                        }
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        consultationDetailsBtn.innerHTML = btnHtml
                        consultationDetailsBtn.removeAttribute('disabled')
                        console.log(error)
                    })
            }
    
            if (investigationsBtn) {
                investigationsBtn.setAttribute('disabled', 'disabled')
                const tableId = investigationsModal._element.querySelector('.investigationsTable').id
                const visitId = investigationsBtn.getAttribute('data-id')
                investigationsModal._element.querySelector('#patient').value = investigationsBtn.getAttribute('data-patient')
                investigationsModal._element.querySelector('#sponsorName').value = investigationsBtn.getAttribute('data-sponsor') + ' - ' + investigationsBtn.getAttribute('data-sponsorcat')
    
                getLabTableByConsultation(tableId, investigationsModal._element, viewer, null, visitId)
    
                investigationsModal.show()
                investigationsBtn.removeAttribute('disabled')
            }
    
        })
    })

    document.querySelectorAll('#treatmentDetailsModal, #ancTreatmentDetailsModal, #investigationsModal, #addResultModal, #updateResultModal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function () {
            refreshMainTables()
            modal.id == 'addResultModal' || modal.id == 'updateResultModal' ?
             '':
            regularTreatmentDiv.innerHTML = ''
            ancTreatmentDiv.innerHTML = ''
        })
    })

    document.querySelectorAll('#inpatientInvestigationsTable, #outpatientInvestigationsTable').forEach(table => {
        table.addEventListener('click', (event) => {
            const addResultBtn      = event.target.closest('#addResultBtn')
            const removeResultBtn   = event.target.closest('#removeTestBtn')
    
            if (addResultBtn) {
                createResultBtn.setAttribute('data-id', addResultBtn.getAttribute('data-id'))
                createResultBtn.setAttribute('data-table', addResultBtn.getAttribute('data-table'))
                addResultModal._element.querySelector('#patient').value = addResultBtn.getAttribute('data-patient')
                addResultModal._element.querySelector('#sponsorName').value = addResultBtn.getAttribute('data-sponsor') + ' - ' + addResultBtn.getAttribute('data-sponsorcat')
                addResultModal._element.querySelector('#diagnosis').value = addResultBtn.getAttribute('data-diagnosis')
                addResultModal._element.querySelector('#investigation').value = addResultBtn.getAttribute('data-investigation')
                addResultModal.show()
            }

            if (removeResultBtn){
                saveRemovalReasonBtn.setAttribute('data-id', removeResultBtn.getAttribute('data-id'))
                saveRemovalReasonBtn.setAttribute('data-table', removeResultBtn.getAttribute('data-table'))
                removeTestModal._element.querySelector('#patient').value = removeResultBtn.getAttribute('data-patient')
                removeTestModal._element.querySelector('#sponsorName').value = removeResultBtn.getAttribute('data-sponsor') + ' - ' + removeResultBtn.getAttribute('data-sponsorcat')
                removeTestModal._element.querySelector('#diagnosis').value = removeResultBtn.getAttribute('data-diagnosis')
                removeTestModal._element.querySelector('#investigation').value = removeResultBtn.getAttribute('data-investigation')
                removeTestModal.show()
            }
        })
    })

    document.querySelectorAll('#treatmentDiv, #investigationModalDiv').forEach(div => {
        div.addEventListener('click', function (event) {
            const collapseConsultationBtn  = event.target.closest('.collapseConsultationBtn')
            const addResultBtn             = event.target.closest('#addResultBtn')
            const updateResultBtn          = event.target.closest('#updateResultBtn')
            const printThisBtn             = event.target.closest('#printThisBtn')
            const printAllBtn              = event.target.closest('#printAllBtn')
            const deleteResultBtn          = event.target.closest('.deleteResultBtn')
            const viewer = 'lab'
    
            if (collapseConsultationBtn) {
                const gotoDiv = document.querySelector(collapseConsultationBtn.getAttribute('data-goto'))
                const investigationTableId = gotoDiv.querySelector('.investigationTable').id
                const conId = gotoDiv.querySelector('.investigationTable').dataset.id
                if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                    $('#' + investigationTableId).dataTable().fnDestroy()
                }
    
                const goto = () => {
                    location.href = collapseConsultationBtn.getAttribute('data-goto')
                    window.history.replaceState({}, document.title, '/' + 'investigations')
                    getLabTableByConsultation(investigationTableId, treatmentDetailsModal._element, viewer, conId, null)
                }
                setTimeout(goto, 300)
            }
    
            if (addResultBtn) {
                // investigationsModal.hide()
                createResultBtn.setAttribute('data-id', addResultBtn.getAttribute('data-id'))
                createResultBtn.setAttribute('data-table', addResultBtn.getAttribute('data-table'))
                populatePatientSponsor(addResultModal, addResultBtn)
                addResultModal._element.querySelector('#diagnosis').value = addResultBtn.getAttribute('data-diagnosis')
                addResultModal._element.querySelector('#investigation').value = addResultBtn.getAttribute('data-investigation')
                addResultModal.show()
            }
    
            if (updateResultBtn) {
                // investigationsModal.hide()
                const prescriptionId = updateResultBtn.getAttribute('data-id')
                saveResultBtn.setAttribute('data-table', updateResultBtn.getAttribute('data-table'))
                populatePatientSponsor(updateResultModal, updateResultBtn)
                updateResultModal._element.querySelector('#diagnosis').value = updateResultBtn.getAttribute('data-diagnosis')
                updateResultModal._element.querySelector('#investigation').value = updateResultBtn.getAttribute('data-investigation')
                http.get(`/investigations/${prescriptionId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openModals(updateResultModal, saveResultBtn, response.data.data)
                        updateResultModal._element.querySelector('#result').innerHTML = response.data.data?.result ?? ''
                    }
                })
                .catch((error) => {
                    alert(error)
                })
            }

            if (printThisBtn) {
                const prescriptionId = printThisBtn.getAttribute('data-id')
                labResultModal._element.querySelector('#test').innerHTML = printThisBtn.getAttribute('data-investigation')
                labResultModal._element.querySelector('#patientsId').innerHTML = printThisBtn.getAttribute('data-patient')
                http.get(`/investigations/${prescriptionId}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        labResultModal._element.querySelector('#result').innerHTML = response.data.data?.result ?? ''
                    }
                })
                labResultModal._element.querySelector('#resultDate').innerHTML = printThisBtn.getAttribute('data-sent')
                labResultModal._element.querySelector('#StaffFullName').innerHTML = printThisBtn.getAttribute('data-stafffullname')
                labResultModal.show()
            }

            if (printAllBtn){
                const id = printAllBtn.getAttribute('data-id')
                labResultModal._element.querySelector('#patientsId').innerHTML = printAllBtn.getAttribute('data-patient')
                labResultModal._element.querySelector('#resultDate').innerHTML = new Date().toLocaleDateString('en-GB')
                http.get(`/investigations/printall/${id}`)
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300) {
                        openLabResultModal(labResultModal, labResultModal._element.querySelector('.multipleTestsListDiv'), response.data.tests.data)
                    }
                    printAllBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    alert(error)
                    printAllBtn.removeAttribute('disabled')
                })

            }
    
            if (deleteResultBtn){
                deleteResultBtn.setAttribute('disabled', 'disabled')
                const prescriptionTableId = deleteResultBtn.getAttribute('data-table')
                if (confirm('Are you sure you want to delete this result?')) {
                    const prescriptionId = deleteResultBtn.getAttribute('data-id')
                    http.patch(`/investigations/remove/${prescriptionId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            
                            if ($.fn.DataTable.isDataTable('#' + prescriptionTableId)) {
                                $('#' + prescriptionTableId).dataTable().fnDraw()
                            }
                        }
                        deleteResultBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                        deleteResultBtn.removeAttribute('disabled')
                    })
                }
            }
        })
    })

    testListDiv.addEventListener('click', function () {
        testListDiv.setAttribute('contentEditable', 'true')
    })

    multipleTestsListDiv.addEventListener('click', function () {
        multipleTestsListDiv.setAttribute('contentEditable', 'true')
    })

    labResultModal._element.addEventListener('hide.bs.modal', function (){
        testListDiv.querySelector('#test').innerHTML = ''
        testListDiv.querySelector('#result').innerHTML = ''
        multipleTestsListDiv.innerHTML = ''
    })

    // const editor = document.querySelector('#result')
    // editor.addEventListener('paste', handlePaste)

    function handlePaste(e) {
        e.preventDefault()

        const text = (e.clipboardData || window.clipboardData).getData('text')
        const selection = window.getSelection()

        if (selection.rangeCount) {
            selection.deleteFromDocument()
            selection.getRangeAt(0).insertNode(document.createTextNode(text))
        }
    }

    createResultBtn.addEventListener('click', function () {
        const prescriptionId = createResultBtn.getAttribute('data-id')
        const investigationTableId = createResultBtn.getAttribute('data-table')
        createResultBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(addResultDiv), prescriptionId, result: addResultDiv.querySelector('#result').innerHTML }

        http.patch(`/investigations/create/${prescriptionId}`, { ...data }, { "html": addResultDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(addResultDiv)
                addResultDiv.querySelector('#result').innerHTML = ''
                clearValidationErrors(addResultDiv)
                addResultModal.hide()

                if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                    $('#' + investigationTableId).dataTable().fnDraw()
                }
            }
            createResultBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            console.log(error)
            createResultBtn.removeAttribute('disabled')
        })
    })

    saveResultBtn.addEventListener('click', function () {
        const prescriptionId = saveResultBtn.getAttribute('data-id')
        const investigationTableId = saveResultBtn.getAttribute('data-table')
        saveResultBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(updateResultDiv), prescriptionId, result: updateResultDiv.querySelector('#result').innerHTML }

        http.patch(`/investigations/update/${prescriptionId}`, { ...data }, { "html": updateResultDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(updateResultDiv)
                updateResultDiv.querySelector('#result').innerHTML = ''
                clearValidationErrors(updateResultDiv)

                if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                    $('#' + investigationTableId).dataTable().fnDraw()
                }
            }
            saveResultBtn.removeAttribute('disabled')
            updateResultModal.hide()
        })
        .catch((error) => {
            console.log(error)
            saveResultBtn.removeAttribute('disabled')
        })
    })

    saveRemovalReasonBtn.addEventListener('click', function () {
        const prescriptionId = saveRemovalReasonBtn.getAttribute('data-id')
        const investigationTableId = saveRemovalReasonBtn.getAttribute('data-table')
        saveRemovalReasonBtn.setAttribute('disabled', 'disabled')

        let data = { ...getDivData(removalReasonDiv), prescriptionId }

        http.patch(`/investigations/removalreason/${prescriptionId}`, { ...data }, { "html": removalReasonDiv })
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(removalReasonDiv)
                clearValidationErrors(removalReasonDiv)
                removeTestModal.hide()

                if ($.fn.DataTable.isDataTable('#' + investigationTableId)) {
                    $('#' + investigationTableId).dataTable().fnDraw()
                }
            }
            saveRemovalReasonBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            console.log(error)
            saveRemovalReasonBtn.removeAttribute('disabled')
        })
    })

    bulkRequestBtn.addEventListener('click', function () {
        bulkRequestModal.show()
    })

    itemInput.addEventListener('input', function () {
        const dept       = itemInput.dataset.dept
        const datalistEl = bulkRequestModal._element.querySelector(`#itemList${dept}`)
            if (itemInput.value <= 2) {
            datalistEl.innerHTML = ''
            }
            if (itemInput.value.length > 2) {
                http.get(`/bulkrequests/list/bulk`, {params: {resource: itemInput.value, dept: dept}}).then((response) => {
                    displayItemsList(datalistEl, response.data, 'itemOption')
                })
            }
    })

    requestBulkBtn.addEventListener('click', function () {
        const dept      = itemInput.dataset.dept
        requestBulkBtn.setAttribute('disabled', 'disabled')
        const itemId    =  getDatalistOptionId(bulkRequestModal._element, itemInput, bulkRequestModal._element.querySelector(`#itemList${dept}`))
        // const itemStock =  getDatalistOptionStock(bulkRequestModal._element, itemInput, bulkRequestModal._element.querySelector(`#itemList${dept}`))
        // const quantity  = bulkRequestModal._element.querySelector('#quantity').value
        if (!itemId) {
            clearValidationErrors(bulkRequestModal._element)
            const message = {"item": ["Please pick an item from the list"]}               
            handleValidationErrors(message, bulkRequestModal._element)
            requestBulkBtn.removeAttribute('disabled')
            return
        // } else if (quantity < 0){
        //     clearValidationErrors(bulkRequestModal._element)
        //     const message = {"quantity": ["This quantity is more than the available stock, please reduce the quantity"]}               
        //     handleValidationErrors(message, bulkRequestModal._element)
        //     requestBulkBtn.removeAttribute('disabled')
        //     return
        } else {clearValidationErrors(bulkRequestModal._element)}
        http.post(`/bulkrequests/${itemId}`, getDivData(bulkRequestModal._element), {"html": bulkRequestModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300) {
                clearDivValues(bulkRequestModal._element.querySelector('.valuesDiv'))
                clearValidationErrors(bulkRequestModal._element)
            }
            requestBulkBtn.removeAttribute('disabled')
            bulkRequestModal.hide()
        })
        .catch((error) => {
            console.log(error)
            requestBulkBtn.removeAttribute('disabled')
        })
    })

    bulkRequestModal._element.addEventListener('hide.bs.modal', function () {
        bulkRequestsTable ? bulkRequestsTable.draw() : ''
    })

    downloadResultBtn.addEventListener('click', function () {
        const patientFullName = labResultModal._element.querySelector('#patientsId').innerHTML
        const resultModalBody = labResultModal._element.querySelector('.resultModalBody')

        var opt = {
        margin:       0.5,
        filename:     patientFullName + `'s result.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(resultModalBody).save()
    })

})

function openLabModals(modal, button, { id, visitId, ancRegId, patientType, ...data }) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${name}"]`)

        nameInput.value = data[name]
    }

    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', visitId)
}

function openLabResultModal(modal, div, data) {

    data.forEach(test => {
        div.innerHTML += `<div class="fw-semibold" name="test" id="test">${test.test}</div> <p class="" id="result" name="result">${test.result}</p>`
    })

    modal.show() 
}