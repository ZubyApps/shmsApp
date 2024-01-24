import $ from 'jquery';
import { getAncPatientsVisitTable, getInpatientsVisitTable, getOutpatientsVisitTable } from './tables/doctorstables';

function clearDivValues(div) {
    const tagName = div.querySelectorAll('input, select, textarea')

        tagName.forEach(tag => {
            tag.value = ''
        });        
}

function clearItemsList(element){
    element.innerHTML = ''
}

function stringToRoman(num) { 
    const values =  
        [1000, 900, 500, 400, 100,  
         90, 50, 40, 10, 9, 5, 4, 1]; 
    const symbols =  
        ['M', 'CM', 'D', 'CD', 'C',  
         'XC', 'L', 'XL', 'X', 'IX',  
         'V', 'IV', 'I']; 
    let result = ''; 
  
    for (let i = 0; i < values.length; i++) { 
        while (num >= values[i]) { 
            result += symbols[i]; 
            num -= values[i]; 
        } 
    } 
  
    return result; 
}

function getOrdinal(n) {
    let ord = 'th';
  
    if (n % 10 == 1 && n % 100 != 11)
    {
      ord = 'st';
    }
    else if (n % 10 == 2 && n % 100 != 12)
    {
      ord = 'nd';
    }
    else if (n % 10 == 3 && n % 100 != 13)
    {
      ord = 'rd';
    }
  
    return ord;
  }

  function getDivData(div) {
    let data = {}
    const fields = [
        ...div.getElementsByTagName('input'),
        ...div.getElementsByTagName('select'),
        ...div.getElementsByTagName('textarea')
    ]

    fields.forEach(select => {
        select.hasAttribute('name') ?
            data[select.name] = select.hasAttribute('multiple') ? $('#'+ select.id).val().toString(): select.value : ''
    })

    return data
}

function removeAttributeLoop(element, attribute, value) {
    element.forEach(tag => {
        tag.removeAttribute(attribute)
    })
}

function toggleAttributeLoop(element, attribute, value) {
    element.forEach(tag => {
        tag.toggleAttribute(attribute)
    })
}

function querySelectAllTags(div, ...tags){
    return div.querySelectorAll(tags)
}

function textareaHeightAdjustment(setHeight, tag){
    for (let i = 0; i < tag.length; i++) {
        if (tag[i].value == '') {
            tag[i].setAttribute("style", "height:" + setHeight + "px;overflow-y:hidden;");
        } else {
            tag[i].setAttribute("style", "height:" + (tag[i].scrollHeight) + "px;overflow-y:hidden;");
        }
        tag[i].addEventListener("input", OnInput, false);
    }
}

function OnInput(e){
    let setHeight = 65
    this.scrollHeight < setHeight || this.value === '' ? this.style.height = setHeight + "px" : this.style.height = this.scrollHeight + "px";
}

function dispatchEvent(tag, event) {
    for (let i = 0; i < tag.length; i++) {
        tag[i].dispatchEvent(new Event(event, { bubbles: true }))
    }
}

function handleValidationErrors(errors, domElement) {
    let elementId = []
    for (const name in errors) {
        const element = domElement.querySelector(`[name="${ name }"]`)
        
        elementId.push(element.id)

        element.classList.add('is-invalid')

        const errorDiv = document.createElement('div')

        errorDiv.classList.add('invalid-feedback')
        errorDiv.textContent = errors[name][0]

        element.parentNode.append(errorDiv)
    }
    location.href = '#'+elementId[0]
    window.history.replaceState({}, document.title, "/" + document.title.toLowerCase() )
}

function clearValidationErrors(domElement) {
    domElement.querySelectorAll('.is-invalid').forEach(function (element) {
        element.classList.remove('is-invalid')

        element.parentNode.querySelectorAll('.invalid-feedback').forEach(function (e) {
            e.remove()
        })
    })
}

const getSelctedText = (selectEl) => {
    return selectEl.options[selectEl.selectedIndex]
}

function displayList(dataListEl, optionsId, data) {
    dataListEl.innerHTML = ''
    
    
    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', optionsId)
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)
        dataListEl.appendChild(option)
    })

    }

function getDatalistOptionId(modal, inputEl, datalistEl) {    
    const selectedOption = datalistEl.options.namedItem(inputEl.value)
    
        if (selectedOption) {
            return selectedOption.getAttribute('data-id')
        } else {
            return ""
        }
    }

function openModals(modal, button, {id, ...data}) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        
        nameInput.value = data[name]
    }
    
    button.setAttribute('data-id', id)
    modal.show()
}

function doctorsModalClosingTasks(event, modal, textareaHeight){
    if (!confirm('Have you saved? You will loose all unsaved data')) {
        event.preventDefault()
        return
    }
    clearDivValues(modal.querySelector('.investigationAndManagementDiv'))
    clearDivValues(modal.querySelector('#consultationDiv'))
    clearValidationErrors(modal.querySelector('#consultationDiv'))
    modal.querySelector('#updateKnownClinicalInfoBtn').innerHTML = `Update`
    modal.querySelector('#saveConsultationBtn').removeAttribute('disabled')
    modal.querySelector('.investigationAndManagementDiv').classList.add('d-none')
    modal.querySelectorAll('.resourceList').forEach(list => clearItemsList(list))
    removeAttributeLoop(querySelectAllTags(modal.querySelector('#consultationDiv'), ['input, select, textarea']), 'disabled')
    for (let t = 0; t < modal.querySelector('#consultationDiv').getElementsByTagName("textarea").length; t++){
        modal.querySelector('#consultationDiv').getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
    }
}

function addDays(date, days) {
    const dateCopy = new Date(date);
    dateCopy.setDate(date.getDate() + days);
    return dateCopy;
}

function getWeeksDiff(today, lmp) {
    const weeksCoverter = 1000 * 60 * 60 * 24 * 7;
    return (Math.abs(today.getTime() - lmp.getTime())/weeksCoverter).toFixed(1);
}

function getWeeksModulus(today, lmp) {
    const daysCoverter = 1000 * 60 * 60 * 24;
    return Math.round(Math.abs(today.getTime() - lmp.getTime())/daysCoverter);
}

function getPatientSponsorDatalistOptionId(modal, inputEl, datalistEl) {  
    const selectedOption = datalistEl.options.namedItem(inputEl.value)
    
        if (selectedOption) {
            return selectedOption.getAttribute('data-id')
        } else {
            return ""
        }
    }

function loadingSpinners() {
    return `<div class="ms-1 spinner-grow spinner-grow-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="ms-1 spinner-grow spinner-grow-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `
}

const detailsBtn = (row) => {
    return `
            <div class="d-flex flex-">
                <button class="btn btn-outline-primary consultationDetailsBtn" data-id="${ row.id }" data-patientType="${ row.patientType }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">Details</button>
            </div>
            `      
}

const reviewBtn = (row) => {
    return `
            <div class="d-flex flex-">
                <button class="btn btn-outline-primary consultationReviewBtn" data-id="${ row.id }" data-patientType="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">Review</button>
            </div>
            `      
}

const sponsorAndPayPercent = (row) => {
    let payPercent
    if (row.sponsorCategory === 'NHIS'){
        payPercent = row.payPercentNhis
    } else if (row.sponsorCategory === 'HMO' || row.sponsorCategory === 'Retainership'){
        payPercent = row.payPercentHmo
    } else {
        payPercent = row.payPercent
    }
    return payPercent !== null ? 
            `<div class="progress" role="progressbar" aria-label="sponsor bill" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height: 40px">
            <div class="progress-bar text-dark fs-6 px-1 overflow-visible bg-${payPercent <= 50 ? 'danger' : payPercent > 50 && payPercent < 100 ? 'warning' : 'primary'}" style="width: ${payPercent}%";>${row.sponsor+' '+payPercent+'%'}</div>
            </div>` : 
           row.sponsor
}

const displayPaystatus = (row, credit, NHIS) => {
    if (credit || NHIS){
        return  `<i class="bi ${row.approved ? 'bi-check-circle-fill text-primary' : row.rejected ? 'bi-x-circle-fill text-danger' : 'bi-dash-circle-fill text-secondary'} tooltip-test" title=${row.approved ? 'approved' : row.rejected ? 'rejected' : 'not-processed'}></i> ${row.paid || row.paidNhis ? '<i class="bi bi-p-circle-fill text-primary tooltip-test" title="paid"></i>': ''} `
    } else {
        return  row.paid ? '<i class="bi bi-p-circle-fill tooltip-test text-primary" title="paid"></i>' : ''
    }
}

const admissionStatus = (row) => {
    return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
    `<div class="d-flex flex-">
        <button class="d-flex flex- btn fw-bold text-primary tooltip-test dischargedBtn" title="Inpatient" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">
        <i class="bi bi-hospital-fill"></i>
        ${row.dicharged ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}" title="discharged"></i>` : ''}
        </button>
    </div>` :
    `<div class="d-flex flex-">
        <button class="d-flex flex- btn fw-bold tooltip-test dischargedBtn" title="Outpatient" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">
        <i class="bi bi-hospital"></i>
        ${row.reason ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}" title="discharged"></i>` : ''}
        </button>
    </div>`
}

const bmiCalculator = (elements) => {
    elements.forEach(elInput => {
        elInput.addEventListener('input',  function (e){
            const div = elInput.parentElement.parentElement.parentElement
            if (elInput.dataset.id == div.id){
                const bmiValue = (div.querySelector('#weight').value.split('k')[0]/(div.querySelector('#height').value.split('m')[0])**2).toFixed(2) 
                div.querySelector('#bmi').value = bmiValue > 0 && bmiValue !== 'Infinity' ? bmiValue : ''
            }
        })
    })
}

const lmpCalculator = (elements, elementDiv) => {
    elements.forEach(lmp => {
        lmp.addEventListener('change', function () {
            elementDiv.forEach(div => {
                if (lmp.dataset.lmp == div.dataset.div){
                    if (lmp.value){
                        const lmpDate = new Date(lmp.value) 
                        div.querySelector('#edd').value = addDays(lmpDate, 280).toISOString().split('T')[0]
                        div.querySelector('#ega').value = String(getWeeksDiff(new Date(), lmpDate)).split('.')[0] + 'W' + ' ' + getWeeksModulus(new Date, lmpDate)%7 + 'D'
                    }                    
                }
            })
        })
    })
}

const filterPatients = (elements) => {
    elements.forEach(filterInput => {
        filterInput.addEventListener('change', function () {
            if (filterInput.id == 'filterListOutPatients'){
                $.fn.DataTable.isDataTable( '#outPatientsVisitTable' ) ? $('#outPatientsVisitTable').dataTable().fnDestroy() : ''
                getOutpatientsVisitTable('#outPatientsVisitTable', filterInput.value)
            }
            if (filterInput.id == 'filterListInPatients'){
                $.fn.DataTable.isDataTable( '#inPatientsVisitTable' ) ? $('#inPatientsVisitTable').dataTable().fnDestroy() : ''
                getInpatientsVisitTable('#inPatientsVisitTable', filterInput.value)
            }
            if (filterInput.id == 'filterListAncPatients'){
                $.fn.DataTable.isDataTable( '#ancPatientsVisitTable' ) ? $('#ancPatientsVisitTable').dataTable().fnDestroy() : ''
                getAncPatientsVisitTable('#ancPatientsVisitTable', filterInput.value)
            }
        })
    })
}

const removeDisabled = (element) => {
    setTimeout(() => element.removeAttribute('disabled'), 2000 ) 
}

const resetFocusEndofLine = (element) => {
    let value = element.value
    setTimeout(function(){
        element.focus()
        element.value = ''
        element.value = value
    }, 1)
}

const dischargeColour = (reason) => {
    switch(reason) {
        case 'Treated':
            return 'primary'
          break;
        case 'AHOR':
            return 'warning'
          break;
        case 'Referred':
            return 'info'
            break;
        case 'DAMA':
            return 'danger'
            break;
        case 'LTFU':
            return 'secondary'
            break;
        case 'Diceased':
            return 'dark'
            break;
        default:
          return ''
      }
}

const populateConsultationModal = (modal, btn, visitId, ancRegId, patientType) => {
    btn.setAttribute('data-id', visitId)
    btn.setAttribute('data-ancregid', ancRegId)
    btn.setAttribute('data-patientType', patientType)
    modal._element.querySelector('#saveConsultationBtn').setAttribute('data-patientType', patientType)
}

const populateDischargeModal = (modal, btn) => {
    modal._element.querySelector('#patientId').value = btn.getAttribute('data-patient')
    modal._element.querySelector('#sponsorName').value = btn.getAttribute('data-sponsor')
    modal._element.querySelector('#currentDiagnosis').value = btn.getAttribute('data-diagnosis')
    modal._element.querySelector('#admissionStatus').value = btn.getAttribute('data-admissionstatus')
    modal._element.querySelector('#reason').value = btn.getAttribute('data-reason')
    modal._element.querySelector('#remark').value = btn.getAttribute('data-remark')
    modal._element.querySelector('#doctor').innerHTML = btn.getAttribute('data-doctor')
    modal._element.querySelector('#saveDischargeBtn').setAttribute('data-id', btn.getAttribute('data-id'))
}

const populatePatientSponsor = (modal, data) => {
    modal._element.querySelector('#patient').value = data.patientId
    modal._element.querySelector('#sponsorName').value = data.sponsorName
    
    // updateResultModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
    // updateResultModal._element.querySelector('#patient').value = patientBio.patientId
    // investigationAndManagementModal._element.querySelector('#patient').value = patientBio.patientId
    // investigationAndManagementModal._element.querySelector('#sponsorName').value = patientBio.sponsorName
}
    
export {clearDivValues, clearItemsList, stringToRoman, getOrdinal, getDivData, removeAttributeLoop, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, dispatchEvent, handleValidationErrors, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId, openModals,doctorsModalClosingTasks, addDays, getWeeksDiff, getWeeksModulus, loadingSpinners, detailsBtn, reviewBtn, sponsorAndPayPercent, displayPaystatus, bmiCalculator, lmpCalculator, filterPatients, removeDisabled, resetFocusEndofLine, getPatientSponsorDatalistOptionId, admissionStatus, dischargeColour, populateConsultationModal, populateDischargeModal, populatePatientSponsor}    