const review = (iteration, numberConverter, count, consultationDetails, line) => {
    return `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">${count + numberConverter(count)} Review</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        <div class="mb-2 form-control">
                            <span class="fw-bold text-primary">Consultation Review</span>                                            
                            <div class="row">
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="complainLabel">Complain</span> 
                                        <textarea class="form-control" name="complain" id="complain" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="historyOfPresentingComplainLabel">Physician's <br> Notes </span>
                                        <textarea class="form-control" name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div> 
                                <div class="col-xl-4 themed-grid-col col-xl-12">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                                        <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3" readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="diagnosisLabel"> Assessment </span>
                                        <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="3" readonly="readonly"></textarea>
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
                                        <input class="form-control patientStatus" name="patientStatus" value="" readonly="readonly">
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="wardLabel"> Ward </span>
                                        <input class="form-control ward" name="ward" value="">
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
                                                <td>12/09/23</td>
                                                <td>Onjefu</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                ${consultationDetails.data.length > iteration ? '' : 
                                `<div class="investigationDiv d-none" data-div="${iteration}">
                                    <div class="mb-2 form-control">
                                        <span class="fw-semibold">Investigation</span>
                                        <div class="row">
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text" id="itemLabel">Test</span> 
                                                    <input class="form-control" type="search" name="item" id="item" placeholder="search" autocomplete="off">
                                                    <datalist name="item" type="text" class="decoration-none"></datalist>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col col-xl-6">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text" id="testResultLabel">Result</span> 
                                                    <input class="form-control" type="text" name="testResult" id="testResult" placeholder="Enter result">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            <button type="button" id="saveInvestigationBtn" data-btn="${iteration}" class="btn btn-primary">
                                                save
                                            <i class="bi bi-eyedropper"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center my-2">
                                    <button type="button" class="btn btn-primary" id="addInvestigationBtn" data-btn="${iteration}">
                                        Add Test Done
                                    </button>
                                </div>`}
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
                                                <th>Action</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>12/09/2023 11:02pm</td>
                                                <td>N/S 500mls</td>
                                                <td>500mls 12hrly x2</td>
                                                <td>Dr Emma</td>
                                                <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                                    Chart
                                                </button></td>
                                                <td>Complete</td>
                                            </tr>
                                            <tr>
                                                <td>12/09/2023 11:02pm</td>
                                                <td>N/S 500mls</td>
                                                <td>500mls 12hrly x2</td>
                                                <td>Dr Emma</td>
                                                <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                                Chart
                                                </button></td>
                                                <td>Discontinued</td>
                                            </tr>
                                            <tr>
                                                <td>12/09/2023 11:02pm</td>
                                                <td>N/S 500mls</td>
                                                <td>500mls 12hrly x2</td>
                                                <td>Dr Emma</td>
                                                <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                                Chart
                                                </button></td>
                                                <td>Incomplete</td>
                                            </tr>
                                            <tr>
                                                <td>12/09/2023 11:02pm</td>
                                                <td>N/S 500mls</td>
                                                <td>500mls 12hrly x2</td>
                                                <td>Dr Emma</td>
                                                <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                                Chart
                                                </button></td>
                                                <td>None</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start my-3 gap-2">
                                <button type="button" id="medicationChartBtn" class="btn btn-outline-primary">
                                    <i class="bi bi-capsule"></i>
                                    Medication Chart
                                </button>
                                ${consultationDetails.data.length > iteration ? '' :
                                `<button type="button" id="newDeliveryNoteBtn" class="btn btn-outline-primary">
                                    Delivery Note
                                    <i class="bi bi-pencil-square"></i>
                                </button>`
                                }
                            </div>
                            <div class="mb-2 form-control">
                                <span class="fw-bold text-primary">Vital Signs</span>                                            
                                <div class="row overflow-auto m-1">
                                    <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Temperature</th>
                                                <th>Blood Pressure</th>
                                                <th>Pulse Rate</th>
                                                <th>Respiratory Rate</th>
                                                <th>Weight</th>
                                                <th>Height</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>120/80mmgh</td>
                                                <td>90</td>
                                                <td>32</td>
                                                <td>94kg</td>
                                                <td>1.5m</td>
                                            </tr>
                                            <tr>
                                                <td>10-Jul-2023</td>
                                                <td>37.1C</td>
                                                <td>110/80mmgh</td>
                                                <td>96</td>
                                                <td>40</td>
                                                <td>94kg</td>
                                                <td>1.5m</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row d-none" id="addVitalsignsDiv" data-div="${iteration}">
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="temperatureLabel">Temparature</span>    
                                            <input class="form-control" name="temperature" id="temperature" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="bloodPressureLabel">Blood Pressure</span>    
                                            <input class="form-control" name="bloodPressure" id="bloodPressure" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="spO2Label">SpO2</span>    
                                            <input class="form-control" name="spO2" id="spO2" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="pulseRateLabel">Pulse Rate</span>    
                                            <input class="form-control" type="text" name="pulseRate" id="pulseRate" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="respiratoryRateLabel"> Respiratory Rate</span>    
                                            <input class="form-control" type="text" name="respiratoryRate" id="respiratoryRate" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="weightLabel">Weight</span>    
                                            <input class="form-control" type="text" name="weight" id="weight" autocomplete="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 themed-grid-col">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="heightLabel">Height</span>    
                                            <input class="form-control" type="text" name="height" id="height" autocomplete="">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="addVitalsignsBtn" data-btn="${iteration}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        add
                                    </button>
                                </div>
                            </div>
                            <div class="extraInfoDiv">
                                <div class="mb-2">
                                    <div class="my-2 form-control">
                                        <span class="fw-bold text-primary"> Delivery Note </span>
                                        ${line.deliveryNote === '' ? '<div class="mb-2">No record</div>' :
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
                                                    <input class="form-control" type="datetime-local" name="timeOfAdmission" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Time of Delivery</span>
                                                    <input class="form-control" datetime-local name="timeOfDelivery" value="" readonly>
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
                                                    <input class="form-control" name="modeOfDelivery" value="" readonly>
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
                                                    <input class="form-control" name="headCircumference" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">Sex</span>
                                                    <input class="form-control" name="sex" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 themed-grid-col">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text">EBL</span>
                                                    <input class="form-control" name="ebl" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            <button type="button" id="updateDeliveryNoteBtn" data-btn="${iteration}" class="btn btn-primary">
                                                Update
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
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
                <span class="fw-bold text-primary">Consultation</span>                                            
                <div class="row">
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="presentingComplainLabel">Presenting <br>Complain</span> 
                            <textarea class="form-control" name="presentingComplain" id="presentingComplain" cols="10" rows="3" value"" readonly="readonly"></textarea>
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
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" value="" readonly="readonly"></textarea>
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
                                    <td>12/09/23</td>
                                    <td>Onjefu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    ${consultationDetails.data.length > iteration ? '' : 
                    `<div class="investigationDiv d-none" data-div="${iteration}">
                        <div class="mb-2 form-control">
                            <span class="fw-semibold">Investigation</span>
                            <div class="row">
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="itemLabel">Test</span> 
                                        <input class="form-control" type="search" name="item" id="item" placeholder="search" autocomplete="off">
                                        <datalist name="item" type="text" class="decoration-none"></datalist>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="testResultLabel">Result</span> 
                                        <input class="form-control" type="text" name="testResult" id="testResult" placeholder="Enter result">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" id="saveInvestigationBtn" data-btn="${iteration}" class="btn btn-primary">
                                    save
                                <i class="bi bi-eyedropper"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center my-2">
                        <button type="button" class="btn btn-primary" id="addInvestigationBtn" data-btn="${iteration}">
                            Add Test Done
                        </button>
                    </div>`}
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
                                    <th>Action</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12/09/2023 11:02pm</td>
                                    <td>N/S 500mls</td>
                                    <td>500mls 12hrly x2</td>
                                    <td>Dr Emma</td>
                                    <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                        Chart
                                    </button></td>
                                    <td>Complete</td>
                                </tr>
                                <tr>
                                    <td>12/09/2023 11:02pm</td>
                                    <td>N/S 500mls</td>
                                    <td>500mls 12hrly x2</td>
                                    <td>Dr Emma</td>
                                    <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                    Chart
                                    </button></td>
                                    <td>Discontinued</td>
                                </tr>
                                <tr>
                                    <td>12/09/2023 11:02pm</td>
                                    <td>N/S 500mls</td>
                                    <td>500mls 12hrly x2</td>
                                    <td>Dr Emma</td>
                                    <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                    Chart
                                    </button></td>
                                    <td>Incomplete</td>
                                </tr>
                                <tr>
                                    <td>12/09/2023 11:02pm</td>
                                    <td>N/S 500mls</td>
                                    <td>500mls 12hrly x2</td>
                                    <td>Dr Emma</td>
                                    <td><button type="button" id="chartMedicationBtn" class="btn btn-outline-primary">
                                    Chart
                                    </button></td>
                                    <td>None</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-start my-2 gap-2">
                    <button type="button" id="fileBtn" class="btn btn-outline-primary">
                        <i class="bi bi-capsule"></i>
                        Chart
                    </button>
                    ${consultationDetails.data.length > iteration ? '' :
                    `<button type="button" id="deliveryBtn" class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square"></i>
                        Delivery Note
                    </button>`
                    }
                </div>
                <div class="mb-2 form-control">
                    <span class="fw-bold text-primary">Vital Signs</span>                                            
                    <div class="row overflow-auto m-1">
                        <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Temperature</th>
                                    <th>Blood Pressure</th>
                                    <th>Pulse Rate</th>
                                    <th>Respiratory Rate</th>
                                    <th>Weight</th>
                                    <th>Height</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>120/80mmgh</td>
                                    <td>90</td>
                                    <td>32</td>
                                    <td>94kg</td>
                                    <td>1.5m</td>
                                </tr>
                                <tr>
                                    <td>10-Jul-2023</td>
                                    <td>37.1C</td>
                                    <td>110/80mmgh</td>
                                    <td>96</td>
                                    <td>40</td>
                                    <td>94kg</td>
                                    <td>1.5m</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="row" id="addVitalsignsDiv" data-div="${iteration}">
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="temperatureLabel">Temparature</span>    
                                    <input class="form-control" name="temperature" id="temperature" autocomplete="">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="bloodPressureLabel">Blood Pressure</span>    
                                    <input class="form-control" name="bloodPressure" id="bloodPressure" autocomplete="">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="spO2Label">SpO2</span>    
                                    <input class="form-control" name="spO2" id="spO2" autocomplete="">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="pulseRateLabel">Pulse Rate</span>    
                                    <input class="form-control" type="text" name="pulseRate" id="pulseRate" autocomplete="">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="respiratoryRateLabel"> Respiratory Rate</span>    
                                    <input class="form-control" type="text" name="respiratoryRate" id="respiratoryRate" autocomplete="">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="weightLabel">Weight</span>    
                                    <input class="form-control" type="text" name="weight" id="weight" autocomplete="">
                                </div>
                            </div>
                            <div class="col-xl-4 themed-grid-col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text" id="heightLabel">Height</span>    
                                    <input class="form-control" type="text" name="height" id="height" autocomplete="">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="button" id="addVitalsignsBtn" data-btn="${iteration}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                add
                            </button>
                        </div>
                    </div>
                </div>
                <div class="extraInfoDiv">
                    <div class="mb-2">
                        <div class="my-2 form-control">
                            <span class="fw-bold text-primary"> Delivery Note </span>
                            ${line.deliveryNote === '' ? '<div class="mb-2">No record</div>' :
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
                                        <input class="form-control" type="datetime-local" name="timeOfAdmission" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text">Time of Delivery</span>
                                        <input class="form-control" type="datetime-local" name="timeOfDelivery" readonly>
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
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" id="updateDeliveryNoteBtn" data-btn="${iteration}" class="btn btn-primary">
                                    Update
                                    <i class="bi bi-prescription"></i>
                                </button>
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
        <span class="mx-2">Close Initial Consultation</span>
        <i class="bi bi-chevron-double-up text-primary"></i>
        </div>
    </div>
    `
}

export{review, InitialRegularConsultation}