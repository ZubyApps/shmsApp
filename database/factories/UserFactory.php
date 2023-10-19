<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => 'Nzube',
            'middlename' => 'Yonna',
            'lastname' => 'Okoye',
            'email' => 'zuby@example.com',
            'address' => 'Flat 1 Behind Larazon Annex, Achusa',
            'date_of_birth' => new DateTime('1983/03/05'),
            'highest_qualification' => 'B.Tech',
            'sex' => 'male',
            'marital_status' => 'married',
            'username' => 'Mr Nzube',
            'phone_no' => '08035999029',
            'state_of_origin' => 'Anambra',
            'next_of_kin' => 'Stephanie Okoye',
            'next_of_kin_rship' => 'wife',
            'next_of_kin_phone' => '08103830241',
            'date_of_employment' => new DateTime('2018/02/01'),
            'password' => Hash::make('mylovelywife'), // password
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
