<?php

namespace Tests\Feature;

use App\Models\Salary;
use Tests\TestCase;

class SalaryTest extends TestCase
{
    public function test_admin_can_process_salary(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee();

        $this->actingAs($admin);
        $response = $this->post('/salaries', [
            'user_id' => $employee->id,
            'amount' => 5000.00,
            'period_start' => '2026-01-01',
            'period_end' => '2026-01-31',
        ]);
        $response->assertRedirect(route('salaries.index'));
        $this->assertDatabaseHas('salaries', [
            'user_id' => $employee->id,
            'amount' => 5000.00,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_mark_salary_paid(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee();

        $salary = Salary::create([
            'user_id' => $employee->id,
            'amount' => 5000.00,
            'period_start' => '2026-01-01',
            'period_end' => '2026-01-31',
            'status' => 'pending',
            'processed_by' => $admin->id,
        ]);

        $this->actingAs($admin);
        $response = $this->patch(route('salaries.pay', $salary));
        $response->assertRedirect(route('salaries.index'));
        $this->assertDatabaseHas('salaries', ['id' => $salary->id, 'status' => 'paid']);
    }

    public function test_employee_can_view_own_salary(): void
    {
        $employee = $this->createEmployee();
        $this->actingAs($employee);
        $response = $this->get('/salaries');
        $response->assertStatus(200);
    }

    public function test_employee_cannot_process_salary(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/salaries/create');
        $response->assertStatus(403);
    }
}
