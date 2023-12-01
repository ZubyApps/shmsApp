const surgeryNote = (surgery) => {
    return  `
                <div>
                    <div class="mb-2">
                        <span class="fw-bold text-primary"> Surgeon's Notes </span>
                        <div class="row">
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Date</span>
                                    <input class="form-control" type="text" name="date" value="${surgery.date ? surgery.date : ''}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Surgeon</span>
                                    <input class="form-control" name="surgeon"  value="${surgery.surgeon}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                <span class="input-group-text">Assitant Surgeon </span>
                                <input class="form-control" name="assistantSurgeon" value="${surgery.assistanSurgeon}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                <span class="input-group-text">Type of Aneasthesia</span>
                                <input class="form-control" name="typeOfAneasthesia" value="${surgery.typeOfAneasthesia}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Aneasthetist</span>
                                    <input class="form-control" name="aneasthetist" value="${surgery.aneasthetist}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Scrub Nurse</span>
                                    <input class="form-control" name="scrubNurse" value="${surgery.srubNurse}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Type of Operation</span>
                                    <input class="form-control" name="typeOfOperation" value="${surgery.typeOfOperation}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Surgical Procedure</span>
                                    <textarea class="form-control" name="surgicalProcedure" value="${surgery.surgicalProcedure}" readonly></textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Surgeon's Notes</span>
                                    <textarea class="form-control" name="surgeonsNotes" value="${surgery.surgeonsNotes}" readonly></textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Aneasthetist's Notes</span>
                                    <textarea class="form-control" name="aneasthetistNotes" readonly>${surgery.aneasthetistNotes}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Post Operarion <br> Notes</span>
                                    <textarea class="form-control" name="postOperationNotes" readonly>${surgery.postOperationNotes}</textarea>
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
                                    <input class="form-control" name="pre-assessment" value="${surgery.postOperationNotes}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Indication</span>
                                    <input class="form-control" name="indication" value="${surgery.indication}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Surgery</span>
                                    <input class="form-control" name="surgery" value="${surgery.surgery}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Plan</span>
                                    <input class="form-control" name="plan" value="${surgery.plan}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Pre-med</span>
                                    <input class="form-control" name="preMed" value="${surgery.preMed}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Baseline</span>
                                    <input class="form-control" name="baseLine" value="${surgery.baseLine}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Cannulation</span>
                                    <input class="form-control" name="cannulation" value="${surgery.cannulation}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Preloading</span>
                                    <input class="form-control" name="preloading" value="${surgery.preloading}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Induction</span>
                                    <input class="form-control" name="induction" value="${surgery.induction}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Maintenance</span>
                                    <textarea class="form-control" name="maintenance" readonly>${surgery.maintenance}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Infusion</span>
                                    <input class="form-control" name="infusion" value="${surgery.infusion}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Analgesics</span>
                                    <input class="form-control" name="analgesics" value="${surgery.analgesics}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Transfusion</span>
                                    <input class="form-control" name="transfusion" value="${surgery.transfusion}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Antibiotics</span>
                                    <input class="form-control" name="antibiotics" value="${surgery.anibiotics}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">KOS</span>
                                    <input class="form-control" name="kos" value="${surgery.kos}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">EOS</span>
                                    <input class="form-control" name="eos" value="${surgery.eos}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">EBL</span>
                                    <input class="form-control" name="ebl" value="${surgery.ebl}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Immediate Post-Op</span>
                                    <input class="form-control" name="immediatePostOp" value="${surgery.immediatePostOp}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Tourniquet time</span>
                                    <input class="form-control" name="tourniquetTime" value="${surgery.tourniquetTime}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Tourniquet out</span>
                                    <input class="form-control" name="tourniquetOut" value="${surgery.tourniquetOut}" readonly>
                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold text-primary">Baby Details</span>
                        <div class="row">
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Baby out</span>
                                    <input class="form-control" name="babyOut" value="${surgery.babyOut}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Sex</span>
                                    <input class="form-control" name="sex" value="${surgery.sex}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Apgar Score</span>
                                    <input class="form-control" name="apgarScore" value="${surgery.apgarScore}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Weight</span>
                                    <input class="form-control" name="weight" value="${surgery.weight}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Surgeon</span>
                                    <input class="form-control" name="csSurgeon" value="${surgery.csSurgeon}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">Anaesthetist</span>
                                    <input class="form-control" name="anaesthetist" value="${surgery.csAneasthetist}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `
        }


const deliveryNote = (delivery) => {
        return  `
                    <div class="row">
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Date</span>
                                <input class="form-control" name="date" value="${delivery.date ?? ''}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Time of Admission</span>
                                <input class="form-control" name="timeOfAdmission" value="${delivery.timeOfAdmission ?? ' '}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Time of Delivery</span>
                                <input class="form-control" name="timeOfDelivery" value="${delivery.timeOfDelivery}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Apgar Score</span>
                                <input class="form-control" name="apgarScore" value="${delivery.apgarScore}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Weight</span>
                                <input class="form-control" name="weight" value="${delivery.weight}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Mode of Delivery</span>
                                <input class="form-control" name="modeOfDelivery" value="${delivery.modeOfDelivery}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Lenght of Parity</span>
                                <input class="form-control" name="lengthOfParity" value="${delivery.lengthOfParity}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Head Circumference</span>
                                <input class="form-control" name="headCircumference" value="${delivery.headCircumference}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Sex</span>
                                <input class="form-control" name="sex" value="${delivery.sex}" readonly>
                            </div>
                        </div>
                        <div class="col-xl-4 themed-grid-col">
                            <div class="input-group mb-1">
                                <span class="input-group-text">EBL</span>
                                <input class="form-control" name="ebl" value="${delivery.ebl}" readonly>
                            </div>
                        </div>
                    </div>
                `
            }

const vitalsignsTable = (line) => {
        return  `
                <div class="mb-2 form-control">
                <span class="fw-bold text-primary">Vital Signs</span>                                            
                <div class="row overflow-auto m-1">
                    <table id="vitalsignsTable${line.visitId}" class="table table-hover align-middle table-sm">
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
                        </tbody>
                    </table>
                </div>
            </div>
                `
}

const files = (line) => {
            return `
            <table id="otherDocumentsTable${line.id}" class="table table-hover align-middle table-sm bg-primary">
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
        </table>`
}

const updateInvestigationAndManagement = (length, iteration, line) => {
    return ` 
                <div class="investigationAndManagmentDiv mt-2 active" data-div="${iteration}" data-goto=#gotoResource${iteration}>
                    <div class="d-flex justify-content-center">
                        <button type="button" id="updateResourceListBtn" data-conid="${line.id}" data-visitid="${line.visitId}" data-btn="${iteration}" class="btn btn-primary">
                            Update Resources
                            <i class="bi bi-prescription"></i>
                        </button>
                    </div>
                    <div class="my-2 form-control resourceDiv d-none" id="gotoResource${iteration}">
                        <span class="fw-semibold">Resource Items</span>
                        <div class="mb-2 form-control active">
                            <table id="prescriptionTable${line.id}" data-id="${line.id}" class="table table-hover align-middle table-sm prescriptionTable">
                                <thead>
                                    <tr>
                                        <th>Added</th>
                                        <th>Resource</th>
                                        <th>Prescription</th>
                                        <th>Qty</th>
                                        <th>Note</th>
                                        <th>By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        ${length > iteration ? '': `
                        <div class="my-2">
                            <div class="row">
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="resourceLabel">Medical Resource</span> 
                                        <input class="form-control resource" type="search" name="resource" id="resource" data-input="${iteration}" placeholder="search" autocomplete="" list="resourceList${iteration}">
                                        <datalist name="resource" class="datalistEl"  type="text" id="resourceList${iteration}"></datalist>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6" id="pres">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="prescriptionLabel">Prescription</span> 
                                        <input class="form-control" type="text" name="prescription" id="prescription" placeholder="eg: 5mg BD x5" autocomplete="">
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6" id="qty">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="quantityLabel"> Quantity</span> 
                                        <input class="form-control" type="number" name="quantity" id="quantity" placeholder="" autocomplete="">
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="notesLabel">Note</span> 
                                        <input class="form-control" name="note" id="notes" placeholder="" autocomplete="">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" id="addInvestigationAndManagmentBtn" data-conid="${line.id}" data-visitid="${line.visitId}" data-btn="${iteration}" class="btn btn-primary">
                                    add
                                <i class="bi bi-prescription"></i>
                                </button>
                            </div>
                            <div class="toast align-items-center shadow-none border-0" id="saveUpdateInvestigationAndManagementToast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-body">
                                    <h6 class="text-primary">Successful</h6>
                                </div>  
                            </div>
                        </div>`}
                    </div>
                </div>`
}

const investigations = (line) => {
            return `
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Investigations </span>
                    <div class="row overflow-auto m-1">
                        <table id="investigationTable${line.id}" data-id="${line.id}" class="table table-hover align-middle table-sm investigationTable">
                            <thead>
                                <tr>
                                    <th>Requested</th>
                                    <th>Investigation</th>
                                    <th>By</th>
                                    <th>Result</th>
                                    <th>Sent</th>
                                    <th>Staff</th>
                                    <th>Upload</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
        `
}

const review = (count, line) => {
    return `<div class="form-control">
                <span class="fw-bold text-primary">Consultation Review ${count}</span>                                            
                <div class="row">
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="specialistDesignationLabel"> Consultant Specialist<br>Name & Designation</span>
                            <input class="form-control" name="consultantSpecialist" value="${line.consultantSpecialist}" ${line.consultantSpecialist ? 'readonly' : 'disabled'}>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="complainLabel">Complain</span> 
                            <textarea class="form-control" name="complaint" id="complaint" cols="10" rows="2" readonly="readonly">${line.complaint}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                            <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="2" readonly="readonly">${line.examinationFindings}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Selected <br>ICD11 Diagnosis </span>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" value="" readonly="readonly">${line.selectedDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Assessment </span>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="2" readonly="readonly">${line.assessment}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="physiciansPlanLabel"> Physicians Plan </span>
                            <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="2" readonly="readonly">${line.plan}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="admitLabel"> Patient Status </span>
                            <input class="form-control patientStatus" name="patientStatus" value="${line.status}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="wardLabel"> Ward </span>
                            <input class="form-control ward" name="ward" value="${line.ward}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="bedLabel"> Bed </span>
                            <input class="form-control bed" name="bed" value="${line.bedNumber}" disabled>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end my-2">
                        <span class="input-group-text">${line.doctor}</span>
                    </div>
                </div>
            </div>
        `
}

const consultation = (line) => {
    return  `
            <div class="form-control">
                <span class="fw-bold text-primary">Consultation</span>                                            
                <div class="row mt-1">
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="specialistDesignationLabel"> Consultant Specialist<br>Name &amp; Designation</span>
                            <input class="form-control" name="consultantSpecialist" value="${line.consultantSpecialist}" ${line.consultantSpecialist ? 'readonly' : 'disabled'}>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="presentingComplainLabel">Presenting <br>Complain</span> 
                            <textarea class="form-control" name="presentingComplain" id="presentingComplain" cols="10" rows="2" readonly="readonly">${line.presentingComplain}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                    <div class="input-group mb-1">
                        <span class="input-group-text" id="historyOfPresentingComplainLabel">History of <br> Presenting Complain </span>
                        <textarea class="form-control" name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="2" readonly="readonly">${line.historyOfPresentingComplain}</textarea>
                    </div>
                </div> 
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="medicalHistoryLabel"> Past Medical/ <br> Surgical History</span>                            
                            <textarea class="form-control" name="medicalHistory" id="medicalHistory" cols="10" rows="2" readonly="readonly">${line.pastMedicalHistory}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="obGynHistoryLabel">Obstetrics/<br>Gynecological History</span>
                            <textarea class="form-control" type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="2" readonly="readonly">${line.obGynHistory}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings</span>                                                    
                            <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="2"readonly="readonly">${line.examinationFindings}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Selected <br>ICD11 Diagnosis </span>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="2" readonly="readonly">${line.selectedDiagnosis ?? 'nill'}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Addional <br> Diagnosis </span> 
                            <textarea class="form-control additionalDiagnosis" type="text" name="additionalDiagnosis" cols="10" rows="2" readonly="readonly"></textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="physiciansPlanLabel"> Physicians Plan </span>
                            <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="2" readonly="readonly">${line.plan}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2 admissionStatus">
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="admitLabel"> Patient Status </span>
                            <input class="form-control patientStatus" name="patientStatus" value="${line.status}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="wardLabel"> Ward </span>
                            <input class="form-control ward" name="ward" value="${line.ward}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="bedLabel"> Bed </span>
                            <input class="form-control bed" name="bed" value="${line.bedNumber}" disabled>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end my-2">
                        <span class="input-group-text">${line.doctor}</span>
                    </div>
                </div>
            </div>
        `
}

const AncConsultation = (line, iteration, count) => {
    return  `
            <div class="form-control">
                <span class="fw-bold text-primary">Consultation ${iteration > 1 ? ' Review '+count : ''}</span>                                            
                <div class="row mt-1">
                    <div class="col-xl-4 themed-grid-col col-xl-12">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="specialistDesignationLabel"> Consultant (Name&Designation)</span>
                            <input class="form-control" name="consultantSpecialist" value="${line.consultantSpecialist}" ${line.consultantSpecialist ? 'readonly' : 'disabled'}>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="lmpLabel">LMP</span> 
                            <input class="form-control" name="lmp" id="lmp" value="${line.lmp ?? ''}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="eddLabel">EDD</span> 
                            <input class="form-control" name="edd" id="edd" value="${line.edd ?? ''}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="egaLabel">EGA</span> 
                            <input class="form-control" name="ega" id="ega"value="${line.ega}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="fetalHeartRateLabel">Fetal Heart Rate</span> 
                            <input class="form-control" name="fetalHeartRate" id="fetalHeartRate" value="${line.fetalHeartRate}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="heightOfFondusLabel">Height of Fundus</span> 
                            <input class="form-control" name="heightOfFundus" id="heightOfFundus" value="${line.heightOfFundus}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="presentationAndPositionLabel">Presentation&Position</span> 
                            <input class="form-control" name="presentationAndPosition" id="presentationAndPosition" value="${line.presentationAndPosition}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="relationOfPresentingPartToBrimLabel">Relation of Presenting <br> Part to Brim</span> 
                            <input class="form-control" name="relationOfPresentingPartToBrim" id="relationOfPresentingPartToBrim"  value="${line.relationOfPresentingPartToBrim}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="obGynHistoryLabel">Obstetrics/<br>Gynecological History</span>
                            <textarea class="form-control" type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="2" value="" readonly="readonly">${line.obGynHistory}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="ultrasoundReportLabel"> Ultrasound Report </span>                                                    
                            <textarea class="form-control" type="text" name="ultrasoundReport" id="ultrasoundReport" cols="10" rows="2" value="" readonly="readonly">${line.ultrasoundReport}</textarea>
                        </div>
                    </div>                    
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="selectedDiagnosisLabel"> Selected ICD11 <br> Diagnosis </span>
                            <textarea class="form-control selectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="2" readonly="readonly">${line.selectedDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-12">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Physician's Notes </span> 
                            <textarea class="form-control notes" type="text" name="notes" cols="10" rows="2" readonly="readonly">${line.notes}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="remarksLabel"> Remarks </span>
                            <textarea class="form-control" type="text" name="remarks" id="remarks" cols="10" rows="2" readonly="readonly">${line.remarks}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="physiciansPlanLabel"> Physicians Plan </span>
                            <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="2" readonly="readonly">${line.plan}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="admitLabel"> Patient Status </span>
                            <input class="form-control patientStatus" name="patientStatus" value="${line.status}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="wardLabel"> Ward </span>
                            <input class="form-control ward" name="ward" value="${line.ward}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="bedLabel"> Bed </span>
                            <input class="form-control bed" name="bed" value="${line.bedNumber}" disabled>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end my-2">
                        <span class="input-group-text">${line.doctor}</span>
                    </div>
                </div>
            </div>
        `
    }

const medicationAndTreatment = (line) => {
    return `
            <div class="my-2 form-control">
                <span class="fw-bold text-primary">Treatment & Medication</span>
                <div class="row overflow-auto m-1">
                    <table id="treatmentTable${line.id}" data-id="${line.id}" class="table table-hover table-sm treatmentTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Treatment</th>
                                <th>Prescription</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Billed</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        `
}

const medicationAndTreatmentNurses = (line) => {
    return `
            <div class="my-2 form-control">
                <span class="fw-bold text-primary">Treatment & Medication</span>
                <div class="row overflow-auto m-1">
                    <table id="nurseTreatmentTable${line.id}" data-id="${line.id}" class="table table-hover table-sm nurseTreatmentTable">
                        <thead>
                            <tr>
                                <th>Treatment</th>
                                <th>Prescription</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Create</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        `
}

const updateAdmissionStatus = (line, iteration) => {
    return     `
    <div class="my-2 form-control" id="wardAndBedDiv" data-div="${iteration}">
        <span class="fw-bold text-primary">Update Patient's Admission Status </span>
        <div class="row mt-2">
            <div class="col-xl-4 themed-grid-col col-xl-4">
                <div class="input-group mb-1">
                    <span class="input-group-text" id="admitLabel">Admit?</span>
                    <select class="form-select form-select-md" name="admit" id="admit">
                        <option value="">Select</option>
                        <option value="Outpatient">No</option>
                        <option value="Observation">Observation</option>
                        <option value="Inpatient">Yes</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-4 themed-grid-col col-xl-4">
                <div class="input-group mb-1">
                    <span class="input-group-text" id="wardTypeLabel"> Ward Type </span>
                    <select class="form-select form-select-md" name="ward" id="ward">
                        <option value="">Select Ward</option>
                        <option value="FW">Female Ward</option>
                        <option value="MW">Male Ward</option>
                        <option value="PW 1">Private Ward 1</option>
                        <option value="PW 2">Private Ward 2</option>
                        <option value="PW 3">Private Ward 3</option>
                        <option value="PW 4">Private Ward 4</option>
                        <option value="PW 5">Private Ward 5</option>
                        <option value="PW 6">Private Ward 6</option>
                        <option value="Old Ward">Old Ward</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-4 themed-grid-col col-xl-4">
                <div class="input-group mb-1">
                    <span class="input-group-text" id="bedNumberLabel"> Bed Number </span>
                    <select class="form-select form-select-md" name="bedNumber" id="bedNumber">
                        <option value="">Select Bed</option>
                        <option value="Bed 1">Bed 1</option>
                        <option value="Bed 2">Bed 2</option>
                        <option value="Bed 3">Bed 3</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="toast align-items-center shadow-none border-0" id="saveUpdateAdmissionStatusToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <h6 class="text-primary">Successful</h6>
            </div>  
        </div>
        <div class="d-flex justify-content-center mt-2">
            <button type="button" id="saveWardAndBedBtn" data-id="${line.id}" data-btn="${iteration}" class="btn btn-primary">
                save
            </button>
        </div>
    </div>`
}

export {surgeryNote, deliveryNote, vitalsignsTable, files, updateInvestigationAndManagement, investigations, review, consultation, AncConsultation, medicationAndTreatment, medicationAndTreatmentNurses, updateAdmissionStatus}

{/* <td><span class="position-relative"><a href="/transactions/11/receipts/15" target="blank" title="ABAGI Ernest_Nguevese.pdf"> *
                                    <i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a></span></td> */}