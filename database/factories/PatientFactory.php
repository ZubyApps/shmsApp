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
            "patient_type"          => 'regular',
            "address"               => $this->faker->address(),
            "blood_group"           => $this->faker->bloodGroup(),
            "card_no"               => $this->faker->unique()->numberBetween(0001,9999),
            "date_of_birth"         => new Carbon('1983-03-05'),
            "email"                 => $this->faker->unique()->safeEmail(),
            "ethnic_group"          => 'Igbo',
            "first_name"            => $this->faker->firstName(),
            "genotype"              => 'AA',
            "known_conditions"      => '',
            "last_name"             => $this->faker->lastName(),
            "marital_Status"        => 'Married',
            "middle_name"           => $this->faker->name(),
            "nationality"           => 'Nigerian',
            "next_of_kin"           => $this->faker->name(),
            "next_of_kin_phone"     => $this->faker->phoneNumber(),
            "next_of_kin_rship"     => 'Wife',
            "occupation"            => 'Computer Programmer',
            "phone"                 => $this->faker->phoneNumber(),
            "registration_bill"     => '2000',
            "religion"              => $this->faker->word(),
            "sex"                   => $this->faker->word(),
            "sponsor_id"            => '1',
            "staff_Id"              => '',
            "state_of_origin"       => 'Anambra',
            "state_of_residence"    => 'Benue',
            "user_id"               => '1',
        ];
    }
}
