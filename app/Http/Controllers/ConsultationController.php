<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\StoreConsultationReviewRequest;
use App\Http\Requests\UpdateAdmissionStatusRequest;
use App\Http\Requests\UpdateConsultationRequest;
use App\Http\Resources\ConsultationReviewCollection;
use App\Http\Resources\LatestLmpResource;
use App\Http\Resources\PatientBioResource;
use App\Http\Resources\PatientHistoryBioResource;
use App\Http\Resources\VisitCollection;
use App\Models\Patient;
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

        return ["consultations" => new ConsultationReviewCollection($consultations), "bio" => new PatientBioResource($visit), "latestLmp" => new LatestLmpResource($visit)];
    }

    public function loadVisitsAndConsultations(Request $request, Patient $patient)
    {
        $visits = $this->consultationService->getVisitsAndConsultations($request, $patient);
        
        return ["visits" => new VisitCollection($visits), "bio" => new PatientHistoryBioResource($patient)];

    }

    public function updateAdmissionStatus(UpdateAdmissionStatusRequest $request, Consultation $consultation)
    {
        return $this->consultationService->updateAdmissionStatus($consultation, $request, $request->user());
    }
    
    public function destroy(Consultation $consultation)
    {
       return DB::transaction(function () use ($consultation) {
                    $consultation->visit->consultations->count() < 2 ? $consultation->visit->update(['consulted' => null]) : '' ;
                    $consultation->destroy($consultation->id);
                }, 2);

    }
}
