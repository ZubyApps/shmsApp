import { AncPatientReviewDetails, regularReviewDetails } from "./consultations"

const visitDetails = (visitIteration, numberConverter, visit, viewer, isAnc) => {
                const displayfunction =  isAnc ? AncPatientReviewDetails : regularReviewDetails
                const consultations = visit.consultations.data
                let [consultationIteration, consultationCount, consultationsDiv, isDoctorDone, closed, isHistory] = [0, 0, '', 1, 1, 1]
                 
                consultations.forEach(line => {
                    consultationIteration++
                    consultationIteration > 1 ? consultationCount++ : ''
                    consultationsDiv += displayfunction(consultationIteration, numberConverter, consultationCount, consultations.length, line, viewer, isDoctorDone, closed, isHistory);
                })

            return `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center collapseVisitBtn" id="collapseVisit" data-bs-toggle="collapse" href="#collapseVisit${visitIteration}" role="button" aria-expanded="true" aria-controls="collapseVisit" data-gotovisit="#gotovisit${visitIteration}" data-id="${visit.id}" data-isanc="${isAnc}">
                    <span class="mx-2 fw-semibold">${visitIteration + numberConverter(visitIteration) + ' Visit ' + `(${visit.came})` }</span>
                    <i class="bi bi-chevron-double-down text-warning fw-semibold"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseVisit${visitIteration}" style="">
                    <div class="card card-body">
                            <div class="mb-2 form-control" id="gotovisit${visitIteration}">
                                <x-form-span>Vital Signs</x-form-span>
                                <div class="row overflow-auto my-3">
                                    <table id="vitalSignsHistory${visit.id}"
                                        class="table table-hover align-middle table-sm vitalSignsTable">
                                        <thead>
                                            ${isAnc ? 
                                                `<tr>
                                                    <th>Done</th>
                                                    <th>BP</th>
                                                    <th>Weight</th>
                                                    <th>Urine-Protein</th>
                                                    <th>Urine-Glucose</th>
                                                    <th>Remarks</th>
                                                    <th>By</th>
                                                    <th></th>
                                                </tr>` :
                                                `
                                                <tr>
                                                    <th>Done</th>
                                                    <th>Temp</th>
                                                    <th>BP</th>
                                                    <th>Pulse</th>
                                                    <th>Resp Rate</th>
                                                    <th>SpO2</th>
                                                    <th>Weight</th>
                                                    <th>Height</th>
                                                    <th>BMI</th>
                                                    <th>Head Circ</th>
                                                    <th>Mid A Circ</th>
                                                    <th>Fluid Drain</th>
                                                    <th>Urine OutPut</th>
                                                    <th>Fetal HR</th>
                                                    <th>Note</th>
                                                    <th>By</th>
                                                    <th></th>
                                                </tr>` 
                                            }
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id=conDiv>
                            ${consultationsDiv}
                            </div>
                            <div class="my-2 form-control">
                                <span class="fw-bold text-primary"> Delivery Note </span>
                                <div class="row overflow-auto m-1">
                                    <table id="deliveryNoteTable${ visit.id }" class="table table-sm deliveryNoteTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time of Admission</th>
                                                <th>Time of Delivery</th>
                                                <th>Mode of Delivery</th>
                                                <th>Sex</th>
                                                <th>EBL</th>
                                                <th>Nurse</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-2 form-control">
                                <span class="fw-bold">Surgery Note</span>
                                <div class="row overflow-auto m-1">
                                    <table id="surgeryNoteTableHistory${ visit.id }" class="table table-sm surgeryNoteTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Operation</th>
                                                <th>Aneasthesia</th>
                                                <th>Surgeon</th>
                                                <th>Surgeons Notes</th>
                                                <th>PostOp Notes</th>
                                                <th>Saved By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-2 form-control">
                                <span class="fw-bold">Files/Documents</span>
                                <div class="row overflow-auto m-1">
                                    <table id="patientsFileTableHistory${ visit.id }" class="table align-middle table-sm bg-primary">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>File Name</th>
                                                <th>Third Party</th>
                                                <th>Comment</th>
                                                <th>File</th>
                                                <th>UploadedBy</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div "class" = "mb-2">
                                <div class="mb-2 form-control">
                                    <x-form-label>Patient's Bill Details</x-form-label>
                                    <X-form-div class="my-4">
                                        <table id="billingTableHistory${ visit.id }" class="table align-middle">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </X-form-div>
                                </div>
                            </div>
                    </div>
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseVisit" data-bs-toggle="collapse" href="#collapseVisit${visitIteration}" role="button" aria-expanded="true" aria-controls="collapseVisit">
                    <span class="mx-2">Close ${visitIteration + numberConverter(visitIteration) + ' Visit' }</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
            `
}

export {visitDetails}