const review = (iteration, numberConverter, count, consultationDetails, line) => {
    return `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">${count + numberConverter(count)} Review</span>
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
                            </div>
                            <div class="extraInfoDiv">
                                <div class="my-2 form-control">
                                    <span class="fw-bold text-primary"> Other Documents </span>
                                    <div class="row overflow-auto m-1">
                                        ${ line.file === undefined ? '<td>No files</td>' :
                                            `<table id="otherDocumentsTable" class="table table-hover align-middle table-sm bg-primary">
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
                                    </div>
                                </div>
                                <div class="my-2 form-control">
                                    <span class="fw-semibold fs-5 mb-2"> Surgery Details </span>
                                    ${line.surgery === undefined ? '<div class="mb-2">No record</div>' : `<div>
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
                                    </div>`
                                    }
                                </div>
                                <div class="mb-2">
                                    <div class="my-2 form-control">
                                        <span class="fw-bold text-primary"> Delivery Note </span>
                                        ${line.deliveryNote === undefined ? '<div class="mb-2">No record</div>' :
                                        `<div class="row">
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
                                        </div>`
                                        }
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end my-2">
                                <span class="input-group-text">Dr Toby</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close ${count + numberConverter(count)} Review</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
                `
}

const InitialRegularConsultation = (iteration, consultationDetails, line) => {
    return `
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
                </div>
                <div class="extraInfoDiv">
                    <div class="my-2 form-control">
                        <span class="fw-bold text-primary"> Other Documents </span>
                        <div class="row overflow-auto m-1">
                            ${line.file === undefined ? '<td>No files</td>' :
                                `<table id="otherDocumentsTable" class="table table-hover align-middle table-sm bg-primary">
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
                        </div>
                    </div>
                    <div class="my-2 form-control">
                        <span class="fw-semibold fs-5 mb-2"> Surgery Details </span>
                        ${line.surgery === undefined ? '<div class="mb-2">No record</div>' :
                        `<div>
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
                        </div>`
                        }
                    </div>
                    <div class="mb-2">
                        <div class="my-2 form-control">
                            <span class="fw-bold text-primary"> Delivery Note </span>
                            ${line.deliveryNote === undefined ? '<div class="mb-2">No record</div>' :
                            `<div class="row">
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
                            </div>`
                            }
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

export{review, InitialRegularConsultation}