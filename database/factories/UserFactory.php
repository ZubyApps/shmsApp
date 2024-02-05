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
            'firstname' => 'User',
            'middlename' => 'Super',
            'lastname' => 'Admin',
            'email' => 'admin@example.com',
            'address' => '24 J.S Tarka Way, Wadata',
            'date_of_birth' => new DateTime('1982/06/28'),
            'highest_qualification' => 'MBBS',
            'sex' => 'male',
            'marital_status' => 'Married',
            'username' => 'Admin User',
            'phone_number' => '08022812281',
            'state_of_origin' => 'Anambra',
            'next_of_kin' => 'Nzube Okoye',
            'next_of_kin_rship' => 'Son',
            'next_of_kin_phone' => '08035999029',
            'date_of_employment' => new DateTime('1982/06/28'),
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
