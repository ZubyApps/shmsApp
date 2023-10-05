import { Modal, Collapse } from "bootstrap"
import * as ECT from '@whoicd/icd11ect'
import '@whoicd/icd11ect/style.css'
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList } from "./helpers"

window.addEventListener('DOMContentLoaded', function () {
    const newConsultationModal              = new Modal(document.getElementById('newConsultationModal'))
    const reviewConsultationModal           = new Modal(document.getElementById('reviewConsultationModal'))
    const surgeryModal                      = new Modal(document.getElementById('surgeryModal'))
    const fileModal                         = new Modal(document.getElementById('fileModal'))
    const deliveryModal                     = new Modal(document.getElementById('deliveryModal'))
    const newReviewModal                    = new Modal(document.getElementById('newReviewModal'))
    const specialistConsultationModal       = new Modal(document.getElementById('specialistConsultationModal'))

    const newConsultationBtn                = document.querySelector('.newConsultationBtn')
    const reviewConsultationBtn             = document.querySelector('.reviewConsultationBtn')

    const saveNewConsultationBtn            = document.querySelector('#saveNewConsultationBtn')
    const saveReviewConsultationBtn         = document.querySelector('#saveReviewConsultationBtn')
    const saveSpecialistConsultationBtn     = document.querySelector('#saveSpecialistConsultationBtn')

    const addKnownClinicalInfoBtn           = newConsultationModal._element.querySelector('.addKnownClinicalInfoBtn')
    const reviewAddKnownClinicalInfoBtn     = reviewConsultationModal._element.querySelector('.reviewKnownClinicalInfoBtn')

    const reviewPatientbtn                  = reviewConsultationModal._element.querySelector('#reviewPatientBtn')
    const specialistConsultationbtn         = reviewConsultationModal._element.querySelector('#specialistConsultationBtn')

    const newConsultationDiv                = document.querySelector('#newConsultationDiv')
    const reviewConsultationDiv             = document.querySelector('#reviewConsultationDiv')
    const consultationReviewDiv             = document.querySelector('#consultationReviewDiv')
    const specialistConsultationDiv         = document.querySelector('#specialistConsultationDiv')

    const newInvestigationAndManagementDiv  = document.querySelector('.newInvestigationAndManagmentDiv')
    const reviewInvestigationAndManagementDiv = document.querySelector('.reviewInvestigationAndManagmentDiv')
    const specialistConsultationInvestigationAndManagementDiv = document.querySelector('.specialistConsultationInvestigationAndManagmentDiv')

    const investigationAndManagmentDiv      = document.querySelectorAll('.investigationAndManagementDiv')
    console.log(investigationAndManagmentDiv)
    const ItemInput = document.querySelectorAll('#item')
    const addInvestigationAndManagmentBtn   = document.querySelectorAll('#addInvestigationAndManagmentBtn')


    // Auto textarea adjustment
    const textareaHeight = 90;
    const textarea = document.getElementsByTagName("textarea");

        for (let i = 0; i < textarea.length; i++) {
        if (textarea[i].value == '') {
            textarea[i].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;");
        } else {
            textarea[i].setAttribute("style", "height:" + (textarea[i].scrollHeight) + "px;overflow-y:hidden;");
        }
        textarea[i].addEventListener("input", OnInput, false);
        }

    // ICD11settings
    const mySettings = { apiServerUrl: "https://icd11restapi-developer-test.azurewebsites.net" }


    // ICD11 callbacks
    const myCallbacks = {
        selectedEntityFunction: (selectedEntity) => {
            document.querySelector('.selectedDiagnosis-' + selectedEntity.iNo).value += selectedEntity.code + '-' + selectedEntity.selectedText + '\r\n\n'
            document.querySelector('.selectedDiagnosis-' + selectedEntity.iNo).dispatchEvent(new Event('input', { bubbles: true }))
            ECT.Handler.clear(selectedEntity.iNo)
        }
    }

    // ICD11 handler
    ECT.Handler.configure(mySettings, myCallbacks)

    // NEW CONSULTATION MODAL CODE 

    // show new consultation modal
    newConsultationBtn.addEventListener('click', function () {
        //displayItemsList(newInvestigationAndManagementDiv, items)
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
     newConsultationModal._element.querySelector('#addVitalsignsBtn').addEventListener('click', function () {
        newConsultationModal._element.querySelector('.addVitalsignsDiv').classList.toggle('d-none')
    })
   
    // save new consultation
    saveNewConsultationBtn.addEventListener('click', function () {
        console.log(getConsultationDivData(newConsultationDiv))

        toggleAttributeLoop(querySelectAllTags(newConsultationDiv, ['input, select, textarea']), 'disabled')

        saveNewConsultationBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? 
        saveNewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : 
        saveNewConsultationBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'

        newInvestigationAndManagementDiv.classList.remove('d-none')
    })


    // tasks to run when closing new consultation modal
    newConsultationModal._element.addEventListener('hide.bs.modal', function(event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        newInvestigationAndManagementDiv.classList.add('d-none')
        saveNewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        removeAttributeLoop(querySelectAllTags(newConsultationDiv, ['input, select, textarea']), 'disabled')
        clearDivValues(newConsultationDiv)
        clearDivValues(newInvestigationAndManagementDiv)
        for (let t = 0; t < newConsultationDiv.getElementsByTagName("textarea").length; t++){
            newConsultationDiv.getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // REVIEW CONSULTATION MODAL CODE

    // Open review consultation modal and returning a loop of all consultations for the given pattient
    reviewConsultationBtn.addEventListener('click', function () {
        let iteration = 0
        let count = 0
        consultationDetails.data.forEach(line => {
            iteration++

            if (iteration > 1) {
                count++
                consultationReviewDiv.innerHTML += `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Review ${stringToRoman(count)}</span>
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
                                        <span class="input-group-text" id="complainLabel">Complain</span> 
                                        <textarea class="form-control" name="complain" id="complain" cols="10" rows="3" readonly="readonly">${line.complain}</textarea>
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
                                <button type="button" class="btn btn-primary updateInvestigationAndManagmentBtn" data-btn="${iteration}">
                                    Update Investigation & Managment
                                </button>
                            </div> 
                            <div class="investigationAndManagmentDiv mt-2 d-none" data-div="${iteration}">
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
                                        <button type="button" id="addInvestigationAndManagmentBtn" data-btn="${iteration}" class="btn btn-primary">
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
                                    <span class="fw-semibold fs-5 mb-2"> Surgery Details </span>
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Surgeon's Notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Date</span>
                                                    <input class="form-control" type="text" name="date" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Assitant Surgeon </span>
                                                <input class="form-control" name="assistantSurgeon" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Type of Aneasthesia</span>
                                                <input class="form-control" name="typeOfAneasthesia" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist</span>
                                                    <input class="form-control" name="aneasthetist" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Scrub Nurse</span>
                                                    <input class="form-control" name="scrubNurse" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Type of Operation</span>
                                                    <input class="form-control" name="typeOfOperation" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgical Procedure</span>
                                                    <textarea class="form-control" name="surgicalProcedure" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon's Notes</span>
                                                    <textarea class="form-control" name="surgeonsNotes" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist's Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Post Operarion <br> Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Anesthesiologist's Notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-assessment</span>
                                                    <input class="form-control" name="pre-assessment" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Indication</span>
                                                    <input class="form-control" name="indication" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgery</span>
                                                    <input class="form-control" name="surgery" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Plan</span>
                                                    <input class="form-control" name="plan" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-med</span>
                                                    <input class="form-control" name="pre-med" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baseline</span>
                                                    <input class="form-control" name="baseline" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Cannulation</span>
                                                    <input class="form-control" name="cannulation" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Preloading</span>
                                                    <input class="form-control" name="preloading" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Induction</span>
                                                    <input class="form-control" name="induction" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Maintenance</span>
                                                    <textarea class="form-control" name="maintenance" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Infusion</span>
                                                    <input class="form-control" name="infusion" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Analgesics</span>
                                                    <input class="form-control" name="analgesics" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Transfusion</span>
                                                    <input class="form-control" name="transfusion" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Antibiotics</span>
                                                    <input class="form-control" name="antibiotics" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">KOS</span>
                                                    <input class="form-control" name="kos" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EOS</span>
                                                    <input class="form-control" name="eos" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Immediate Post-Op</span>
                                                    <input class="form-control" name="immediatePostOp" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet time</span>
                                                    <input class="form-control" name="tourniquetTime" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet out</span>
                                                    <input class="form-control" name="tourniquetOut" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-semibold text-primary">Baby Details</span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baby out</span>
                                                    <input class="form-control" name="babyOut" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Anaesthetist</span>
                                                    <input class="form-control" name="anaesthetist" readonly>
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
                                                    <input class="form-control" type="date" name="date" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Admission</span>
                                                    <input class="form-control" name="timeOfAdmission" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Delivery</span>
                                                    <input class="form-control" name="timeOfDelivery" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Mode of Delivery</span>
                                                    <input class="form-control" name="modeOfDelivery" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Lenght of Parity</span>
                                                    <input class="form-control" name="lengthOfParity" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Head Circumference</span>
                                                    <input class="form-control" name="headCircumference" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" readonly>
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
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close Review ${stringToRoman(count)}</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
                `
            } else {
                consultationReviewDiv.innerHTML += `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Initial Consultation</span>
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
                                <button type="button" class="btn btn-primary updateInvestigationAndManagmentBtn" data-btn="${iteration}">
                                    Update Investigation & Managment
                                </button>
                            </div> 
                            <div class="investigationAndManagmentDiv d-none" data-div="${iteration}">
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
                                        <button type="button" id="addInvestigationAndManagmentBtn" data-btn="${iteration}" class="btn btn-primary">
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
                                    <span class="fw-semibold fs-5 mb-2"> Surgery Details </span>
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Surgery Notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Date</span>
                                                    <input class="form-control" type="text" name="date" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Assitant Surgeon </span>
                                                <input class="form-control" name="assistantSurgeon" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                <span class="input-group-text">Type of Aneasthesia</span>
                                                <input class="form-control" name="typeOfAneasthesia" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist</span>
                                                    <input class="form-control" name="aneasthetist" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Scrub Nurse</span>
                                                    <input class="form-control" name="scrubNurse" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Type of Operation</span>
                                                    <input class="form-control" name="typeOfOperation" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgical Procedure</span>
                                                    <textarea class="form-control" name="surgicalProcedure" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon's Notes</span>
                                                    <textarea class="form-control" name="surgeonsNotes" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Aneasthetist's Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Post Operarion <br> Notes</span>
                                                    <textarea class="form-control" name="assistantSurgeon" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary"> Anesthesiologist's Notes </span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-assessment</span>
                                                    <input class="form-control" name="pre-assessment" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Indication</span>
                                                    <input class="form-control" name="indication" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgery</span>
                                                    <input class="form-control" name="surgery" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Plan</span>
                                                    <input class="form-control" name="plan" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Pre-med</span>
                                                    <input class="form-control" name="pre-med" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baseline</span>
                                                    <input class="form-control" name="baseline" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Cannulation</span>
                                                    <input class="form-control" name="cannulation" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Preloading</span>
                                                    <input class="form-control" name="preloading" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Induction</span>
                                                    <input class="form-control" name="induction" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Maintenance</span>
                                                    <textarea class="form-control" name="maintenance" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Infusion</span>
                                                    <input class="form-control" name="infusion" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Analgesics</span>
                                                    <input class="form-control" name="analgesics" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Transfusion</span>
                                                    <input class="form-control" name="transfusion" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Antibiotics</span>
                                                    <input class="form-control" name="antibiotics" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">KOS</span>
                                                    <input class="form-control" name="kos" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EOS</span>
                                                    <input class="form-control" name="eos" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Immediate post-op</span>
                                                    <input class="form-control" name="immediatePostOp" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet time</span>
                                                    <input class="form-control" name="tourniquetTime" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Tourniquet out</span>
                                                    <input class="form-control" name="tourniquetOut" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-semibold text-primary">Baby Details</span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Baby out</span>
                                                    <input class="form-control" name="babyOut" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Surgeon</span>
                                                    <input class="form-control" name="surgeon" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Anaesthetist</span>
                                                    <input class="form-control" name="anaesthetist" readonly>
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
                                                    <input class="form-control" name="date" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Admission</span>
                                                    <input class="form-control" name="timeOfAdmission" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Delivery</span>
                                                    <input class="form-control" name="timeOfDelivery" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Apgar Score</span>
                                                    <input class="form-control" name="apgarScore" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Weight</span>
                                                    <input class="form-control" name="weight" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Mode of Delivery</span>
                                                    <input class="form-control" name="modeOfDelivery" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Lenght of Parity</span>
                                                    <input class="form-control" name="lengthOfParity" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Head Circumference</span>
                                                    <input class="form-control" name="headCircumference" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" readonly>
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
                    <span class="mx-2">Close Consultation</span>
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

    // manipulating the vital signs div on new review modal
    newReviewModal._element.querySelector('#addVitalsignsBtn').addEventListener('click', function () {
        newReviewModal._element.querySelector('.addVitalsignsDiv').classList.toggle('d-none')
    })

    // open review patient modal
    reviewPatientbtn.addEventListener('click', function () {
        newReviewModal.show()
    })

    //save review consultation
    saveReviewConsultationBtn.addEventListener('click', function () {
        console.log(getConsultationDivData(reviewConsultationDiv))

        toggleAttributeLoop(querySelectAllTags(reviewConsultationDiv, ['input, select, textarea']), 'disabled')

        saveReviewConsultationBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? 
        saveReviewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : 
        saveReviewConsultationBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'

        reviewInvestigationAndManagementDiv.classList.remove('d-none')
    })

    // tasks to run when closing new review modal 
    newReviewModal._element.addEventListener('hide.bs.modal', function () {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        saveReviewConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        removeAttributeLoop(querySelectAllTags(reviewConsultationDiv, ['input, select, textarea']), 'disabled')
        clearDivValues(reviewConsultationDiv)
        clearDivValues(reviewInvestigationAndManagementDiv)
        reviewConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))
        reviewInvestigationAndManagementDiv.classList.add('d-none')
        for (let t = 0; t < reviewConsultationDiv.getElementsByTagName("textarea").length; t++){
            reviewConsultationDiv.getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // open specialist consultation modal
    specialistConsultationbtn.addEventListener('click', function () {
        specialistConsultationModal.show()
    })

    // manipulating the vital signs div on specialist consultation modal
    specialistConsultationModal._element.querySelector('#addVitalsignsBtn').addEventListener('click', function () {
        specialistConsultationModal._element.querySelector('.addVitalsignsDiv').classList.toggle('d-none')
    })

    //save specialist consultation
    saveSpecialistConsultationBtn.addEventListener('click', function () {
        console.log(getConsultationDivData(specialistConsultationDiv))

        toggleAttributeLoop(querySelectAllTags(specialistConsultationDiv, ['input, select, textarea']), 'disabled')

        saveSpecialistConsultationBtn.innerHTML === '<i class="bi bi-pencil"></i> Edit' ? 
        saveSpecialistConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save` : 
        saveSpecialistConsultationBtn.innerHTML = '<i class="bi bi-pencil"></i> Edit'

        specialistConsultationInvestigationAndManagementDiv.classList.remove('d-none')
    })

    // tasks to run when closing specialist consultation modal
    specialistConsultationModal._element.addEventListener('hide.bs.modal', function (event) {
        if (!confirm('Have you saved? You will loose all unsaved data')) {
            event.preventDefault()
            return
        }
        saveSpecialistConsultationBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i> Save`
        removeAttributeLoop(querySelectAllTags(specialistConsultationDiv, ['input, select, textarea']), 'disabled')
        clearDivValues(specialistConsultationDiv)
        clearDivValues(specialistConsultationInvestigationAndManagementDiv)
        specialistConsultationModal._element.querySelectorAll('#itemsList').forEach(list => clearItemsList(list))
        specialistConsultationInvestigationAndManagementDiv.classList.add('d-none')
        for (let t = 0; t < specialistConsultationDiv.getElementsByTagName("textarea").length; t++){
            specialistConsultationDiv.getElementsByTagName("textarea")[t].setAttribute("style", "height:" + textareaHeight + "px;overflow-y:hidden;")
        }
    })

    // review consultation item input
    ItemInput.forEach(input => {
        getItemsFromInput(input, items)
    })

    addInvestigationAndManagmentBtn.forEach(addBtn => {
        addBtn.addEventListener('click', () => {
            investigationAndManagmentDiv.forEach(div => {
                if (addBtn.dataset.btn === div.dataset.div) {
                    console.log(getConsultationDivData(div))
                }
            })
        })
    })

    // review consultation loops
    document.querySelector('#consultationReviewDiv').addEventListener('click', function (event) {
        const deleteConsultationBtn                 = event.target.closest('#deleteConsultationBtn')
        const updateInvestigationAndManagmentBtn    = event.target.closest('.updateInvestigationAndManagmentBtn')
        const updateInvestigationAndManagmentDiv    = document.querySelectorAll('.investigationAndManagmentDiv')
        const addInvestigationAndManagmentBtn       = event.target.closest('#addInvestigationAndManagmentBtn')
        const surgeryBtn                            = event.target.closest('#surgeryBtn')
        const fileBtn                               = event.target.closest('#fileBtn')
        const deliveryBtn                           = event.target.closest('#deliveryBtn')

        if (deleteConsultationBtn) {
            if (confirm('If you delete this consultation you cannot get it back! Are you sure you want to delete?')) {

            }
        }

        if (updateInvestigationAndManagmentBtn) {
            updateInvestigationAndManagmentDiv.forEach(div => {

                if (div.getAttribute('data-div') === updateInvestigationAndManagmentBtn.getAttribute('data-btn')) {
                    div.classList.toggle('d-none')
                    getItemsFromInput(div.querySelector('#item'), items)
                }
                
            })
        }

        if (addInvestigationAndManagmentBtn) {
            console.log('clicked')
            updateInvestigationAndManagmentDiv.forEach(div => {
                if (div.dataset.div === addInvestigationAndManagmentBtn.dataset.btn) {
                console.log(getConsultationDivData(div))
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
        
        document.querySelectorAll('#collapseReviewDiv, #collapseSpecialistConsultation').forEach(el => {
            let collapseable = new Collapse(el, {toggle: false})
            collapseable.hide()
        })
        
    })

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
      
    //const input = "2013"; 
    //const result = stringToRoman(parseInt(input)); 
    

})

function OnInput(e) {
  this.scrollHeight < 90 ? this.style.height = 90 + "px" : this.style.height = (this.scrollHeight) + "px";
}

function getItemsFromInput(input, data) {
    input.addEventListener('keyup', function() {
        let records = data.filter(d => d.name.toLocaleLowerCase().includes(input.value.toLocaleLowerCase()) ? d : '')
        displayItemsList(input.parentNode, records)
    })
}

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
