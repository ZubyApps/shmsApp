const review = (iteration, numberConverter, count, consultationDetails, line) => {
    return  `
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
                                <div class="col-xl-4 themed-grid-col col-xl-6">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="examinationFindingsLabel"> Examination <br> Findings </span>                                                    
                                        <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3" readonly="readonly"></textarea>
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
                                <div class="col-xl-4 themed-grid-col col-xl-4">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="admitLabel"> Patient Status </span>
                                        <input class="form-control patientStatus" name="patientStatus" value="In-patient" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-4">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="wardLabel"> Ward </span>
                                        <input class="form-control ward" name="ward" value="Female Ward" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-4">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="bedLabel"> Bed </span>
                                        <input class="form-control bed" name="bed" value="Bed 1" disabled>
                                    </div>
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
                                    </table>
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
                        <div class="form-control">
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
                                <div class="col-xl-4 themed-grid-col col-xl-4">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="admitLabel"> Patient Status </span>
                                        <input class="form-control patientStatus" name="patientStatus" value="Out-Patient" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-4">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="wardLabel"> Ward </span>
                                        <input class="form-control ward" name="ward" value="Private Ward" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-4 themed-grid-col col-xl-4">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="bedLabel"> Bed </span>
                                        <input class="form-control bed" name="bed" value="Bed 1" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end my-2">
                                <span class="input-group-text">Dr Emannuel</span>
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                <span class="mx-2">Close Initial Consultation</span>
                <i class="bi bi-chevron-double-up text-primary"></i>
                </div>
            </div>
            `
}

export{review, InitialRegularConsultation}