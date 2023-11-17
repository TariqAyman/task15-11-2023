<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'amount' => fake()->randomFloat(2, 10, 200),
            'paid_on' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
            'details' => fake()->sentence,
            'created_by' => User::factory(),
        ];
    }
}
