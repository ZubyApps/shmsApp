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
                    <table id="prescriptionTable${line.visitId}" class="table table-hover align-middle table-sm bg-primary">
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

const updateInvestigationAndManagement = (iteration, line) => {
    return `
                <div class="d-flex justify-content-center my-2">
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
                            <button type="button" id="addInvestigationAndManagmentBtn" data-id="${line.id}" data-btn="${iteration}" class="btn btn-primary">
                                add
                            <i class="bi bi-prescription"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2 form-control">
                        <table id="prescriptionTable${line.id}" class="table table-hover align-middle table-sm bg-primary">
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
                                    <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>`
}

const investigations = (line) => {
            return `
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Investigations </span>
                    <div class="row overflow-auto m-1">
                        <table id="investigationTable${line.id}" class="table table-hover align-middle table-sm bg-primary">
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
                                    <td><textarea class="form-control" id="resultEl" readonly>Pfall ++
                                    Pfall ++
                                    </textarea></td>
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
        `
}

const review = (line) => {
    return `<div class="form-control">
                <span class="fw-bold text-primary">Consultation Review</span>                                            
                <div class="row">
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="complainLabel">Complain</span> 
                            <textarea class="form-control" name="complaint" id="complaint" cols="10" rows="3" readonly="readonly">${line.complaint}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                            <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3" readonly="readonly">${line.examinationFindings}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Selected <br>ICD11 <br> Diagnosis </span>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" value="" readonly="readonly">${line.selectedDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Assessment </span>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="3" readonly="readonly">${line.assessment}</textarea>
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
                            <input class="form-control" name="consultantSpecialist" value="${line.consultantSpecialist}">
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
                            <textarea class="form-control" type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="2" readonly="readonly">${line.obyGynHistory}</textarea>
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

const AncConsultation = (line, iteration) => {
    return  `1
            <div class="form-control">
                <span class="fw-bold text-primary">Consultation ${iteration > 1 ? ' Review' : ''}</span>                                            
                <div class="row">
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
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="obGynHistoryLabel">Obstetrics/<br>Gynecological History</span>
                            <textarea class="form-control" type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="3" value="${line.obGynHistory}" readonly="readonly"></textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="ultrasoundReportLabel"> Ultrasound Report </span>                                                    
                            <textarea class="form-control" type="text" name="ultrasoundReport" id="ultrasoundReport" cols="10" rows="3" value="${line.ultrasoundReport}" readonly="readonly"></textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="fetalHeartRateLabel">Fetal <br/> Heart Rate</span> 
                            <input class="form-control" name="fetalHeartRate" id="fetalHeartRate" value="${line.fetalHeartRate}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="heightOfFondusLabel">Height <br/> of Fondus</span> 
                            <input class="form-control" name="heightOfFondus" id="heightOfFondus" value="${line.heightOfFundus}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="presentationAndPositionLabel">Presentation <br/> And Position</span> 
                            <input class="form-control" name="presentationAndPosition" id="presentationAndPosition" value="${line.presentationAndPosition}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="relationOfPresentingPartToBrimLabel">Relation <br/> of Presenting <br> Part to Brim</span> 
                            <input class="form-control" name="relationOfPresentingPartToBrim" id="relationOfPresentingPartToBrim"  value="${line.relationOfPresentingPartToBrim}" readonly="readonly"/>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Selected <br>ICD11 <br> Diagnosis </span>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="3" readonly="readonly">${line.selectedDiagnosis ?? 'nill'}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="diagnosisLabel"> Physician's Notes </span> 
                            <textarea class="form-control additionalDiagnosis" type="text" name="additionalDiagnosis" cols="10" rows="3" readonly="readonly">${line.notes}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="remarksLabel"> Remarks </span>
                            <textarea class="form-control" type="text" name="remarks" id="remarks" cols="10" rows="3" readonly="readonly"></textarea>
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
                <span class="fw-bold text-primary"> Medication & Treatment </span>
                <div class="row overflow-auto m-1">
                    <table id="prescriptionTable${line.id}" class="table table-hover align-middle table-sm bg-primary">
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
                    </table>
                </div>
            </div>
        `
}

export {surgeryNote, deliveryNote, vitalsignsTable, files, updateInvestigationAndManagement, investigations, review, consultation, AncConsultation, medicationAndTreatment}