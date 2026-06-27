<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_employee_can_access_dashboard(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_employee_can_access_attendance(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/attendance');
        $response->assertStatus(200);
    }

    public function test_employee_can_access_stock(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/stock');
        $response->assertStatus(200);
    }

    public function test_employee_cannot_access_employee_management(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/employees');
        $response->assertStatus(403);
    }

    public function test_employee_cannot_access_salaries(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/salaries');
        $response->assertStatus(403);
    }

    public function test_employee_cannot_access_activity_logs(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/logs/activity');
        $response->assertStatus(403);
    }

    public function test_manager_can_access_employees(): void
    {
        $this->actingAs($this->createManager());
        $response = $this->get('/employees');
        $response->assertStatus(200);
    }

    public function test_manager_cannot_access_salaries(): void
    {
        $this->actingAs($this->createManager());
        $response = $this->get('/salaries');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_employees(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->get('/employees');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_salaries(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->get('/salaries');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_activity_logs(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->get('/logs/activity');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_device_logs(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->get('/logs/devices');
        $response->assertStatus(200);
    }
}
