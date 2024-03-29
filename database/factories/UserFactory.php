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
            'email' => 'zubyokoye@gmail.com',
            'address' => '24 J.S Tarka Way, Wadata',
            'date_of_birth' => new DateTime('1983/03/05'),
            'highest_qualification' => 'BTech',
            'sex' => 'male',
            'marital_status' => 'Married',
            'username' => 'Nzube',
            'phone_number' => '08035999029',
            'state_of_origin' => 'Anambra',
            'next_of_kin' => 'Stephanie Okoye',
            'next_of_kin_rship' => 'Wife',
            'next_of_kin_phone' => '08103830241',
            'date_of_employment' => new DateTime('1983/03/05'),
            'special_note' => 'Management',
            'password' => Hash::make('Mylovelywife'),
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
