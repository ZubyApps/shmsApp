<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Designation;
use App\Models\SponsorCategory;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
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
       $user->update([
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
        'state_of_origin'       => $data->stateOfOrigin,
        'next_of_kin'           => $data->nextOfKin,
        'next_of_kin_rship'     => $data->nextOfKinRship,
        'next_of_kin_phone'     => $data->nextOfKinPhone,
        'date_of_employment'    => $data->dateOfEmployment,
        'date_of_exit'          => $data->dateOfExit,
        'password'              => Hash::make($data->password),
        'created_by'            => $updater->username,

        ]);

        return $user;
    }

    public function getPaginatedUsers(DataTableQueryParams $params)
    {
        $orderBy    = 'firstname';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->user
                        ->where('firstname', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middlename', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('lastname', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->whereRelation('designation', 'access_level', '<', 5)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->user
                    ->where('id', '!=', 1)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (User $user) {
            return [
                'id'                => $user->id,
                'designationId'     => $user->designation?->id,
                'name'              => $user->nameInFull(),
                'employed'          => $user->date_of_employment ? (new Carbon($user->date_of_employment))->format('d/m/Y g:ia') : '',
                'designation'       => $user?->designation?->designation,
                'lastLogin'         => $user->login ? (new Carbon($user->login))->format('d/m/Y g:ia') : '',
                'qualification'     => $user->highest_qualification,
                'username'          => $user->username,
                'phone'             => $user->phone_number,
                'address'           => $user->address,
                'createdAt'         => (new Carbon($user->created_at))->format('d/m/Y'),
                'count'             => $user->patients()->count() 
            ];
         };
    }

    public function designate(Request $data, User $user, User $designator)
    {
        return $this->designation->updateOrCreate(['user_id' => $user->id], 
        [
            'designation'  => $data->designation,
            'access_level' => $data->accessLevel,
            'designator'   => $designator->username
        ]);
    }
}