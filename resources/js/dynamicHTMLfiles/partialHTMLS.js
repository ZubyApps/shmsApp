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

const updateInvestigationAndManagement = (length, iteration, line, isDoctorDone, closed) => {
    return ` 
                <div class="investigationAndManagmentDiv mt-2 active" data-div="${iteration}" data-goto=#gotoResource${iteration}>
                    <div class="d-flex justify-content-center">
                        <button type="button" id="updateResourceListBtn" data-conid="${line.id}" data-visitid="${line.visitId}" data-patient="${line.patient}" data-sponsorcat="${line.sponsorCat}" data-sponsor="${line.sponsorName}" data-btn="${iteration}" data-last="${length > iteration || isDoctorDone || closed ? '' : 'last'}" class="btn btn${length > iteration || isDoctorDone || closed ? '-outline' : ''}-primary">
                            Update Resources
                            ${length > iteration || isDoctorDone || closed ? '<i class="bi bi-lock-fill tooltip-test" title="veiwing only"></i>' : '<i class="bi bi-prescription"></i>'}
                        </button>
                    </div>
                </div>`
}

const investigations = (line) => {
            return `
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Investigations </span>
                    <div class="row overflow-auto m-1">
                        <table id="investigationTable${line.id}" data-id="${line.id}" class="table table-sm investigationTable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Investigation</th>
                                    <th>Requested By</th>
                                    <th>Requested</th>
                                    <th>Actions</th>
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
                        <div class="form-outline mb-2">
                            <label class="form-label" id="specialistDesignationLabel"> Consultant Specialist (Name&Designation)</label>
                            <textarea class="form-control" name="consultantSpecialist" ${line.consultantSpecialist ? 'readonly' : 'disabled'}>${line.consultantSpecialist}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="complainLabel">Complain</label> 
                            <textarea class="form-control" name="complaint" id="complaint" cols="10" rows="2" readonly="readonly">${line.complaint}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="examinationFindingsLabel"> Examination Findings </label>                                                    
                            <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="2" readonly="readonly">${line.examinationFindings}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="diagnosisLabel"> Selected ICD11 Diagnosis </label>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" value="" readonly="readonly">${line.selectedDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="diagnosisLabel"> Assessment </label>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="2" readonly="readonly">${line.assessment}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="physiciansPlanLabel"> Physicians Plan </label>
                            <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="2" readonly="readonly">${line.plan}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="admitLabel"> Patient Status </label>
                            <input class="form-control patientStatus" name="patientStatus" value="${line.status}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="wardLabel"> Ward </label>
                            <input class="form-control ward" name="ward" value="${line.ward}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="bedLabel"> Bed </label>
                            <input class="form-control bed" name="bed" value="${line.bedNumber}" disabled>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end my-2">
                        <label class="form-label">${line.doctor}</label>
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
                        <div class="form-outline mb-2">
                            <label class="form-label" id="specialistDesignationLabel"> Consultant Specialist (Name&Designation)</label>
                            <textarea class="form-control" name="consultantSpecialist" value="${line.consultantSpecialist}" ${line.consultantSpecialist ? 'readonly' : 'disabled'}></textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label ">Presenting Complain</label>
                            <textarea class="form-control form-control-md" name="presentingComplain" id="presentingComplain" cols="10" rows="2" readonly="readonly">${line.presentingComplain}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                    <div class="form-outline mb-2">
                        <label class="form-label" id="historyOfPresentingComplainLabel">History of Presenting Complain </label>
                        <textarea class="form-control" name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="2" readonly="readonly">${line.historyOfPresentingComplain}</textarea>
                    </div>
                </div> 
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="medicalHistoryLabel"> Past Medical Surgical History</label>                            
                            <textarea class="form-control" name="medicalHistory" id="medicalHistory" cols="10" rows="2" readonly="readonly">${line.pastMedicalHistory}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="obGynHistoryLabel">Obstetrics/Gynecological History</label>
                            <textarea class="form-control" type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="2" readonly="readonly">${line.obGynHistory}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="examinationFindingsLabel"> Examination Findings</label>                                                    
                            <textarea class="form-control" type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="2"readonly="readonly">${line.examinationFindings}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="diagnosisLabel"> Selected ICD11 Diagnosis </label>
                            <textarea class="form-control reviewSelectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="2" readonly="readonly">${line.selectedDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="diagnosisLabel"> Provisional Diagnosis </label> 
                            <textarea class="form-control additionalDiagnosis" type="text" name="additionalDiagnosis" cols="10" rows="2" readonly="readonly">${line.provisionalDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="physiciansPlanLabel"> Physicians Plan </label>
                            <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="2" readonly="readonly">${line.plan}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2 admissionStatus">
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="admitLabel"> Patient Status </label>
                            <input class="form-control patientStatus" name="patientStatus" value="${line.status}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="wardLabel"> Ward </label>
                            <input class="form-control ward" name="ward" value="${line.ward}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="bedLabel"> Bed </label>
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
                        <div class="form-outline mb-2">
                            <label class="form-label" id="specialistDesignationLabel"> Consultant Specialist (Name&Designation)</label>
                            <input class="form-control" name="consultantSpecialist" value="${line.consultantSpecialist}" ${line.consultantSpecialist ? 'readonly' : 'disabled'}>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="lmpLabel">LMP</label> 
                            <input class="form-control" name="lmp" id="lmp" value="${line.lmp ?? ''}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="eddLabel">EDD</label> 
                            <input class="form-control" name="edd" id="edd" value="${line.edd ?? ''}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="egaLabel">EGA</label> 
                            <input class="form-control" name="ega" id="ega"value="${line.ega}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="fetalHeartRateLabel">Fetal Heart Rate</label> 
                            <input class="form-control" name="fetalHeartRate" id="fetalHeartRate" value="${line.fetalHeartRate}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="heightOfFondusLabel">Height of Fundus</label> 
                            <input class="form-control" name="heightOfFundus" id="heightOfFundus" value="${line.heightOfFundus}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="presentationAndPositionLabel">Presentation&Position</label> 
                            <input class="form-control" name="presentationAndPosition" id="presentationAndPosition" value="${line.presentationAndPosition}" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="relationOfPresentingPartToBrimLabel">Relation of Presenting Part to Brim</label> 
                            <textarea class="form-control" name="relationOfPresentingPartToBrim" id="relationOfPresentingPartToBrim"  value="${line.relationOfPresentingPartToBrim}" readonly="readonly"></textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="obGynHistoryLabel">Obstetrics/Gynecological History</label>
                            <textarea class="form-control" type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="2" value="" readonly="readonly">${line.obGynHistory}</textarea>
                        </div>
                    </div>                   
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="selectedDiagnosisLabel"> Selected ICD11 Diagnosis </label>
                            <textarea class="form-control selectedDiagnosis" type="text" name="selectedDiagnosis" cols="10" rows="2" readonly="readonly">${line.selectedDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="diagnosisLabel"> Provisional Diagnosis </label> 
                            <textarea class="form-control additionalDiagnosis" type="text" name="additionalDiagnosis" cols="10" rows="2" readonly="readonly">${line.provisionalDiagnosis}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="diagnosisLabel"> Physician's Notes </label> 
                            <textarea class="form-control notes" type="text" name="notes" cols="10" rows="2" readonly="readonly">${line.notes}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="remarksLabel"> Remarks </label>
                            <textarea class="form-control" type="text" name="remarks" id="remarks" cols="10" rows="2" readonly="readonly">${line.remarks}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="physiciansPlanLabel"> Physicians Plan </label>
                            <textarea class="form-control" type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="2" readonly="readonly">${line.plan}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="admitLabel"> Patient Status </label>
                            <input class="form-control patientStatus" name="patientStatus" value="${line.status}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="wardLabel"> Ward </label>
                            <input class="form-control ward" name="ward" value="${line.ward}" disabled>
                        </div>
                    </div>
                    <div class="col-xl-4 themed-grid-col col-xl-4">
                        <div class="form-outline mb-2">
                            <label class="form-label" id="bedLabel"> Bed </label>
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
                    <table id="treatmentTable${line.id}" data-id="${line.id}" class="table table-sm treatmentTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Treatment</th>
                                <th>Prescription</th>
                                <th>Qty</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Billed</th>
                                <th>Dispensed</th>
                                <th>Chart</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        `
}

const otherPrescriptions = (line) => {
    return `
            <div class="my-2 form-control">
                <span class="fw-bold text-primary">Other Prescriptions</span>
                <div class="row overflow-auto m-1">
                    <table id="otherPrescriptionsTable${line.id}" data-id="${line.id}" class="table table-sm otherPrescriptionsTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Item</th>
                                <th>Note/Instruction</th>
                                <th>Qty</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Billed</th>
                                <th>Dispensed</th>
                                <th>Chart</th>
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
                    <table id="nurseTreatmentTable${line.id}" data-id="${line.id}" class="table table-sm nurseTreatmentTable">
                        <thead>
                            <tr>
                                <th>Treatment</th>
                                <th>Prescription</th>
                                <th>Qty</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Note</th>
                                <th>Chartable</th>
                                <th>Chart</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        `
}

const otherPrescriptionsNurses = (line) => {
    return `
            <div class="my-2 form-control">
                <span class="fw-bold text-primary">Other Prescriptions</span>
                <div class="row overflow-auto m-1">
                    <table id="otherPrescriptionsNursesTable${line.id}" data-id="${line.id}" class="table table-sm otherPrescriptionsNursesTable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Note/Instruction</th>
                                <th>Qty</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Chartable</th>
                                <th>Chart</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        `
}

export {vitalsignsTable, updateInvestigationAndManagement, investigations, review, consultation, AncConsultation, medicationAndTreatment, otherPrescriptions, medicationAndTreatmentNurses, otherPrescriptionsNurses}
