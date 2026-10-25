<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token()
    {
        $user = User::factory()->create([
            'email' => 'admin@myevent.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@myevent.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token']);
    }

    public function test_authenticated_user_can_create_event()
    {
        $user = User::factory()->create();

        $eventData = [
            'title' => 'Seminar PPL 2026',
            'description' => 'Pembahasan CI/CD dan Docker',
            'location' => 'Gedung SV UGM',
            'event_date' => '2026-11-01',
            'quota' => 100,
            'price' => 50000,
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/events', $eventData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Seminar PPL 2026']);
    }
}