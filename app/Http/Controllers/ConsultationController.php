<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\StoreConsultationReviewRequest;
use App\Http\Requests\UpdateAdmissionStatusRequest;
use App\Http\Requests\UpdateConsultationRequest;
use App\Http\Resources\ConsultationReviewCollection;
use App\Http\Resources\PatientBioResource;
use App\Models\Visit;
use App\Services\ConsultationService;
use App\Services\DatatablesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ConsultationService $consultationService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsultationRequest $request)
    {
        $consultation = $this->consultationService->create($request, $request->user());
        
        return $consultation->load('visit');
    }

    public function storeReview(StoreConsultationReviewRequest $request)
    {
        $consultation = $this->consultationService->create($request, $request->user());
        
        return $consultation->load('visit');
    }

    public function loadConsultations(Request $request, Visit $visit)
    {
        $consultations = $this->consultationService->getConsultations($request, $visit);

        return ["consultations" => new ConsultationReviewCollection($consultations), "bio" => new PatientBioResource($visit)];
    }

    public function updateAdmissionStatus(UpdateAdmissionStatusRequest $request, Consultation $consultation)
    {
        return $this->consultationService->updateAdmissionStatus($consultation, $request, $request->user());
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsultationRequest $request, Consultation $consultation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consultation $consultation)
    {
       return DB::transaction(function () use ($consultation) {
                    $consultation->visit->consultations->count() < 2 ? $consultation->visit->update(['consulted' => null]) : '' ;
                    $consultation->destroy($consultation->id);
                }, 2);

    }
}
