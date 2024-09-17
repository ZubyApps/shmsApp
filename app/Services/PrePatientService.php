<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\DataObjects\FormLinkParams;
use App\Models\Patient;
use App\Models\PatientPreForm;
use App\Models\User;
use App\Notifications\FormLinkNotifier;
use App\Notifications\PatientCardNumber;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class PrePatientService
{
    public function __construct(
        private readonly Patient $patient, 
        private readonly HelperService $helperService, 
        private readonly PatientCardNumber $patientCardNumber,
        private readonly FormLinkNotifier $formLinkNotifier,
        private readonly PatientPreForm $patientPreForm
        )
    {
    }

    public function updateForm(Request $data, PatientPreForm $patientPreForm): PatientPreForm
    {
        return DB::transaction(function () use($data, $patientPreForm){
            $patientPreForm->update([
                    "patient_type"          => $data->patientType,
                    "address"               => $data->address,
                    "blood_group"           => $data->bloodGroup,
                    "card_no"               => $data->cardNumber,
                    "date_of_birth"         => $data->dateOfBirth,
                    "email"                 => $data->email,
                    "ethnic_group"          => $data->ethnicGroup,
                    "first_name"            => $data->firstName,
                    "genotype"              => $data->genotype,
                    "known_conditions"      => $data->knownConditions,
                    "last_name"             => $data->lastName,
                    "marital_status"        => $data->maritalStatus,
                    "middle_name"           => $data->middleName,
                    "nationality"           => $data->nationality,
                    "next_of_kin"           => $data->nextOfKin,
                    "next_of_kin_phone"     => $data->nextOfKinPhone,
                    "next_of_kin_rship"     => $data->nextOfKinRship,
                    "occupation"            => $data->occupation,
                    "phone"                 => $data->phone,
                    "registration_bill"     => $data->registrationBill,
                    "religion"              => $data->religion,
                    "sex"                   => $data->sex,
                    "sponsor_id"            => $data->sponsor,
                    "sponsor_category"      => $data->sponsorCategory,
                    "staff_Id"              => $data->staffId,
                    "state_of_origin"       => $data->stateOrigin,
                    "state_of_residence"    => $data->stateResidence,
            ]);
    
            return $patientPreForm;
        });
    }

    public function sendFormLink($data, User $user)
    {
        $notifiable  = new FormLinkParams(
            config('app.url').'/form?', 
            (int)$data->sponsorCategory, 
            (int)$data->sponsor, 
            $data->cardNumber, 
            $data->patientType, 
            $data->phone, 
            $user->id
        );
        
        // $link2 = route('patientForm',
        //         [
        //             'sponsorCategory'   => $notifiable->sponsorCat,
        //             'sponsor'           => $notifiable->sponsor,
        //             'cardNumber'        => $notifiable->cardNumber,
        //             'patientType'       => $notifiable->patientType,
        //             'phone'             => $notifiable->phone,
        //             'user'              => $notifiable->userId
        //         ]
        //     );

        $patientForm = $this->patientPreForm->create(
            [
                'sponsor_category'  => $notifiable->sponsorCat,
                'sponsor'           => $notifiable->sponsor,
                'card_no'           => $notifiable->cardNumber,
                'patient_type'      => $notifiable->patientType,
                'phone'             => $notifiable->phone,
                'user_id'           => $notifiable->userId
            ]
            );

        $signedLink = URL::temporarySignedRoute('patientForm', now()->addMinutes(5), ['patientPreForm' => $patientForm->id]);

        // $patientForm->update(['short_link' => $signedLink]);

        // $link = $notifiable->linkBaseUrl.'sponsorCategory='. $notifiable->sponsorCat.'&sponsor='. $notifiable->sponsor.'&cardNumber='. $notifiable->cardNumber . '&patientType='. $notifiable->patientType. '&phone='. $notifiable->phone. '&user='. $notifiable->userId;

        Log::info('form link', ['signed' => $signedLink]);
        $this->formLinkNotifier->toSms($notifiable, $signedLink, $notifiable->phone);
        if ($this->helperService->nccTextTime()){
        }
    }

    public function getPaginatedPrePatients(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->patientPreForm
                        ->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('date_of_birth', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->patientPreForm
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (PatientPreForm $patientPreForm) {
            return [
                'id'                => $patientPreForm->id,
                'card'              => $patientPreForm->card_no,
                'patient'           => $patientPreForm->fullName(),
                'phone'             => $patientPreForm->phone,
                'sex'               => $patientPreForm->sex,
                'age'               => $this->helperService->twoPartDiffInTimePast($patientPreForm->date_of_birth),
                'sponsor'           => $patientPreForm->sponsor->name,
                'category'          => $patientPreForm->sponsor->sponsorCategory->name,
                'flagSponsor'       => $patientPreForm->sponsor->flag,
                'flagPatient'       => $patientPreForm->flag,
                'flagReason'        => $patientPreForm->flag_reason,
                'createdAt'         => (new Carbon($patientPreForm->created_at))->format('d/m/Y'),
                // 'createdBy'         => $patientPreForm->user->username,
                'active'            => $patientPreForm->is_active,
                // 'count'             => $patientPreForm->visits()->count(),
                // 'patient'           => $patientPreForm->patientId()
            ];
         };
    }
}
