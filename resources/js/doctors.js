import { Modal } from "bootstrap"
import * as ECT from '@whoicd/icd11ect'
import '@whoicd/icd11ect/style.css'
import { consultationDetails, items } from "./data"
import { clearDivValues } from "./helpers"

window.addEventListener('DOMContentLoaded', function () {
    const newConsultationModal      = new Modal(document.getElementById('newConsultationModal'))
    const reviewConsultationModal   = new Modal(document.getElementById('reviewConsultationModal'))
    const surgeryModal              = new Modal(document.getElementById('surgeryModal'))

    const newConsultationBtn        = document.querySelector('.newConsultationBtn')
    const reviewConsultationBtn     = document.querySelector('.reviewConsultationBtn')

    const saveNewConsultationBtn    = document.querySelector('#saveNewConsultationBtn')
    const saveReviewConsultationBtn = document.querySelector('#saveReviewConsultationBtn')

    const addKnownClinicalInfoBtn   = newConsultationModal._element.querySelector('.addKnownClinicalInfoBtn')
    const reviewAddKnownClinicalInfoBtn = reviewConsultationModal._element.querySelector('.reviewKnownClinicalInfoBtn')

    const addVitalsignsBtn = document.querySelector('#addVitalsignsBtn')
    const addReviewVitalsignsBtn = document.querySelector('#addReviewVitalsignsBtn')
    
    const addVitalsignsDiv = document.querySelector('.addVitalsignsDiv')
    const addReviewVitalsignsDiv = document.querySelector('.addReviewVitalsignsDiv')

    const newConsultationDiv = document.querySelector('#newConsultationDiv')
    const reviewConsultationDiv = document.querySelector('#reviewConsultationDiv')
    const consultationReviewDiv = document.querySelector('#consultationReviewDiv')

    const newInvestigationAndManagementDiv = document.querySelector('.newInvestigationAndManagmentDiv')
    const reviewInvestigationAndManagementDiv = document.querySelector('.reviewInvestigationAndManagmentDiv')

    const diagnosisInput = document.querySelector('.selectedDiagnosis')
    const newConsultationItemInput = newConsultationModal._element.querySelector('#item')


    // ICD11settings
    const mySettings = { apiServerUrl: "https://icd11restapi-developer-test.azurewebsites.net" }


    // ICD11 callbacks
    const myCallbacks = {
        selectedEntityFunction: (selectedEntity) => {
            diagnosisInput.value += selectedEntity.code + '-' + selectedEntity.selectedText
            ECT.Handler.clear("1")
        }
    }

    // ICD11 handler
    ECT.Handler.configure(mySettings, myCallbacks)

    // NEW CONSULTATION MODAL CODE 

    // show new consultation modal
    newConsultationBtn.addEventListener('click', function () {
        displayItemsList(newInvestigationAndManagementDiv, items)
        newConsultationModal.show()
    })

    // manipulating known clinical info on new consultation modal
    addKnownClinicalInfoBtn.addEventListener('click', function () {
        toggleAttributeLoop(querySelectAllTags(newConsultationModal._element.querySelector('.knownClinicalInfoDiv'), ['input, select, textarea']), 'disabled', '')

        addKnownClinicalInfoBtn.textContent === "Done" ? 
        addKnownClinicalInfoBtn.innerHTML = `<i class="bi bi-arrow-up-circle"></i> Update` : 
        addKnownClinicalInfoBtn.textContent = "Done"
    }) 

     // manipulating the vital signs div on new consultation modal
     addVitalsignsBtn.addEventListener('click', function () {
        addVitalsignsDiv.classList.toggle('d-none')
    })
   
    // save new consultation
    saveNewConsultationBtn.addEventListener('click', function () {
        //console.log(getConsultationDivData(newConsultationDiv))

        toggleAttributeLoop(querySelectAllTags(newConsultationDiv, ['input, select, textarea']), 'disabled')

        saveNewConsultationBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? 
        saveNewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : 
        saveNewConsultationBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'


        newInvestigationAndManagementDiv.classList.remove('d-none')
    })

    // newConsultationItemInput.addEventListener('keyup', function() {

    //     const searchTerm = newConsultationItemInput.value
    //     const list = []
    //     for(var i=0; i<items.length; i++) {
    //         for(var key in items[i]) {
    //             //console.log(items[i])
    //             console.log(key)
    //           if(items[i][key].indexOf(searchTerm)!=-1) {
    //             list.push(items[i]);
    //           }
    //         }
    //       }

       
    //     //console.log(list, searchTerm)
    //     displayItemsList(newInvestigationAndManagementDiv, list)
    // })

    // tasks to run when closing new consultation modal
    newConsultationModal._element.addEventListener('hidden.bs.modal', function() {
        newInvestigationAndManagementDiv.classList.add('d-none')
        saveNewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        removeAttributeLoop(querySelectAllTags(newConsultationDiv, ['input, select, textarea']), 'disabled')
        clearDivValues(newConsultationDiv)
    })


    // REVIEW CONSULTATION MODAL CODE

    // Open review consultation modal and returning a loop of all consultations for the given pattient
    reviewConsultationBtn.addEventListener('click', function () {
        let iteration = 0
        consultationDetails.data.forEach(line => {
            iteration++
            iteration > 1 ? 
            consultationReviewDiv.innerHTML += `
            <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                <span class="mx-2">Consultation ${iteration}</span>
                <i class="bi bi-chevron-double-down text-primary"> </i>
            </div>
            <div class="collapse" id="collapseExample${iteration}" style="">
                <div class="card card-body">
                    <div class="mb-2 form-control">
                        <span class="fw-semibold">Vital Signs</span>                                            
                        <div class="row overflow-auto m-1">
                            <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Temperature</th>
                                        <th>Blood Pressure</th>
                                        <th>Sugar Level</th>
                                        <th>Pulse Rate</th>
                                        <th>Respiratory Rate</th>
                                        <th>Weight</th>
                                        <th>Height</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${line.id}</td>
                                        <td>${line.temperature}</td>
                                        <td>120/80mmgh</td>
                                        <td>8.8mmol</td>
                                        <td>90</td>
                                        <td>32</td>
                                        <td>94kg</td>
                                        <td>1.5m</td>
                                    </tr>
                                    <tr>
                                        <td>10-Jul-2023</td>
                                        <td>37.1C</td>
                                        <td>110/80mmgh</td>
                                        <td>8.5mmol</td>
                                        <td>96</td>
                                        <td>40</td>
                                        <td>94kg</td>
                                        <td>1.5m</td>
                                    </tr>
                                </tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="mb-2 form-control">
                        <span class="fw-semibold">Consultation Review</span>                                            
                        <div class="row">
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="presentingComplainLabel">Complain</span> 
                                    <textarea class="form-control" name="presentingComplain" id="presentingComplain" cols="10" rows="3" readonly="readonly">${line.complain}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="historyOfPresentingComplainLabel">Physician's <br> Notes </span>
                                    <textarea class="form-control" name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="3" readonly="readonly">${line.notes}</textarea>
                                </div>
                            </div> 
                            <div class="col-xl-4 themed-grid-col col-xl-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                                    <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3" readonly="readonly">${line.examinationFindings}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="diagnosisLabel"> Assessment </span>
                                    <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="3" readonly="readonly">${line.selectedDiagnosis ?? 'nill'}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="physiciansPlanLabel"> Physicians Plan </span>
                                    <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="3" readonly="readonly">${line.plan}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="admitLabel"> Patient Status </span>
                                    <input class="form-control patientStatus" name="patientStatus" value="${line.status}" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="wardLabel"> Ward </span>
                                    <input class="form-control ward" name="ward" value="${line.ward}">
                                </div>
                            </div>
                        </div>
                        <div class="my-2 form-control">
                            <span class="fw-semibold"> Medication & Treatment </span>
                            <div class="row overflow-auto m-1">
                                <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                    <thead>
                                        <tr>
                                            <th>Prescribed At</th>
                                            <th>Treatment/Medication</th>
                                            <th>Dosaage</th>
                                            <th>Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>12/09/2023 11:02pm</td>
                                            <td>N/S 500mls</td>
                                            <td>500mls 12hrly x2</td>
                                            <td>2</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                         <div class="d-flex justify-content-end my-2">
                            <button type="button" id="deleteConsultationBtn" class="btn btn-outline-primary">
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>
                        </div>
                        ${consultationDetails.data.length > iteration ? '' : 
                        `<div class="d-flex justify-content-center my-2">
                            <button type="button" class="btn btn-primary updateInvestigationAndManagmentBtn" data-id="${iteration}">
                                Update Investigation & Managment
                            </button>
                        </div> 
                        <div class="updateInvestigationAndManagmentDiv d-none" data-id="${iteration}">
                            <div class="mb-2 form-control">
                                <span class="fw-semibold">Investigation & Management</span>
                                <div class="row">
                                    <div class="col-xl-4 themed-grid-col col-xl-6">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="itemLabel">Item</span> 
                                            <input class="form-control" type="search" name="item" id="item" placeholder="search" autocomplete="">
                                            <datalist name="item" type="text" class="decoration-none"></datalist>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col col-xl-6">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="prescriptionLabel">Prescription </span> 
                                            <input class="form-control" type="text" name="prescription" id="prescription" placeholder="eg: 5mg BD x5" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col col-xl-6">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="quantityLabel"> Quantity</span> 
                                            <input class="form-control" type="number" name="quantity" id="quantity" placeholder="" autocomplete="">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="addInvestigationAndManagmnentBtn" class="btn btn-primary">
                                        add
                                    <i class="bi bi-prescription"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2 form-control">
                                <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Billed at</th>
                                            <th>Item</th>
                                            <th>Prescription</th>
                                            <th>Qty</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>12/09/2023 11:02pm</td>
                                            <td>N/S 500mls</td>
                                            <td>500mls 12hrly x2</td>
                                            <td></td>
                                            <td><button class="btn btn-outline-primary deleteBtn"><i
                                                        class="bi bi-trash"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>12/09/2023 11:15pm</td>
                                            <td>5% mls Syringe</td>
                                            <td></td>
                                            <td>4</td>
                                            <td><button class="btn btn-outline-primary deleteBtn"><i
                                                        class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>`}
                        <div class="d-flex justify-content-start my-2 gap-2">
                            <button type="button" id="deleteConsultationBtn" class="btn btn-outline-primary">
                            File
                            <i class="bi bi-archive"></i>
                            </button>
                            <button type="button" id="surgeryBtn" class="btn btn-outline-primary">
                                Surgery 
                            <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" id="deleteConsultationBtn" class="btn btn-outline-primary">
                                Delivery
                            <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            ` : 
            consultationReviewDiv.innerHTML += `
            <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                <span class="mx-2">Consultation ${iteration}</span>
                <i class="bi bi-chevron-double-down text-primary"> </i>
            </div>
            <div class="collapse" id="collapseExample${iteration}" style="">
                <div class="card card-body">
                    <div class="mb-2 form-control">
                        <span class="fw-semibold">Vital Signs</span>                                            
                        <div class="row overflow-auto m-1">
                            <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Temperature</th>
                                        <th>Blood Pressure</th>
                                        <th>Sugar Level</th>
                                        <th>Pulse Rate</th>
                                        <th>Respiratory Rate</th>
                                        <th>Weight</th>
                                        <th>Height</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${line.id}</td>
                                        <td>${line.temperature}</td>
                                        <td>120/80mmgh</td>
                                        <td>8.8mmol</td>
                                        <td>90</td>
                                        <td>32</td>
                                        <td>94kg</td>
                                        <td>1.5m</td>
                                    </tr>
                                    <tr>
                                        <td>10-Jul-2023</td>
                                        <td>37.1C</td>
                                        <td>110/80mmgh</td>
                                        <td>8.5mmol</td>
                                        <td>96</td>
                                        <td>40</td>
                                        <td>94kg</td>
                                        <td>1.5m</td>
                                    </tr>
                                </tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="mb-2 form-control">
                        <span class="fw-semibold">Consultation</span>                                            
                        <div class="row">
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="presentingComplainLabel">Presenting <br>Complain</span> 
                                    <textarea class="form-control" name="presentingComplain" id="presentingComplain" cols="10" rows="3" readonly="readonly"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                            <div class="input-group mb-1">
                                <span class="input-group-text" id="historyOfPresentingComplainLabel">History of <br> Presenting <br> Complain </span>
                                <textarea class="form-control" name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="3"></textarea>
                            </div>
                        </div> 
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="medicalHistoryLabel"> Past Medical/ <br> Surgical History</span>                            
                                    <textarea class="form-control" name="medicalHistory" id="medicalHistory" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="obGyneHistoryLabel">Obstetrics/<br>Gynecological <br> History</span>
                                    <textarea class="form-control" type="text" name="obGyneHistory" id="obGyneHistory" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                                    <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="diagnosisLabel"> Selected <br>ICD11 <br> Diagnosis </span>
                                    <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="3">${line.selectedDiagnosis ?? 'nill'}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="diagnosisLabel"> Addional <br> Diagnosis </span> 
                                    <textarea class="form-control additionalDiagnosis" type="text" name="additionalDiagnosis" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="physiciansPlanLabel"> Physicians Plan </span>
                                    <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="admitLabel"> Patient Status </span>
                                    <input class="form-control patientStatus" name="patientStatus" value="Out-Patient">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="wardLabel"> Ward </span>
                                    <input class="form-control ward" name="ward" value="Private Ward">
                                </div>
                            </div>
                        </div>
                        <div class="my-2 form-control">
                            <span class="fw-semibold"> Medication & Treatment </span>
                            <div class="row overflow-auto m-1">
                                <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                    <thead>
                                        <tr>
                                            <th>Prescribed At</th>
                                            <th>Treatment/Medication</th>
                                            <th>Dosaage</th>
                                            <th>Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>12/09/2023 11:02pm</td>
                                            <td>N/S 500mls</td>
                                            <td>500mls 12hrly x2</td>
                                            <td>2</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        ${consultationDetails.data.length > iteration ? '' : 
                        `<div class="d-flex justify-content-end my-2">
                            <button type="button" id="deleteReviewConsultationBtn" class="btn btn-outline-primary">
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>
                        </div>`}
                        ${consultationDetails.data.length > iteration ? '' : 
                        `<div class="d-flex justify-content-center my-2">
                            <button type="button" class="btn btn-primary updateInvestigationAndManagmentBtn" data-id="${iteration}">
                                Update Investigation & Managment
                            </button>
                        </div> 
                        <div class="updateInvestigationAndManagmentDiv d-none" data-id="${iteration}">
                            <div class="mb-2 form-control">
                                <span class="fw-semibold">Investigation & Management</span>
                                <div class="row">
                                    <div class="col-xl-4 themed-grid-col col-xl-6">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="itemLabel">Item</span> 
                                            <input class="form-control" type="search" name="item" id="item" placeholder="search" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col col-xl-6">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="prescriptionLabel">Prescription </span> 
                                            <input class="form-control" type="text" name="prescription" id="prescription" placeholder="eg: 5mg BD x5" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col col-xl-6">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="quantityLabel"> Quantity</span> 
                                            <input class="form-control" type="number" name="quantity" id="quantity" placeholder="" autocomplete="">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="addInvestigationAndManagmnentBtn" class="btn btn-primary">
                                        add
                                    <i class="bi bi-prescription"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2 form-control">
                                <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Billed at</th>
                                            <th>Item</th>
                                            <th>Prescription</th>
                                            <th>Qty</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>12/09/2023 11:02pm</td>
                                            <td>N/S 500mls</td>
                                            <td>500mls 12hrly x2</td>
                                            <td></td>
                                            <td><button class="btn btn-outline-primary deleteBtn"><i
                                                        class="bi bi-trash"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>12/09/2023 11:15pm</td>
                                            <td>5% mls Syringe</td>
                                            <td></td>
                                            <td>4</td>
                                            <td><button class="btn btn-outline-primary deleteBtn"><i
                                                        class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>`}
                        <div class="d-flex justify-content-start my-2 gap-2">
                            <button type="button" id="deleteConsultationBtn" class="btn btn-outline-primary">
                            File
                            <i class="bi bi-archive"></i>
                            </button>
                            <button type="button" id="surgeryBtn" class="btn btn-outline-primary">
                                Surgery 
                            <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" id="deleteConsultationBtn" class="btn btn-outline-primary">
                                Delivery
                            <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            `  
        })
      
        reviewConsultationModal.show()
    })

    // manipulating known clinical info on review consultation modal
    reviewAddKnownClinicalInfoBtn.addEventListener('click', function () {    
        toggleAttributeLoop(querySelectAllTags(reviewConsultationModal._element.querySelector('.knownClinicalInfoDiv'), ['input, select, textarea']), 'disabled', '')

        reviewAddKnownClinicalInfoBtn.textContent === "Done" ? 
        reviewAddKnownClinicalInfoBtn.innerHTML = `<i class="bi bi-arrow-up-circle"></i> Update` :
        reviewAddKnownClinicalInfoBtn.textContent = "Done"
    })

    // manipulating the vital signs div on review consultation modal
    addReviewVitalsignsBtn.addEventListener('click', function () {
        addReviewVitalsignsDiv.classList.toggle('d-none')
    })

    //save review consultation
    saveReviewConsultationBtn.addEventListener('click', function () {
        console.log(getConsultationDivData(reviewConsultationDiv))

        toggleAttributeLoop(querySelectAllTags(reviewConsultationDiv, ['input, select, textarea']), 'disabled')

        saveReviewConsultationBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? 
        saveReviewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : 
        saveReviewConsultationBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'

        displayItemsList(reviewInvestigationAndManagementDiv, items)
        console.log(reviewInvestigationAndManagementDiv)
        reviewInvestigationAndManagementDiv.classList.remove('d-none')
    })

    // delete consultation
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteConsultationBtn')
        const updateInvestigationAndManagmentBtn    = event.target.closest('.updateInvestigationAndManagmentBtn')
        const updateInvestigationAndManagmentDiv    = document.querySelectorAll('.updateInvestigationAndManagmentDiv')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')

        if (deleteConsultationBtn) {
            if (confirm('If you delete this consultation you cannot get it back! Are you sure you want to delete?')) {

            }
        }

        if (updateInvestigationAndManagmentBtn) {
            updateInvestigationAndManagmentDiv.forEach(div => {

                if (div.getAttribute('data-id') === updateInvestigationAndManagmentBtn.getAttribute('data-id')) {
                    div.classList.toggle('d-none')
                    displayItemsList(div, items)
                }
                
            })
        }

        if (surgeryBtn) {
            surgeryModal.show()
        }
    })


    // tasks to run when closing review consultation modal
    reviewConsultationModal._element.addEventListener('hide.bs.modal', function(event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        consultationReviewDiv.innerHTML = ''
        reviewInvestigationAndManagementDiv.classList.add('d-none')
        saveReviewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        removeAttributeLoop(querySelectAllTags(reviewConsultationDiv, ['input, select, textarea']), 'disabled')
        clearDivValues(reviewConsultationDiv)
    })


})

function getConsultationFormData(modal) {
    let data = {}
    const fields = [
        ...modal._element.getElementsByTagName('input'),
        ...modal._element.getElementsByTagName('select'),
        ...modal._element.getElementsByTagName('textarea')
    ]

    fields.forEach(select => {
        select.hasAttribute('name') ?
            data[select.name] = select.value : ''
    })

    return data
}

function getConsultationDivData(div) {
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

function displayItemsList(div, data) {
    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', 'itemsOption')

        // if (div.id === 'editJobModal')
        // {
        //     var elementAttributes = {
        //     "value"     : line.name,
        //     "data-id"   : line.id,
        //     "name"      : line.name,
        //     }
        // } else {
            var elementAttributes = {
            "value"     : line.name + ' ' + line.phoneNumber,
            "data-id"   : line.id,
            "name"      : line.name + ' ' + line.phoneNumber,
            }
        //}


        Object.keys(elementAttributes).forEach(attribute => {
        option.setAttribute(attribute, elementAttributes[attribute])
        
        div.querySelector('#item').setAttribute('list', 'itemsList')
        div.querySelector('datalist').setAttribute('id', 'itemsList')
        div.querySelector('#itemsList').appendChild(option)
        });
        })
    }
