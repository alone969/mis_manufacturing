<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salary>
 */
class SalaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periodStart = fake()->dateTimeBetween('-6 months', 'now');
        $basicSalary = fake()->randomFloat(2, 1000, 10000);
        $bonus = fake()->randomFloat(2, 0, 2000);
        $deductions = fake()->randomFloat(2, 0, 1000);

        return [
            'user_id' => User::factory(),
            'basic_salary' => $basicSalary,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'net_salary' => $basicSalary + $bonus - $deductions,
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => (clone $periodStart)->modify('+1 month')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'paid']),
            'processed_by' => User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
