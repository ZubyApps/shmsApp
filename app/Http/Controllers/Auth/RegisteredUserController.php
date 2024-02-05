<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Designation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\DatatablesService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly UserService $userService
        )
    {
        
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.newstaff');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->create($request, $request->user());    

        event(new Registered($user));

        // Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function loadAllUsers(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->userService->getPaginatedUsers($params);
       
        $loadTransformer = $this->userService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function edit(User $user)
    {
        if ($user->designation?->access_level > 4) {
            return response()->json(['message' => 'You are not authorized'], 403);
        }
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, user $user)
    {        
        return $this->userService->update($request, $user, $request->user());

    }

    public function assignDesignation(StoreDesignationRequest $request, User $user)
    {
        return $this->userService->designate($request, $user, $request->user());
    }

    public function removeDesignation(Designation $designation)
    {
        return $designation->destroy($designation->id);
    }

    public function destroy(User $user)
    {
        return $user->destroy($user->id);
    }
}
