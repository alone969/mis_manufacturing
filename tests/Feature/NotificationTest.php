<?php

namespace Tests\Feature;

use App\Models\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_user_can_view_notifications(): void
    {
        $user = $this->createEmployee();
        $this->actingAs($user);
        $response = $this->get('/notifications');
        $response->assertStatus(200);
    }

    public function test_user_can_mark_notification_read(): void
    {
        $user = $this->createEmployee();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'shift_assigned',
            'title' => 'Shift Assigned',
            'message' => 'You have been assigned',
            'is_read' => false,
        ]);

        $this->actingAs($user);
        $response = $this->put("/notifications/{$notification->id}/read");
        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'is_read' => true]);
    }

    public function test_user_can_mark_all_notifications_read(): void
    {
        $user = $this->createEmployee();
        Notification::create([
            'user_id' => $user->id, 'type' => 'system_announcement',
            'title' => 'Test', 'message' => 'Test', 'is_read' => false,
        ]);

        $this->actingAs($user);
        $response = $this->put('/notifications/read-all');
        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'is_read' => true]);
    }
}
