<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sponsor>
 */
class SponsorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'name' => 'Nzube Okoye',
            'phone' => '08035999029',
            'email' => 'support@axamansard.com',
            'registration_bill' => '3500',
            'sponsor_category_id' => 1
        ];
    }
}
