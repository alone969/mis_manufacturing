<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Otp>
 */
class OtpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => fake()->numerify('######'),
            'purpose' => fake()->randomElement(['password_reset', 'email_verification', 'login']),
            'expires_at' => now()->addMinutes(10),
            'used_at' => null,
        ];
    }
}
