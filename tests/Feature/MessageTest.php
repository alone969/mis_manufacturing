<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Tests\TestCase;

class MessageTest extends TestCase
{
    public function test_user_can_view_inbox(): void
    {
        $user = $this->createEmployee();
        $this->actingAs($user);
        $response = $this->get('/messages');
        $response->assertStatus(200);
    }

    public function test_user_can_compose_message(): void
    {
        $sender = $this->createEmployee();
        $receiver = User::factory()->create([
            'role' => 'employee', 'is_email_verified' => true, 'onboarding_status' => 'active',
        ]);
        $receiver->assignRole('employee');

        $this->actingAs($sender);
        $response = $this->post('/messages', [
            'receiver_id' => $receiver->id,
            'subject' => 'Test Message',
            'body' => 'This is a test message body.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'subject' => 'Test Message',
        ]);
    }

    public function test_user_can_view_message(): void
    {
        $sender = $this->createEmployee();
        $receiver = User::factory()->create([
            'role' => 'employee', 'is_email_verified' => true, 'onboarding_status' => 'active',
        ]);
        $receiver->assignRole('employee');

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'subject' => 'View Test',
            'body' => 'Body content',
        ]);

        $this->actingAs($sender);
        $response = $this->get("/messages/{$message->id}");
        $response->assertStatus(200);
    }

    public function test_user_can_delete_own_sent_message(): void
    {
        $sender = $this->createEmployee();
        $receiver = User::factory()->create([
            'role' => 'employee', 'is_email_verified' => true, 'onboarding_status' => 'active',
        ]);
        $receiver->assignRole('employee');

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'subject' => 'Delete Me',
            'body' => 'Goodbye',
        ]);

        $this->actingAs($sender);
        $response = $this->delete("/messages/{$message->id}");
        $response->assertRedirect(route('messages.index'));
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'is_deleted_by_sender' => true]);
    }
}
