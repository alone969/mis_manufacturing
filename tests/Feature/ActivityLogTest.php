<?php

namespace Tests\Feature;

use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    public function test_admin_can_view_activity_logs(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->get('/logs/activity');
        $response->assertStatus(200);
    }

    public function test_employee_cannot_view_activity_logs(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/logs/activity');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_device_logs(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->get('/logs/devices');
        $response->assertStatus(200);
    }

    public function test_employee_cannot_view_device_logs(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/logs/devices');
        $response->assertStatus(403);
    }
}
