<?php

namespace Database\Factories;

use App\Enum\PayClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SponsorCategory>
 */
class SponsorCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Family',
            'description' => 'Sponsored by the family,usully identified by the name of the pserson representing the family',
            'pay_class' => PayClass::from('Cash'),
            'approval'  => filter_var('false', FILTER_VALIDATE_BOOL),
            'bill_matrix' => '40',
            'balance_required' => filter_var('true', FILTER_VALIDATE_BOOL),
            'consultation_fee' => '1500'
        ];
    }
}
