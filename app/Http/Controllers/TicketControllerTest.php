<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_create_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/requests', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'message' => $this->faker->sentence,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function test_user_can_update_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $ticket = Ticket::factory()->create(['responsible_user_id' => $user->id]);

        $response = $this->putJson('/api/requests/' . $ticket->id, [
            'comment' => $this->faker->sentence,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'comment' => $response['comment'],
        ]);
    }

    public function test_user_can_delete_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $ticket = Ticket::factory()->create(['responsible_user_id' => $user->id]);

        $response = $this->deleteJson('/api/requests/' . $ticket->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_user_can_attach_file_to_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $ticket = Ticket::factory()->create(['responsible_user_id' => $user->id]);

        $response = $this->postJson('/api/requests/' . $ticket->id . '/attach-file', [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(200);

    }
}
