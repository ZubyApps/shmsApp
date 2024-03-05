import $ from 'jquery';
import { getAncPatientsVisitTable, getInpatientsVisitTable, getOutpatientsVisitTable } from './tables/doctorstables';
import { Collapse } from 'bootstrap';

function clearDivValues(div) {
    const tagName = div.querySelectorAll('input, select, textarea')

        tagName.forEach(tag => {
            tag.value = ''
            tag.checked = false
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
            data[select.name] = select.value : ''
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

function openMedicalReportModal(modal, button, {id, ...data}) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        if (name === 'report' || name === 'recipientsAddress'){
            nameInput.innerHTML = data[name]
        }
        nameInput.value = data[name]
    }
    
    button.setAttribute('data-id', id)
    modal.show()
}

function displayMedicalReportModal(modal, {...data}) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        nameInput.innerHTML = data[name]
    }
    
    
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

function getMinsDiff(today, futureDate) {
    const minsCoverter = 1000 * 60;
    return (Math.floor(futureDate.getTime() - today.getTime())/minsCoverter).toFixed(1);
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
                <button class="btn btn-outline-primary tooltip-test consultationDetailsBtn ${row.closed ? 'px-1': ''}" title="${row.closed ? 'record closed': ''}" data-id="${ row.id }" data-patienttype="${ row.patientType }" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctordone="${row.doctorDone}" data-closed="${row.closed}">Details${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}</button>
            </div>
            `      
}

const prescriptionStatusContorller = (row, tableId) => {
    return `<span class="text-decoration-underline btn tootip-test ${row.doseComplete ? '' : 'discontinueBtn'}" title="${row.doseComplete ? 'completed' : 'discontinue'}" data-id="${row.id}"           data-table="${tableId}">
                ${row.prescription == '' ? row.note ?? '' : row.prescription} 
            </span>`      
}

const detailsBtn1 = (row) => {
    return `
    <div class="dropdown">
        <a class="btn btn-outline-primary tooltip-test text-decoration-none ${row.closed ? 'px-1': ''}" title="${row.closed ? 'record closed': ''}" data-bs-toggle="dropdown">
            More${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}
        </a>
            <ul class="dropdown-menu">
            <li>
                <a class=" btn btn-outline-primary dropdown-item consultationDetailsBtn tooltip-test" title="details" data-id="${ row.id }" data-patienttype="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctordone="${row.doctorDone}" data-closed="${row.closed}">
                    Details
                </a>
                <a class="dropdown-item reportsListBtn btn tooltip-test" title="write report"  data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
                    Report
                </a>
            </li>
        </ul>
    </div>
    `
}

const reviewBtn = (row) => {
    return `
    <div class="dropdown">
        <a class="btn btn-outline-primary tooltip-test text-decoration-none ${row.closed ? 'px-1': ''}" title="${row.closed ? 'record closed': ''}" data-bs-toggle="dropdown" data-bs-auto-close='outside'>
            More${row.closed ? '<i class="bi bi-lock-fill"></i>': ''}
        </a>
            <ul class="dropdown-menu">
            <li>
                <a class=" btn btn-outline-primary dropdown-item consultationReviewBtn tooltip-test" title="details" data-id="${ row.id }" data-patienttype="${ row.patientType }" data-sponsorcat="${row.sponsorCategory}" data-ancregid="${row.ancRegId}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctordone="${row.doctorDone}" data-closed="${row.closed}" data-selecteddiagnosis="${row.selectedDiagnosis}" data-provisionaldiagnosis="${row.provisionalDiagnosis}">
                    Review
                </a>
                <li class="dropdown dropend">
                    <a class="dropdown-item btn btn-outline-primary dropdown-toggle patientsBillBtn btn tooltip-test" title="patient's bill" data-bs-auto-close='outside' data-bs-toggle="dropdown">
                        More
                    </a>
                    <ul class="dropdown-menu">
                        <a class="dropdown-item btn btn-outline-primary medicalReportBtn" data-id="${ row.id }" data-patient="${ row.patient }" data-patientid="${ row.patientId }" data-sponsor="${ row.sponsor }" data-age="${ row.age }" data-sex="${ row.sex }">Report/Refer/Result</a>
                        <a class="dropdown-item btn btn-outline-primary dischargedBtn" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">${row.discharged ? 'Patient Discharged' : 'Discharge' }</a>
                    </ul>
                </li>
                <a class="dropdown-item btn btn-outline-primary tooltip-test" title="${row.closed ? 'open?': 'close?'}"  data-id="${ row.id }" id="${row.closed ? 'openVisitBtn' : 'closeVisitBtn'}">
                ${row.closed ? 'Open? <i class="bi bi-unlock-fill"></i>': 'Close? <i class="bi bi-lock-fill"></i>'}
                </a>
            </li>
        </ul>
    </div>
    `
}

const histroyBtn = (row) => {
    return `<a class="historyBtn tooltip-test text-decoration-none text-dark" href="#" title="history" data-patientid="${row.patientId}" data-patientType="${ row.patientType }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">${row.patient}</a>`
}

const displayConsultations = (div, displayFunction, iteration, getOrdinal, count, length, data, viewer, isDoctorDone, closed) => {
    div.innerHTML += displayFunction(iteration, getOrdinal, count, length, data, viewer, isDoctorDone, closed)
}

const displayVisits = (div, displayFunction, iteration, getOrdinal, data, viewer, isDoctorDone, isAnc) => {
    div.innerHTML += displayFunction(iteration, getOrdinal, data, viewer, isDoctorDone, isAnc)
}

const closeReviewButtons = (modal, closed) => {
    let reviewBtns = modal._element.querySelectorAll('.reviewConBtns')
    reviewBtns.forEach(btn => {
        if (closed){
            btn.classList.add('d-none') 
        } else {
            btn.classList.remove('d-none')
        }
    })
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
            <div class="progress-bar text-dark fs-6 px-1 overflow-visible bg-${payPercent <= 50 ? 'danger' : payPercent > 50 && payPercent < 90 ? 'warning' : payPercent > 90 && payPercent < 100 ? 'primary-subtle' : 'primary'}" style="width: ${payPercent}%";>${row.sponsor+'-'+ row.sponsorCategory +' '+payPercent+'%'}</div>
            </div>` : 
           row.sponsor+'-'+ row.sponsorCategory
}

const displayPaystatus = (row, credit, NHIS) => {
    if (credit || NHIS){
        return  `<i class="bi ${+row.approved ? 'bi-check-circle-fill text-primary' : row.rejected ? 'bi-x-circle-fill text-danger' : 'bi-dash-circle-fill text-secondary'} tooltip-test" title=${row.approved ? 'approved' : row.rejected ? 'rejected' : 'not processed'}></i> ${row.paid || row.paidNhis ? '<i class="bi bi-p-circle-fill text-primary tooltip-test" title="paid"></i>': ''} `
    } else {
        return  row.paid ? '<i class="bi bi-p-circle-fill tooltip-test text-primary" title="paid"></i>' : '<i class="bi bi-dash-circle-fill text-secondary tooltip-test" title="not processed"></i>'
    }
}

const admissionStatus = (row) => {
    return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
    `<div class="d-flex flex-">
        <div class="dropdown">
            <a class="d-flex flex- btn tooltip-test text-decoration-none text-primary ${row.ward && row.bedNo ? '' : 'colour-change'} tooltip-test" title="Inpatient" data-bs-toggle="dropdown" href="" >
                <i class="bi bi-hospital-fill"></i>
                ${row.discharged ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}" title="discharged"></i>` : ''}
            </a>
                <ul class="dropdown-menu">
                <li>
                    <a role="button" class="dropdown-item wardBedBtn" data-id="${ row.id }" data-conid="${ row.conId }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-updatedby="${row.updatedBy}" data-doctor="${row.doctor}" data-ward="${row.ward}" data-bedno="${row.bedNo}">
                        Update Ward & Bed
                    </a>
                </li>
                <li>
                    <a role="button" class="dropdown-item dischargedBtn" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">
                        Discharge Details ${row.discharged ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}"></i>` : ''}
                    </a>
                </li>
            </ul>
        </div>
    </div>` :
    `<div class="d-flex flex-">
        <button class="d-flex flex- btn fw-bold tooltip-test dischargedBtn" title="Outpatient" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">
        <i class="bi bi-hospital"></i>
        ${row.reason ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}" title="discharged"></i>` : ''}
        </button>
    </div>`
}

const admissionStatusX = (row) => {
    return row.admissionStatus == 'Inpatient' || row.admissionStatus == 'Observation' ? 
    `<div class="d-flex flex-">
        <div class="dropdown">
            <a class="d-flex flex- btn tooltip-test text-decoration-none text-primary tooltip-test" title="Inpatient" data-bs-toggle="dropdown" href="" >
                <i class="bi bi-hospital-fill"></i>
                ${row.discharged ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}" title="discharged"></i>` : ''}
            </a>
        </div>
    </div>` :
    `<div class="d-flex flex-">
        <button class="d-flex flex- btn fw-bold tooltip-test dischargedBtn" title="Outpatient" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-admissionstatus="${row.admissionStatus}" data-diagnosis="${row.diagnosis}" data-reason="${row.reason}" data-remark="${row.remark}" data-doctor="${row.doctor}">
        <i class="bi bi-hospital"></i>
        ${row.reason ? `<i class="ms-1 bi bi-arrow-up-right-circle-fill tooltip-test text-${dischargeColour(row.reason)}" title="discharged"></i>` : ''}
        </button>
    </div>`
}

const prescriptionOnLatestConsultation = (row) => {
    return `<div class="d-flex flex-">
                <span class="btn" id="${row.closed ? '' : 'updateResourceListBtn'}" data-id="${ row.id }" data-conid="${ row.conId }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">${row.diagnosis}</span>
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

const lmpCurrentCalculator = (value, div) => {
    if (!value){return}
    const lmpDate = new Date(value)
    div.querySelector('#lmp').value = value
    div.querySelector('#edd').value = addDays(lmpDate, 280).toISOString().split('T')[0]
    div.querySelector('#ega').value = String(getWeeksDiff(new Date(), lmpDate)).split('.')[0] + 'W' + ' ' + getWeeksModulus(new Date, lmpDate)%7 + 'D'
}

const filterPatients = (elements) => {
    console.log(elements)
    elements.forEach(filterInput => {
        filterInput.addEventListener('change', function () {
            console.log(filterInput.value)
            if (filterInput.id == 'filterListOutPatients'){
                $.fn.DataTable.isDataTable( '#outPatientsVisitTable' ) ? $('#outPatientsVisitTable').dataTable().fnDestroy() : ''
                return getOutpatientsVisitTable('#outPatientsVisitTable', filterInput.value)
            }
            if (filterInput.id == 'filterListInPatients'){
                $.fn.DataTable.isDataTable( '#inPatientsVisitTable' ) ? $('#inPatientsVisitTable').dataTable().fnDestroy() : ''
                return getInpatientsVisitTable('#inPatientsVisitTable', filterInput.value)
            }
            if (filterInput.id == 'filterListAncPatients'){
                $.fn.DataTable.isDataTable( '#ancPatientsVisitTable' ) ? $('#ancPatientsVisitTable').dataTable().fnDestroy() : ''
                return getAncPatientsVisitTable('#ancPatientsVisitTable', filterInput.value)
            }
        })
    })
}

const removeDisabled = (element) => {
    setTimeout(() => element.removeAttribute('disabled'), 1500 ) 
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
        case 'Recovered':
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
        case 'Deceased':
            return 'dark'
            break;
        default:
          return ''
      }
}

const populateConsultationModal = (modal, btn, visitId, ancRegId, patientType, conbtn) => {
    btn.setAttribute('data-id', visitId)
    btn.setAttribute('data-ancregid', ancRegId)
    btn.setAttribute('data-patientType', patientType)
    modal._element.querySelector('#saveConsultationBtn').setAttribute('data-patientType', patientType)
    modal._element.querySelector('#admit').setAttribute('data-admissionstatus', conbtn.getAttribute('data-admissionstatus'))
    modal._element.querySelector('#selectedDiagnosis').value = conbtn.getAttribute('data-selecteddiagnosis')
    modal._element.querySelector('#provisionalDiagnosis').value = conbtn.getAttribute('data-provisionaldiagnosis')
}

const populateDischargeModal = (modal, btn) => {
    populatePatientSponsor(modal, btn)
    modal._element.querySelector('#currentDiagnosis').value = btn.getAttribute('data-diagnosis')
    modal._element.querySelector('#admissionStatus').value = btn.getAttribute('data-admissionstatus')
    modal._element.querySelector('#reason').value = btn.getAttribute('data-reason')
    modal._element.querySelector('#remark').value = btn.getAttribute('data-remark')
    modal._element.querySelector('#doctor').innerHTML = btn.getAttribute('data-doctor')
    modal._element.querySelector('#saveDischargeBtn').setAttribute('data-id', btn.getAttribute('data-id'))
}

const populateWardAndBedModal = (modal, btn) => {
    populatePatientSponsor(modal, btn)
    modal._element.querySelector('#currentDiagnosis').value = btn.getAttribute('data-diagnosis')
    modal._element.querySelector('#admissionStatus').value = btn.getAttribute('data-admissionstatus')
    modal._element.querySelector('#admit').value = btn.getAttribute('data-admissionstatus')
    modal._element.querySelector('#admit').setAttribute('data-admissionstatus', btn.getAttribute('data-admissionstatus'))
    modal._element.querySelector('#ward').value = btn.getAttribute('data-ward')
    modal._element.querySelector('#bedNumber').value = btn.getAttribute('data-bedno')
    modal._element.querySelector('#doctor').innerHTML = btn.getAttribute('data-doctor')
    modal._element.querySelector('#updatedBy').innerHTML = btn.getAttribute('data-updatedby')
    modal._element.querySelector('#saveWardAndBedBtn').setAttribute('data-id', btn.getAttribute('data-conid'))
}

const populatePatientSponsor = (modal, btn) => {
    modal._element.querySelector('#patient').value = btn.getAttribute('data-patient')
    modal._element.querySelector('#sponsorName').value = btn.getAttribute('data-sponsor')
}

const populateVitalsignsModal = (modal, btn, id) => {
    populatePatientSponsor(modal, btn)
    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-id', id)
    modal._element.querySelector('#addVitalsignsBtn').setAttribute('data-ancregid', id)
}

const displayItemsList = (datalistEl, data, optionName) => {
    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', optionName)
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)
        option.setAttribute('data-cat', line.category)
        // option.setAttribute('data-plainname', line.plainName)

        !datalistEl.options.namedItem(line.name) ? datalistEl.appendChild(option) : ''
    })
}

function getSelectedResourceValues(modal, inputEl, datalistEl) {  
    const selectedOption = datalistEl.options.namedItem(inputEl.value)
        if (selectedOption) {
            return {
                resource : selectedOption.getAttribute('data-id'),
                resourceCategory : selectedOption.getAttribute('data-cat'),              
            }
        } else {
            return ""
        }
}

export {clearDivValues, clearItemsList, stringToRoman, getOrdinal, getDivData, removeAttributeLoop, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, dispatchEvent, handleValidationErrors, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId, openModals,doctorsModalClosingTasks, addDays, getWeeksDiff, getWeeksModulus, loadingSpinners, detailsBtn, reviewBtn, sponsorAndPayPercent, displayPaystatus, bmiCalculator, lmpCalculator, filterPatients, removeDisabled, resetFocusEndofLine, getPatientSponsorDatalistOptionId, admissionStatus, dischargeColour, populateConsultationModal, populateDischargeModal, populatePatientSponsor, populateVitalsignsModal, lmpCurrentCalculator, histroyBtn, displayConsultations, displayVisits, displayItemsList, closeReviewButtons, prescriptionStatusContorller, getMinsDiff, openMedicalReportModal, displayMedicalReportModal, prescriptionOnLatestConsultation, detailsBtn1, admissionStatusX, populateWardAndBedModal, getSelectedResourceValues}