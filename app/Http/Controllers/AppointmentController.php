<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\DatatablesService;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly AppointmentService $appointmentService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request, Patient $patient)
    {
        return $this->appointmentService->create($request, $patient, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->appointmentService->getPaginatedAppointments($params, $request, $request->user());
       
        $loadTransformer = $this->appointmentService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        return new AppointmentResource($appointment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        return $appointment->destroy($appointment->id);
    }
}
