<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use DateTime;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        //return view('auth.register');
        return view('auth.newstaff');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'firstName'             => ['required', 'string', 'max:255'],
            'middleName'            => ['string', 'max:255'],
            'lastName'              => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:255', 'unique:' . User::class],
            'phone_no'              => ['required', 'numeric', 'max:11', 'unique:' . User::class],
            'phone_no'              => ['required', 'numeric', 'min:11', 'unique:' . User::class],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'address'               => ['string', 'max:255'],
            'highestQualification'  => ['required', 'string', 'max:255'],
            'dateOfBirth'           => ['required', 'date'],
            'sex'                   => ['required', 'string', 'max:255'],
            'maritalStatus'         => ['required', 'string', 'max:255'],
            'stateOfOrigin'         => ['required', 'string', 'max:255'],
            'nextOfKin'             => ['required', 'string', 'max:255'],
            'nextOfKinPhone'        => ['required', 'numeric', 'max:11'],
            'nextOfKinPhone'        => ['required', 'numeric', 'min:11'],
            'nextOfKinRship'        => ['required', 'string'],
            'dateOfEmployment'      => ['required', 'nullable', 'date'],
            'dateOfExit'            => ['nullable', 'date'],
            'department'            => ['nullable', 'string'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'firstname'             => $request->firstName,
            'middlename'            => $request->middleName,
            'lastname'              => $request->lastName,
            'username'              => $request->username,
            'phone_no'              => $request->phone_no,
            'email'                 => $request->email,
            'address'               => $request->address,
            'highest_qualification' => $request->highestQualification,
            'date_of_birth'         => new DateTime($request->dateOfBirth),
            'sex'                   => $request->sex,
            'marital_status'        => $request->maritalStatus,
            'state_of_origin'       => $request->stateOfOrigin,
            'next_of_kin'           => $request->nextOfKin,
            'next_of_kin_rship'     => $request->nextOfKinRship,
            'next_of_kin_phone'     => $request->nextOfKinPhone,
            'date_of_employment'    => new DateTime($request->dateOfEmployment),
            'date_of_exit'          => new DateTime($request->dateOfExit),
            'password'              => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
