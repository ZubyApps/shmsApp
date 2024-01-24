import { deliveryNotes, surgeryNote, updateAdmissionStatus, files, updateInvestigationAndManagement, investigations, review, consultation, AncConsultation, medicationAndTreatment, medicationAndTreatmentNurses} from "./partialHTMLS"

const regularReviewDetails = (iteration, numberConverter, count, length, line, viewer, isDoctorDone) => {

    return `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center collapseBtn" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample" data-goto="#goto${iteration}">
                    <span class="mx-2">${iteration > 1 && !line.specialistFlag ? count + numberConverter(count) + ' Review' : line.specialistFlag ? 'Specialist Consultation' : 'Initial Consultation'}</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        
                        <div class="mb-2 form-control" id="goto${iteration}">
                            ${iteration < 2 || line.specialistFlag ? consultation(line) :  review(count, line)}
                            ${ viewer == 'nurse' && length == iteration ? updateAdmissionStatus(line, iteration) : ''}
                            ${investigations(line, viewer)}
                            ${viewer == '' ||  viewer == 'hmo' ? medicationAndTreatment(line) : viewer == 'nurse' ? medicationAndTreatmentNurses(line) : ''}
                            ${!viewer ? updateInvestigationAndManagement(length, iteration, line, isDoctorDone) : ''}
                            ${isDoctorDone ? '' :
                            `<div class="d-flex justify-content-start my-3 gap-2" >
                                ${!viewer ? `
                                <button type="button" id="fileBtn" data-id="${line.id}" data-visitid="${line.visitId}" class="btn btn-outline-primary">
                                    File
                                    <i class="bi bi-file-earmark-medical"></i>
                                </button>
                                <button type="button" id="surgeryBtn" data-id="${line.id}" data-visitid="${line.visitId}" class="btn btn-outline-primary">
                                    Surgery 
                                    <i class="bi bi-pencil-square"></i>
                                </button>` : ''}
                                ${length == iteration && (viewer == 'nurse') ? `
                                <button type="button" id="newDeliveryNoteBtn" class="btn btn-outline-primary ${line.deliveryNotes ? 'd-none' : ''}" data-id="${line.id}" data-visitid="${line.visitId}">
                                    Delivery Note
                                    <i class="bi bi-pencil-square"></i>
                                </button>` : ''}
                            </div>` }
                            <div class="extraInfoDiv">
                                ${!viewer ? `
                                <div class="my-2 form-control">
                                    <span class="fw-bold text-primary"> Other Documents </span>
                                    <div class="row overflow-auto m-1">
                                        ${ line.file === undefined ? '<td>No files</td>' : files(line.file)}
                                    </div>
                                </div>
                                <div class="my-2 form-control">
                                    <span class="fw-semibold fs-5 mb-2"> Surgery Details </span>
                                    ${line.surgery === undefined ? '<div class="mb-2">No record</div>' : surgeryNote(line.surgery)}
                                </div>` : ''}
                                ${viewer == 'nurse' || !viewer ? deliveryNotes(line): ''}
                                ${length == iteration && viewer == '' ? 
                                `<div class="d-flex justify-content-between my-2">
                                    <button type="button" id="closeVisitBtn" data-id="${line.id}" class="btn btn-outline-primary">
                                        <i class="bi bi-trash"></i>
                                        Close Visit
                                    </button>
                                    <button type="button" id="deleteReviewConsultationBtn" data-id="${line.id}" class="btn btn-outline-primary">
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </button>
                                </div>` : ''}
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

const AncPatientReviewDetails = (iteration, numberConverter, count, length, line, viewer, isDoctorDone) => {

    return `
                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center collapseBtn" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample${iteration}" role="button" aria-expanded="true" aria-controls="collapseExample" data-goto="#goto${iteration}">
                    <span class="mx-2">${iteration > 1 ? count + numberConverter(count) + ' Review' : 'Initial Consultation'}</span>
                    <i class="bi bi-chevron-double-down text-primary"> </i>
                </div>
                <div class="collapse mb-2 reviewDiv" id="collapseExample${iteration}" style="">
                    <div class="card card-body">
                        
                        <div class="mb-2 form-control" id="goto${iteration}">
                            ${AncConsultation(line, iteration, count)}
                            ${ viewer == 'nurse' && length == iteration ? updateAdmissionStatus(line, iteration) : ''}
                            ${investigations(line)}
                            ${viewer == '' ||  viewer == 'hmo' ? medicationAndTreatment(line) : viewer == 'nurse' ? medicationAndTreatmentNurses(line) : ''}
                            ${!viewer ? updateInvestigationAndManagement(length, iteration, line, isDoctorDone) : ''}

                            ${isDoctorDone ? '' :
                            `<div class="d-flex justify-content-start my-3 gap-2">
                                ${!viewer ? `
                                    <button type="button" id="fileBtn" data-id="${line.id}" data-visitid="${line.visitId}" class="btn btn-outline-primary">
                                        File
                                        <i class="bi bi-file-earmark-medical"></i>
                                    </button>` : ''}
                            </div>`}
                            <div class="extraInfoDiv" >
                                ${!viewer ? `
                                    <div class="my-2 form-control">
                                        <span class="fw-bold text-primary"> Other Documents </span>
                                        <div class="row overflow-auto m-1">
                                            ${ line.file === undefined ? '<td>No files</td>' : files(line.file)}
                                        </div>
                                    </div>` : ''}
                                ${length == iteration && viewer == '' ? 
                                `<div class="d-flex justify-content-between my-2">
                                    <button type="button" id="closeVisitBtn" data-id="${line.id}" class="btn btn-outline-primary">
                                        <i class="bi bi-trash"></i>
                                        Close Visit
                                    </button>
                                    <button type="button" id="deleteReviewConsultationBtn" data-id="${line.id}" class="btn btn-outline-primary">
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </button>
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

// 
{/* <tr>
<td>Lab</td>
<td>12/9/23 11:02pm</td>
<td>Dr Toby</td>
<td>Malaria Parasite</td>
<td></td>
<td><span class="position-relative"><a href="/transactions/11/receipts/15" target="blank" title="ABAGI Ernest_Nguevese.pdf">
<i class="bi bi-file-earmark-text download-receipt text-primary fs-4"></i></a></span></td>
<td><a role="button" class=""><i class="bi bi-upload"></i></a></td>
<td>12/09/23</td>
<td>Onjefu</td>
</tr> */}

export{regularReviewDetails, AncPatientReviewDetails}