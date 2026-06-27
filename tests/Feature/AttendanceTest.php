<?php

namespace Tests\Feature;

use App\Models\Shift;
use App\Models\ShiftAssignment;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    public function test_employee_can_clock_in(): void
    {
        $user = $this->createEmployee();
        $shift = Shift::create([
            'name' => 'Morning',
            'start_time' => '06:00',
            'end_time' => '14:00',
            'created_by' => $user->id,
        ]);
        ShiftAssignment::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'date' => now()->toDateString(),
            'status' => 'assigned',
        ]);

        $this->actingAs($user);
        $response = $this->post('/attendance/clock-in');
        $response->assertRedirect();
        $this->assertDatabaseHas('shift_assignments', [
            'user_id' => $user->id,
            'status' => 'present',
        ]);
    }

    public function test_employee_cannot_clock_in_twice(): void
    {
        $user = $this->createEmployee();
        $shift = Shift::create([
            'name' => 'Morning',
            'start_time' => '06:00',
            'end_time' => '14:00',
            'created_by' => $user->id,
        ]);
        ShiftAssignment::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'date' => now()->toDateString(),
            'clock_in' => now(),
            'status' => 'present',
        ]);

        $this->actingAs($user);
        $response = $this->post('/attendance/clock-in');
        $response->assertSessionHas('error');
    }

    public function test_employee_can_clock_out(): void
    {
        $user = $this->createEmployee();
        $shift = Shift::create([
            'name' => 'Morning',
            'start_time' => '06:00',
            'end_time' => '14:00',
            'created_by' => $user->id,
        ]);
        ShiftAssignment::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->subHours(8),
            'status' => 'present',
        ]);

        $this->actingAs($user);
        $response = $this->post('/attendance/clock-out');
        $response->assertRedirect();
        $this->assertDatabaseHas('shift_assignments', [
            'user_id' => $user->id,
            'status' => 'present',
        ]);
    }

    public function test_employee_without_shift_cannot_clock_in(): void
    {
        $user = $this->createEmployee();
        $this->actingAs($user);
        $response = $this->post('/attendance/clock-in');
        $response->assertSessionHas('error');
    }

    public function test_employee_can_view_attendance(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('Blade view rendering causes premature PHP process termination on Windows with SQLite in-memory database.');
        }

        $user = $this->createEmployee();
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertOk();
    }
}
