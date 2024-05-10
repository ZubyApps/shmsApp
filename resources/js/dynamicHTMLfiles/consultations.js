import { updateInvestigationAndManagement, investigations, review, consultation, AncConsultation, medicationAndTreatment, medicationAndTreatmentNurses, otherPrescriptions, otherPrescriptionsNurses} from "./partialHTMLS"

const regularReviewDetails = (iteration, numberConverter, count, length, line, viewer, isDoctorDone, closed, isHistory = 0) => {
    return `
                <div class="btn btn-primary d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center collapseConsultationBtn" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample" data-goto="#goto${iteration}" data-ishistory="${isHistory}">
                    <span class="mx-2 fw-semibold">${iteration > 1 && !line.specialistFlag ? count + numberConverter(count) + ' Review ' : line.specialistFlag ? 'Specialist Consultation ' : 'Initial Consultation '} ${ `(${line.date})`}</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        <div class="mb-2 form-control" id="goto${iteration}">
                            ${iteration < 2 || line.specialistFlag ? consultation(line) :  review(count, line)}
                            ${investigations(line, viewer)}
                            ${viewer == 'doctor' ||  viewer == 'hmo' ? medicationAndTreatment(line) : viewer == 'nurse' ? medicationAndTreatmentNurses(line) : ''}
                            ${viewer == 'doctor' ||  viewer == 'hmo' ? otherPrescriptions(line) : viewer == 'nurse' ? otherPrescriptionsNurses(line) : ''}
                            ${viewer == 'doctor' ? updateInvestigationAndManagement(length, iteration, line, isDoctorDone, closed) : ''}
                            <div class="extraInfoDiv">
                                ${length == iteration && viewer == 'doctor' ? 
                                `<div class="d-flex justify-content-end my-2">                                  
                                    ${closed || isHistory ? '' : 
                                        `<button type="button" id="deleteReviewConsultationBtn" data-id="${line.id}" data-patienttype="${line.patientType}" class="btn btn-outline-primary">
                                        <i class="bi bi-trash"></i>
                                            Delete
                                        </button>`
                                    }   
                                </div>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close ${iteration > 1 && !line.specialistFlag ? count + numberConverter(count) + ' Review' : line.specialistFlag ? 'Specialist Consultation' : 'Initial Consultation'} </span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
                `
}

const AncPatientReviewDetails = (iteration, numberConverter, count, length, line, viewer, isDoctorDone, closed, isHistory = 0) => {

    return `
                <div class="btn btn-primary d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center collapseConsultationBtn" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample" data-goto="#goto${iteration}">
                    <span class="mx-2">${iteration > 1 ? count + numberConverter(count) + ' Review' : 'Initial Consultation'} ${ `(${line.date})`}</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        
                        <div class="mb-2 form-control" id="goto${iteration}">
                            ${AncConsultation(line, iteration, count)}
                            ${investigations(line)}
                            ${viewer == 'doctor' ||  viewer == 'hmo' ? medicationAndTreatment(line) : viewer == 'nurse' ? medicationAndTreatmentNurses(line) : ''}
                            ${viewer == 'doctor' ||  viewer == 'hmo' ? otherPrescriptions(line) : viewer == 'nurse' ? otherPrescriptionsNurses(line) : ''}
                            ${viewer == 'doctor' ? updateInvestigationAndManagement(length, iteration, line, isDoctorDone, closed) : ''}
                            <div class="extraInfoDiv" >
                                ${length == iteration && viewer == 'doctor' ? 
                                `<div class="d-flex justify-content-between my-2">
                                    ${closed || isHistory ? '' : 
                                    `<button type="button" id="deleteReviewConsultationBtn" data-id="${line.id}" data-patienttype="${line.patientType}" class="btn btn-outline-primary">
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </button>`
                                    }
                                </div>`  : ''}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample">
                    <span class="mx-2">Close ${iteration > 1 ? count + numberConverter(count) + ' Review' : 'Initial Consultation'}</span>
                    <i class="bi bi-chevron-double-up text-primary"></i>
                    </div>
                </div>
                `
}

export{regularReviewDetails, AncPatientReviewDetails}
