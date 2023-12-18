<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "patient_type"          => 'Regular',
            "address"               => 'Flat 1, behind Larazon Annex, Achusa',
            "blood_group"           => 'O+',
            "card_no"               => 'SH23/0001',
            "date_of_birth"         => new Carbon('1992-10-02'),
            "email"                 => 'obioma@gmial.com',
            "ethnic_group"          => 'Ejagam',
            "first_name"            => 'Obioma',
            "genotype"              => 'AA',
            "known_conditions"      => '',
            "last_name"             => 'Omenka',
            "marital_Status"        => 'Married',
            "middle_name"           => 'Josephine',
            "nationality"           => 'Nigerian',
            "next_of_kin"           => 'Jonathan Omenka',
            "next_of_kin_phone"     => '08039989091',
            "next_of_kin_rship"     => 'Husband',
            "occupation"            => 'Banker',
            "phone"                 => '08038724121',
            "registration_bill"     => '2000',
            "religion"              => 'Christianity',
            "sex"                   => 'Female',
            "sponsor_id"            => '1',
            "staff_Id"              => '',
            "state_of_origin"       => 'Enugu',
            "state_of_residence"    => 'FCT',
            "user_id"               => '1',
        ];
    }
}
