<?php

namespace Tests\Feature;

use App\Models\Shift;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    public function test_admin_can_create_shift(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->post('/shifts', [
            'name' => 'Morning Shift',
            'start_time' => '06:00',
            'end_time' => '14:00',
        ]);
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseHas('shifts', ['name' => 'Morning Shift']);
    }

    public function test_manager_can_create_shift(): void
    {
        $this->actingAs($this->createManager());
        $response = $this->post('/shifts', [
            'name' => 'Night Shift',
            'start_time' => '22:00',
            'end_time' => '06:00',
        ]);
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseHas('shifts', ['name' => 'Night Shift']);
    }

    public function test_admin_can_delete_shift(): void
    {
        $admin = $this->createAdmin();
        $shift = Shift::create([
            'name' => 'To Delete',
            'start_time' => '10:00',
            'end_time' => '18:00',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);
        $response = $this->delete("/shifts/{$shift->id}");
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseMissing('shifts', ['id' => $shift->id]);
    }

    public function test_manager_can_update_shift(): void
    {
        $manager = $this->createManager();
        $shift = Shift::create([
            'name' => 'Old Name',
            'start_time' => '10:00',
            'end_time' => '18:00',
            'created_by' => $manager->id,
        ]);

        $this->actingAs($manager);
        $response = $this->put("/shifts/{$shift->id}", [
            'name' => 'New Name',
        ]);
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseHas('shifts', ['id' => $shift->id, 'name' => 'New Name']);
    }

    public function test_manager_can_view_shift_schedule(): void
    {
        $this->actingAs($this->createManager());
        $response = $this->get('/shifts/schedule');
        $response->assertStatus(200);
    }
}
