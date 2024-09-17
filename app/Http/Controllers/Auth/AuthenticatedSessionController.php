<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\StorePatientRequest;
use App\Models\PatientPreForm;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\PrePatientService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private readonly PatientPreForm $patientPreForm,
        private readonly PrePatientService $prePatientService
        )
    {
        
    }
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function createPatients(Request $request, PatientPreForm $patientPreForm): View
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        return view('auth.patientRegistration', ['preForm' => $patientPreForm]);
    }

    public function submitPatient(StorePatientRequest $request, PatientPreForm $patientPreForm)
    {
        return $this->prePatientService->updateForm($request, $patientPreForm);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->update([
            'is_active' => false,
            'logout'    => new Carbon()
        ]);

        Auth::guard('web')->logout();


        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
