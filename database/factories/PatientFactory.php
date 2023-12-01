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
            "date_of_birth"         => new Carbon('1988-11-12'),
            "email"                 => 'nyen@gmial.com',
            "ethnic_group"          => 'Ejagam',
            "first_name"            => 'Stephanie',
            "genotype"              => 'AA',
            "known_conditions"      => '',
            "last_name"             => 'Okoye',
            "marital_Status"        => 'Married',
            "middle_name"           => 'Nyen',
            "nationality"           => 'Nigerian',
            "next_of_kin"           => 'Nzube Okoye',
            "next_of_kin_phone"     => '08035999029',
            "next_of_kin_rship"     => 'Husband',
            "occupation"            => 'Computer Programmer',
            "phone"                 => '08103830241',
            "registration_bill"     => '2000',
            "religion"              => 'Christianity',
            "sex"                   => 'Female',
            "sponsor_id"            => '1',
            "staff_Id"              => '',
            "state_of_origin"       => 'Cross River',
            "state_of_residence"    => 'Benue',
            "user_id"               => '1',
        ];
    }
}
