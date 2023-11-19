<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use App\Http\Resources\ConsultationReviewCollection;
use App\Http\Resources\PatientBioResource;
use App\Models\Visit;
use App\Services\ConsultationService;
use App\Services\DatatablesService;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ConsultationService $consultationService)
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsultationRequest $request)
    {
        $consultation = $this->consultationService->create($request, $request->user());
        
        return $consultation->load('visit');
    }

    public function loadConsultations(Request $request, Visit $visit)
    {
        $consultations = $this->consultationService->getConsultations($request, $visit);

        return ["consultations" => new ConsultationReviewCollection($consultations), "bio" => new PatientBioResource($visit)];
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Consultation $consultation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consultation $consultation)
    {
        //
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
        return $consultation->destroy($consultation->id);
    }
}
