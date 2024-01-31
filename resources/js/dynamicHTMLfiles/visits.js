import { regularReviewDetails } from "./consultations"

const visitDetails = (visitIteration, iteration, numberConverter, visitCount, count, visitLength, length, visitLine, line, viewer, isDoctorDone, isAnc) => {
    
    return `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center collapseVisitBtn" id="collapseVisit" data-bs-toggle="collapse" href="#collapseVisit${visitIteration}" role="button" aria-expanded="true" aria-controls="collapseExample" data-goto="#goto${visitIteration}">
                    <span class="mx-2">${count + numberConverter(count) + ' Visit' }</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseVisit${visitIteration}" style="">
                    <div class="card card-body">
                            <div class="mb-2 form-control">
                                <x-form-span>Vital Signs</x-form-span>
                                <div class="row overflow-auto my-3">
                                    <table id="vitalSignsConsultation${ isAnc ? 'AncConHistory' : 'ConHistory' }"
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
                        ${
                            isAnc  ? 
                            AncPatientReviewDetails(iteration, numberConverter, count, length, line, viewer, isDoctorDone) : 
                            regularReviewDetails(iteration, numberConverter, count, length, line, viewer, isDoctorDone)
                        }

                            <div "class" = "mb-2">
                                <div class="mb-2 form-control">
                                    <x-form-label>Patient's Bill Details</x-form-label>
                                    <X-form-div class="my-4">
                                        <table id="billingTable${ visitLine.id }" class="table align-middle">
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
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close ${count + numberConverter(count) + ' Visit' }</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
            `
}

export {visitDetails}