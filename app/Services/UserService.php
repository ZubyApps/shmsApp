<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Designation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private readonly User $user, private readonly Designation $designation)
    {
    }

    public function create(Request $data, User $user): User
    {
        return $this->user->create([
            'firstname'             => $data->firstName,
            'middlename'            => $data->middleName,
            'lastname'              => $data->lastName,
            'username'              => $data->username,
            'phone_number'          => $data->phoneNumber,
            'email'                 => $data->email,
            'address'               => $data->address,
            'highest_qualification' => $data->highestQualification,
            'date_of_birth'         => $data->dateOfBirth,
            'sex'                   => $data->sex,
            'marital_status'        => $data->maritalStatus,
            'special_note'          => $data->specialNote,
            'state_of_origin'       => $data->stateOfOrigin,
            'next_of_kin'           => $data->nextOfKin,
            'next_of_kin_rship'     => $data->nextOfKinRship,
            'next_of_kin_phone'     => $data->nextOfKinPhone,
            'date_of_employment'    => $data->dateOfEmployment,
            'date_of_exit'          => $data->dateOfExit,
            'password'              => Hash::make($data->password),
            'created_by'            => $user->username,
        ]);
    }

    public function update(Request $data, User $user, User $updater): User
    {
        if ($data->password){
            $user->update([
             'firstname'             => $data->firstName1,
             'middlename'            => $data->middleName1,
             'lastname'              => $data->lastName1,
             'username'              => $data->username1,
             'phone_number'          => $data->phoneNumber1,
             'email'                 => $data->email1,
             'address'               => $data->address1,
             'highest_qualification' => $data->highestQualification1,
             'date_of_birth'         => $data->dateOfBirth1,
             'sex'                   => $data->sex1,
             'marital_status'        => $data->maritalStatus1,
             'state_of_origin'       => $data->stateOfOrigin1,
             'next_of_kin'           => $data->nextOfKin1,
             'next_of_kin_rship'     => $data->nextOfKinRship1,
             'next_of_kin_phone'     => $data->nextOfKinPhone1,
             'date_of_employment'    => $data->dateOfEmployment1,
             'date_of_exit'          => $data->dateOfExit1,
             'special_note'          => $data->specialNote1,
             'password'              => Hash::make($data->password),
             'created_by'            => $updater->username,
             ]);
        } else {
            $user->update([
                'firstname'             => $data->firstName1,
                'middlename'            => $data->middleName1,
                'lastname'              => $data->lastName1,
                'username'              => $data->username1,
                'phone_number'          => $data->phoneNumber1,
                'email'                 => $data->email1,
                'address'               => $data->address1,
                'highest_qualification' => $data->highestQualification1,
                'date_of_birth'         => $data->dateOfBirth1,
                'sex'                   => $data->sex1,
                'marital_status'        => $data->maritalStatus1,
                'state_of_origin'       => $data->stateOfOrigin1,
                'next_of_kin'           => $data->nextOfKin1,
                'next_of_kin_rship'     => $data->nextOfKinRship1,
                'next_of_kin_phone'     => $data->nextOfKinPhone1,
                'date_of_employment'    => $data->dateOfEmployment1,
                'date_of_exit'          => $data->dateOfExit1,
                'special_note'          => $data->specialNote1,
                'created_by'            => $updater->username,
                ]);
        }

        return $user;
    }

    public function getAllstaffList(DataTableQueryParams $params)
    {
        $orderBy    = 'firstname';
        $orderDir   =  'desc';
        $query = $this->user::with(['designation']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where('firstname', 'LIKE', $searchTerm )
                        ->orWhere('middlename', 'LIKE', $searchTerm )
                        ->orWhere('lastname', 'LIKE', $searchTerm )
                        ->orWhere('phone_number', 'LIKE', $searchTerm )
                        ->whereRelation('designation', 'access_level', '<', 5)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getAllStaffTransformer(): callable
    {
       return  function (User $user) {
            return [
                'id'                => $user->id,
                'designationId'     => $user->designation?->id,
                'name'              => $user->nameInFull(),
                'employed'          => $user->date_of_employment ? (new Carbon($user->date_of_employment))->format('d/m/Y g:ia') : '',
                'designation'       => $user?->designation?->designation,
                'lastLogin'         => $user->login ? (new Carbon($user->login))->format('d/m/Y g:ia') : '',
                'lastLogout'        => $user->logout ? (new Carbon($user->logout))->format('d/m/Y g:ia') : '',
                'qualification'     => $user->highest_qualification,
                'username'          => $user->username,
                'dateOfExit'        => $user->date_of_exit,
                'phone'             => $user->phone_number,
                'createdAt'         => (new Carbon($user->created_at))->format('d/m/Y'), 
                'hasDesignation'    => $user->designation != null, 
                'guard'             => $user->designation?->access_level > 5, 
                'superUser'         => auth()->user()->designation?->access_level > 5, 
            ];
         };
    }

    public function designate(Request $data, User $user, User $designator)
    {
        if($user->designation?->access_level > 5 && $designator->designation?->access_level < 6) {
            return response()->json(['message' => 'You cannot perform this action'], 403 );
        }
        return $this->designation->updateOrCreate(['user_id' => $user->id], 
        [
            'designation'  => $data->designation,
            'access_level' => $data->accessLevel,
            'designator'   => $designator->username
        ]);
    }

    public function listStaff(String $designation = null, string $special_note = null)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

        if ($special_note == 'Management'){
            return $this->user
                        ->where('special_note', $special_note)
                        ->orderBy($orderBy, $orderDir)
                        ->get(['id', 'username']);
        }
        
        if ($designation){
            return $this->user
                            ->where('date_of_exit', null)
                            ->whereRelation('designation', 'designation', '=', $designation)
                            ->orderBy($orderBy, $orderDir)
                            ->get(['id', 'username']);
        }

    }

    public function getActiveStaffList(DataTableQueryParams $params)
    {
        $orderBy    = 'firstname';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->user
                        ->where('is_active', true)
                        ->where('firstname', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middlename', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('lastname', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereRelation('designation', 'access_level', '<', 6)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->user
                    ->where('is_active', true)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getActiveStaffListTransformer(): callable
    {
       return  function (User $user) {
            return [
                'id'                => $user->id,
                'loggedIn'          => (new Carbon($user->login))->format('d/m/Y g:ia'),
                'name'              => $user->nameInFull(),
                'designation'       => $user?->designation?->designation,
                'phone'             => $user->phone_number,
            ];
         };
    }

    public function markForLogout(Request $request, User $userToLogOut)
    {
        $userToLogOut->update([
            'is_active' => false,
            'logout'    => new Carbon()
        ]);
        return;
    }
}