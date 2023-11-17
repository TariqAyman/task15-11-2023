<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 50, 500),
            'payer_id' => User::factory(),
            'due_on' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'vat' => fake()->randomFloat(2, 5, 20),
            'is_vat_inclusive' => fake()->boolean,
            'status' => fake()->randomElement(['paid', 'outstanding', 'overdue']),
            'total_paid_amount' => fake()->randomFloat(2, 0, 500),
            'created_by' => User::factory(),
        ];
    }
}
