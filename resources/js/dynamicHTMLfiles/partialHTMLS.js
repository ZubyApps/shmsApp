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
                        <button type="button" id="updateResourceListBtn" data-conid="${line.id}" data-id="${line.visitId}" data-patient="${line.patient}" data-sponsorcat="${line.sponsorCat}" data-sponsor="${line.sponsorName}" data-btn="${iteration}" data-last="${length > iteration || isDoctorDone || closed ? '' : 'last'}" class="btn btn${length > iteration || isDoctorDone || closed ? '-outline' : ''}-primary">
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
                                    <th>HMO Approval</th>
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
                <span class="fw-bold text-primary fs-5">Consultation Review ${count}</span>                                            
                <div class="row mt-2">
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Consultant Specialist (Name&Designation)</label>
                            <div>${line.consultantSpecialist}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Complain</label> 
                            <div class="textarea-pre-wrap">${line.complaint}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Examination Findings </label>                                                    
                            <div class="textarea-pre-wrap">${line.examinationFindings}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Selected ICD11 Diagnosis </label>
                            <div class="textarea-pre-wrap">${line.selectedDiagnosis}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Provisional Diagnosis </label>
                            <div class="textarea-pre-wrap">${line.provisionalDiagnosis}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> History Of Care </label>
                            <div class="textarea-pre-wrap">${line.historyOfCare}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Assessment </label>
                            <div class="textarea-pre-wrap">${line.assessment}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Physicians Plan </label>
                            <div>${line.plan}</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Patient Status </label>
                            <div>${line.status} </div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Ward </label>
                            <div>${line.ward}</div>
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
                <span class="fw-bold text-primary fs-5">Consultation</span>                 
                <div class="row mt-2">
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Consultant Specialist (Name&Designation)</label>
                            <div>${line.consultantSpecialist}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Presenting Complain</label>
                            <div class="textarea-pre-wrap">${line.presentingComplain}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">History of Presenting Complain </label>
                            <div class="textarea-pre-wrap">${line.historyOfPresentingComplain}</div>
                        </div>
                    </div> 
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Past Medical Surgical History</label>                            
                            <div class="textarea-pre-wrap">${line.pastMedicalHistory}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Obstetrics/Gynecological History</label>
                            <div class="textarea-pre-wrap">${line.obGynHistory}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Examination Findings</label>                                                    
                            <div class="textarea-pre-wrap">${line.examinationFindings}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Selected ICD11 Diagnosis</label>
                            <div class="selectedDiagnosis textarea-pre-wrap">${line.selectedDiagnosis}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Provisional Diagnosis </label> 
                            <div class="additionalDiagnosis textarea-pre-wrap">${line.provisionalDiagnosis}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> History Of Care </label>
                            <div class="textarea-pre-wrap">${line.historyOfCare}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Physicians Plan </label>
                            <div class="textarea-pre-wrap">${line.plan}</div>
                        </div>
                    </div>
                </div>                                           
                <div class="row mt-2 admissionStatus">
                    <div class="themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Patient Status </label>
                            <div>${line.status}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Ward </label>
                            <div>${line.ward}</div>
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
                <span class="fw-bold text-primary fs-5">Consultation ${iteration > 1 ? ' Review '+count : ''}</span>                                            
                <div class="row mt-2">
                    <div class="themed-grid-col col-xl-12 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Consultant Specialist (Name&Designation)</label>
                            <div>${line.consultantSpecialist}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-4 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">LMP</label> 
                            <div>${line.lmp ?? ''}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-4 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">EDD</label> 
                            <div>${line.edd ?? ''}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-4 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">EGA</label> 
                            <div>${line.ega}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-4 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Fetal Heart Rate</label> 
                            <div>${line.fetalHeartRate}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-4 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Height of Fundus</label> 
                            <div>${line.heightOfFundus}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-4 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Presentation&Position</label> 
                            <div>${line.presentationAndPosition}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary" id="relationOfPresentingPartToBrimLabel">Relation of Presenting Part to Brim</label> 
                            <div>${line.relationOfPresentingPartToBrim}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary">Obstetrics/Gynecological History</label>
                            <div class="textarea-pre-wrap">${line.obGynHistory}</div>
                        </div>
                    </div>                   
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Selected ICD11 Diagnosis </label>
                            <div class="textarea-pre-wrap">${line.selectedDiagnosis}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Provisional Diagnosis </label> 
                            <div class="textarea-pre-wrap">${line.provisionalDiagnosis}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Physician's Notes </label> 
                            <div>${line.notes}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Remarks </label>
                            <div class="textarea-pre-wrap">${line.remarks}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Physicians Plan </label>
                            <div class="textarea-pre-wrap">${line.plan}</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Patient Status </label>
                            <div>${line.status}</div>
                        </div>
                    </div>
                    <div class="themed-grid-col col-xl-6 mt-4">
                        <div class="form-outline mb-2">
                            <label class="form-label fw-bold text-secondary"> Ward </label>
                            <div>${line.ward}</div>
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
                                <th>Route</th>
                                <th>Qty Billed</th>
                                <th>Qty Dispensed</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Note</th>
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
                                <th>Route</th>
                                <th>Qty Billed</th>
                                <th>Qty Dispensed</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Note</th>
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
                                <th>Route</th>
                                <th>Qty Billed</th>
                                <th>Qty Dispensed</th>
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
                                <th>Route</th>
                                <th>Qty Billed</th>
                                <th>Qty Dispensed</th>
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

export {vitalsignsTable, updateInvestigationAndManagement, investigations, review, consultation, AncConsultation, medicationAndTreatment, otherPrescriptions, medicationAndTreatmentNurses, otherPrescriptionsNurses}
