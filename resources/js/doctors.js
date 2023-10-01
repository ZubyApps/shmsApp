import { Modal, Collapse } from "bootstrap"
import * as ECT from '@whoicd/icd11ect'
import '@whoicd/icd11ect/style.css'
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList } from "./helpers"

window.addEventListener('DOMContentLoaded', function () {
    const newConsultationModal      = new Modal(document.getElementById('newConsultationModal'))
    const reviewConsultationModal   = new Modal(document.getElementById('reviewConsultationModal'))
    const surgeryModal              = new Modal(document.getElementById('surgeryModal'))
    const fileModal                 = new Modal(document.getElementById('fileModal'))
    const deliveryModal             = new Modal(document.getElementById('deliveryModal'))

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
    const reviewConsultationItemInput = reviewConsultationModal._element.querySelector('#item')


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

    newConsultationItemInput.addEventListener('keyup', function() {

        const searchTerm = newConsultationItemInput.value
        const list = []
        for(var i=0; i<items.length; i++) {
            for(var key in items[i]) {
                //console.log(items[i])
                console.log(key)
              if(items[i][key].indexOf(searchTerm)!=-1) {
                list.push(items[i]);
              }
            }
          }

       
        //console.log(list, searchTerm)
        displayItemsList(newInvestigationAndManagementDiv, list)
    })

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

            if (iteration > 1) {
                consultationReviewDiv.innerHTML += `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Consultation ${iteration}</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        <div class="mb-2 form-control">
                            <span class="fw-bold text-primary">Vital Signs</span>                                            
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
                            <span class="fw-bold text-primary">Consultation Review</span>                                            
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
                                <span class="fw-bold text-primary"> Investigations </span>
                                <div class="row overflow-auto m-1">
                                    <table id="investigationTable" class="table table-hover align-middle table-sm bg-primary">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Requested</th>
                                                <th>Physician</th>
                                                <th>Test/Investigation</th>
                                                <th>Result</th>
                                                <th>Doc</th>
                                                <th>Upload</th>
                                                <th>Date</th>
                                                <th>Staff</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Lab</td>
                                                <td>12/9/23 11:02pm</td>
                                                <td>Dr Toby</td>
                                                <td>Malaria Parasite</td>
                                                <td>Pfall ++</td>
                                                <td><span class="position-relative"><a href="/transactions/11/receipts/15" target="blank" title="ABAGI Ernest_Nguevese.pdf">
                                                <i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a></span></td>
                                                <td><a role="button" class=""><i class="bi bi-upload"></i></td></a>
                                                <td>12/09/23</td>
                                                <td>Onjefu</td>
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
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="my-2 form-control">
                                <span class="fw-bold text-primary"> Medication & Treatment </span>
                                <div class="row overflow-auto m-1">
                                    <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                        <thead>
                                            <tr>
                                                <th>Prescribed</th>
                                                <th>Treatment/Medication</th>
                                                <th>Dosaage</th>
                                                <th>Physician</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>12/09/2023 11:02pm</td>
                                                <td>N/S 500mls</td>
                                                <td>500mls 12hrly x2</td>
                                                <td>Dr Toby</td>
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
                            <div class="updateInvestigationAndManagmentDiv mt-2 d-none" data-id="${iteration}">
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
                                                <span class="input-group-text" id="prescriptionLabel">Prescription/Instruction </span> 
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
                                                <th>Prescribed</th>
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
                                                <td><button class="btn btn-outline-primary deleteBtn"><iclass="bi bi-trash"></i></button></td>
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
                            <div class="d-flex justify-content-start my-3 gap-2">
                                <button type="button" id="fileBtn" class="btn btn-outline-primary">
                                File
                                <i class="bi bi-file-earmark-medical"></i>
                                </button>
                                <button type="button" id="surgeryBtn" class="btn btn-outline-primary">
                                    Surgery 
                                <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" id="deliveryBtn" class="btn btn-outline-primary">
                                    Delivery
                                <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                            <div class="extraInfoDiv">
                                <div class="my-2 form-control">
                                    <span class="fw-bold text-primary"> Other Documents </span>
                                    <div class="row overflow-auto m-1">
                                        <table id="otherDocumentsTable" class="table table-hover align-middle table-sm bg-primary">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Name</th>
                                                    <th>Document</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>12/09/2023</td>
                                                    <td>Excuse Duty</td>
                                                    <td><span class="position-relative"><a href="/transactions/11/receipts/15" target="blank" title="ABAGI      Ernest_Nguevese.pdf">
                                                        <i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a></span></td>
                                                    <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="my-2 form-control">
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Surgery notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Date</span>
                                                    <input class="form-control" type="text" name="date" value="" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Assitant Surgeon </span>
                                                <input class="form-control" name="assistantSurgeon">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Type of Aneasthesia</span>
                                                <input class="form-control" name="typeOfAneasthesia">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist</span>
                                                    <input class="form-control" name="aneasthetist">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Scrub Nurse</span>
                                                    <input class="form-control" name="scrubNurse">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Type of Operation</span>
                                                    <input class="form-control" name="typeOfOperation">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgical Procedure</span>
                                                    <textarea class="form-control" name="surgicalProcedure"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon's Notes</span>
                                                    <textarea class="form-control" name="surgeonsNotes"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist's Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Post Operarion <br> Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Anesthesiologist's Notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-assessment</span>
                                                    <input class="form-control" name="pre-assessment">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Indication</span>
                                                    <input class="form-control" name="indication">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgery</span>
                                                    <input class="form-control" name="surgery" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Plan</span>
                                                    <input class="form-control" name="plan" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-med</span>
                                                    <input class="form-control" name="pre-med">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baseline</span
                                                    <input class="form-control" name="baseline">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Cannulation</span>
                                                    <input class="form-control" name="cannulation">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-med</span>
                                                    <input class="form-control" name="pre-med">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Preloading</span>
                                                    <input class="form-control" name="preloading">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Induction</span>
                                                    <input class="form-control" name="induction" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Maintenance</span>
                                                    <input class="form-control" name="maintenance">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Infusion</span>
                                                    <input class="form-control" name="infusion">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Analgesics</span>
                                                    <input class="form-control" name="analgesics">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Transfusion</span>
                                                    <input class="form-control" name="transfusion">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Antibiotics</span>
                                                    <input class="form-control" name="antibiotics">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Maintenance</span>
                                                    <input class="form-control" name="maintenance">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">KOS</span>
                                                    <input class="form-control" name="kos" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EOS</span>
                                                    <input class="form-control" name="eos" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Immediate post-op</span>
                                                    <input class="form-control" name="immediatePostOp" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet time</span>
                                                    <input class="form-control" name="tourniquetTime" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet out</span>
                                                    <input class="form-control" name="tourniquetOut" >
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-semibold text-primary">Baby Details</span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baby out</span>
                                                    <input class="form-control" name="babyOut">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Anaesthetist</span>
                                                    <input class="form-control" name="anaesthetist" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="my-2 form-control">
                                        <span class="fw-bold text-primary"> Delivery Note </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Date</span>
                                                    <input class="form-control" type="date" name="date" value="" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Admission</span>
                                                    <input class="form-control" type="datetime-local" name="timeOfAdmission" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Delivery</span>
                                                    <input class="form-control" type="datetime-local" name="timeOfDelivery" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Mode of Delivery</span>
                                                    <input class="form-control" name="modeOfDelivery" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Lenght of Parity</span>
                                                    <input class="form-control" name="lengthOfParity" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Head Circumference</span>
                                                    <input class="form-control" name="headCircumference" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end my-2">
                                <span class="input-group-text">Dr Toby</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview"           data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close Consultation ${iteration}</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
                `
            } else {
                consultationReviewDiv.innerHTML += `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Consultation ${iteration}</span>
                    <i class="bi bi-chevron-double-down text-primary"></i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        <div class="mb-2 form-control">
                            <span class="fw-bold text-primary">Vital Signs</span>                                            
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
                                </table>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <span class="fw-bold text-primary">Consultation</span>                                            
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
                                    <textarea class="form-control" name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="3" readonly="readonly"></textarea>
                                </div>
                            </div> 
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="medicalHistoryLabel"> Past Medical/ <br> Surgical History</span>                            
                                        <textarea class="form-control" name="medicalHistory" id="medicalHistory" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="obGyneHistoryLabel">Obstetrics/<br>Gynecological <br> History</span>
                                        <textarea class="form-control" type="text" name="obGyneHistory" id="obGyneHistory" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                                        <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3"readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="diagnosisLabel"> Selected <br>ICD11 <br> Diagnosis </span>
                                        <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="3" readonly="readonly">${line.selectedDiagnosis ?? 'nill'}</textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="diagnosisLabel"> Addional <br> Diagnosis </span> 
                                        <textarea class="form-control additionalDiagnosis" type="text" name="additionalDiagnosis" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="physiciansPlanLabel"> Physicians Plan </span>
                                        <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="admitLabel"> Patient Status </span>
                                        <input class="form-control patientStatus" name="patientStatus" value="Out-Patient" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="wardLabel"> Ward </span>
                                        <input class="form-control ward" name="ward" value="Private Ward" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="my-2 form-control">
                                <span class="fw-bold text-primary"> Investigations </span>
                                <div class="row overflow-auto m-1">
                                    <table id="investigationTable" class="table table-hover align-middle table-sm bg-primary">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Requested</th>
                                                <th>Physician</th>
                                                <th>Test/Investigation</th>
                                                <th>Result</th>
                                                <th>Doc</th>
                                                <th>Upload</th>
                                                <th>Date</th>
                                                <th>Staff</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Lab</td>
                                                <td>12/9/23 11:02pm</td>
                                                <td>Dr Toby</td>
                                                <td>Malaria Parasite</td>
                                                <td>Pfall ++</td>
                                                <td><span class="position-relative"><a href="/transactions/11/receipts/15" target="blank" title="ABAGI Ernest_Nguevese.pdf">
                                                <i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a></span></td>
                                                <td><a role="button" class=""><i class="bi bi-upload"></i></td></a>
                                                <td>12/09/23</td>
                                                <td>Onjefu</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="my-2 form-control">
                                <span class="fw-bold text-primary"> Medication & Treatment </span>
                                <div class="row overflow-auto m-1">
                                    <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                        <thead>
                                            <tr>
                                                <th>Prescribed</th>
                                                <th>Treatment/Medication</th>
                                                <th>Dosaage</th>
                                                <th>Physician</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>12/09/2023 11:02pm</td>
                                                <td>N/S 500mls</td>
                                                <td>500mls 12hrly x2</td>
                                                <td>Dr Emma</td>
                                            </tr>
                                        </tbody>
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
                                                <input class="form-control" type="search" name="item" id="item" placeholder="search" autocomplete="off">
                                                <datalist name="item" type="text" class="decoration-none"></datalist>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 themed-grid-col col-xl-6">
                                            <div class="input-group mb-1">
                                                <span class="input-group-text" id="prescriptionLabel">Prescription/Instruction </span> 
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
                                                <th>Prescribed</th>
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
                                                <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>12/09/2023 11:15pm</td>
                                                <td>5% mls Syringe</td>
                                                <td></td>
                                                <td>4</td>
                                                <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>`}
                            <div class="d-flex justify-content-start my-2 gap-2">
                                <button type="button" id="fileBtn" class="btn btn-outline-primary">
                                File
                                <i class="bi bi-file-earmark-medical"></i>
                                </button>
                                <button type="button" id="surgeryBtn" class="btn btn-outline-primary">
                                    Surgery 
                                <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" id="deliveryBtn" class="btn btn-outline-primary">
                                    Delivery
                                <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                            <div class="extraInfoDiv">
                                <div class="my-2 form-control">
                                    <span class="fw-bold text-primary"> Other Documents </span>
                                    <div class="row overflow-auto m-1">
                                        <table id="otherDocumentsTable" class="table table-hover align-middle table-sm bg-primary">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Name</th>
                                                    <th>Document</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>12/09/2023</td>
                                                    <td>Excuse Duty</td>
                                                    <td><span class="position-relative"><a href="/transactions/11/receipts/15" target="blank" title="ABAGI      Ernest_Nguevese.pdf">
                                                        <i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a></span></td>
                                                    <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="my-2 form-control">
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Surgery notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Date</span>
                                                    <input class="form-control" type="text" name="date" value="" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Assitant Surgeon </span>
                                                <input class="form-control" name="assistantSurgeon">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Type of Aneasthesia</span>
                                                <input class="form-control" name="typeOfAneasthesia">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist</span>
                                                    <input class="form-control" name="aneasthetist">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Scrub Nurse</span>
                                                    <input class="form-control" name="scrubNurse">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Type of Operation</span>
                                                    <input class="form-control" name="typeOfOperation">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgical Procedure</span>
                                                    <textarea class="form-control" name="surgicalProcedure"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon's Notes</span>
                                                    <textarea class="form-control" name="surgeonsNotes"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist's Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Post Operarion <br> Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Anesthesiologist's Notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-assessment</span>
                                                    <input class="form-control" name="pre-assessment">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Indication</span>
                                                    <input class="form-control" name="indication">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgery</span>
                                                    <input class="form-control" name="surgery" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Plan</span>
                                                    <input class="form-control" name="plan" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-med</span>
                                                    <input class="form-control" name="pre-med">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baseline</span
                                                    <input class="form-control" name="baseline">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Cannulation</span>
                                                    <input class="form-control" name="cannulation">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-med</span>
                                                    <input class="form-control" name="pre-med">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Preloading</span>
                                                    <input class="form-control" name="preloading">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Induction</span>
                                                    <input class="form-control" name="induction" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Maintenance</span>
                                                    <input class="form-control" name="maintenance">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Infusion</span>
                                                    <input class="form-control" name="infusion">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Analgesics</span>
                                                    <input class="form-control" name="analgesics">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Transfusion</span>
                                                    <input class="form-control" name="transfusion">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Antibiotics</span>
                                                    <input class="form-control" name="antibiotics">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Maintenance</span>
                                                    <input class="form-control" name="maintenance">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">KOS</span>
                                                    <input class="form-control" name="kos" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EOS</span>
                                                    <input class="form-control" name="eos" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Immediate post-op</span>
                                                    <input class="form-control" name="immediatePostOp" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet time</span>
                                                    <input class="form-control" name="tourniquetTime" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet out</span>
                                                    <input class="form-control" name="tourniquetOut" >
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-semibold text-primary">Baby Details</span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baby out</span>
                                                    <input class="form-control" name="babyOut">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Anaesthetist</span>
                                                    <input class="form-control" name="anaesthetist" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="my-2 form-control">
                                        <span class="fw-bold text-primary"> Delivery Note </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Date</span>
                                                    <input class="form-control" name="date" value="" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Admission</span>
                                                    <input class="form-control" name="timeOfAdmission" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Delivery</span>
                                                    <input class="form-control" name="timeOfDelivery" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Mode of Delivery</span>
                                                    <input class="form-control" name="modeOfDelivery" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Lenght of Parity</span>
                                                    <input class="form-control" name="lengthOfParity" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Head Circumference</span>
                                                    <input class="form-control" name="headCircumference" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" >
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end my-2">
                                <span class="input-group-text">Dr Emannuel</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview"           data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close Consultation ${iteration}</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
                `
            } 
             
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

        //displayItemsList(reviewInvestigationAndManagementDiv, items)
        //console.log(reviewInvestigationAndManagementDiv)
        reviewInvestigationAndManagementDiv.classList.remove('d-none')
    })

    // review consultation item input
    reviewConsultationItemInput.addEventListener('keyup', function() {

        let records = items.filter(d => d.name.toLocaleLowerCase().includes(reviewConsultationItemInput.value.toLocaleLowerCase()) ? d : '')
        console.log(records)

        displayItemsList(reviewInvestigationAndManagementDiv, records)
        // get('/clients/list', {searchTerm :clientInput.value})
        //         .then(response => response.json())
        //         .then(response => displayItemsList(reviewInvestigationAndManagementDiv, items))
    })

    // review consultation loops
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteConsultationBtn')
        const updateInvestigationAndManagmentBtn    = event.target.closest('.updateInvestigationAndManagmentBtn')
        const updateInvestigationAndManagmentDiv    = document.querySelectorAll('.updateInvestigationAndManagmentDiv')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')
        const fileBtn                               = event.target.closest('#fileBtn')
        const deliveryBtn                           = event.target.closest('#deliveryBtn')

        if (deleteConsultationBtn) {
            if (confirm('If you delete this consultation you cannot get it back! Are you sure you want to delete?')) {

            }
        }

        if (updateInvestigationAndManagmentBtn) {
            updateInvestigationAndManagmentDiv.forEach(div => {

                if (div.getAttribute('data-id') === updateInvestigationAndManagmentBtn.getAttribute('data-id')) {
                    div.classList.toggle('d-none')
                    div.querySelector('#item').addEventListener('keyup', ()=> {
                        let records = items.filter(d => d.name.toLocaleLowerCase().includes(div.querySelector('#item').value.toLocaleLowerCase()) ? d : '')
                        displayItemsList(div, records)
                        console.log(div)
                        console.log(records)
                    })
                }
                
            })
        }

        if (surgeryBtn) {
            surgeryModal.show()
        }

        if (fileBtn) {
            fileModal.show()
        }

        if (deliveryBtn) {
            deliveryModal.show()
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
        clearDivValues(reviewInvestigationAndManagementDiv)
        reviewConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))
        
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
    //const dataListId = div.querySelector('#itemsList')

    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', 'itemsOption')
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)

        let previous = div.querySelectorAll('#itemsOption')
            let elBox = []
            previous.forEach(node => {
               elBox.push(node.dataset.id)
            })

            div.querySelector('#item').setAttribute('list', 'itemsList')
            div.querySelector('datalist').setAttribute('id', 'itemsList')
            !elBox.includes(option.dataset.id) ? div.querySelector('#itemsList').appendChild(option) : ''
    })
}
