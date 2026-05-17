<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking(): void
    {
        $user = User::factory()->create();
        $room = Room::create(['name' => 'Sala A', 'capacity' => 10]);

        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'            => $room->id,
            'starts_at'          => '2030-01-01 10:00:00',
            'ends_at'            => '2030-01-01 12:00:00',
            'participants_count' => 5,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', ['room_id' => $room->id, 'user_id' => $user->id]);
    }

    public function test_cannot_book_room_with_conflicting_reservation(): void
    {
        $user = User::factory()->create();
        $room = Room::create(['name' => 'Sala B', 'capacity' => 10]);

        Booking::create([
            'room_id'            => $room->id,
            'user_id'            => $user->id,
            'starts_at'          => '2030-02-01 09:00:00',
            'ends_at'            => '2030-02-01 11:00:00',
            'participants_count' => 3,
            'status'             => 'confirmed',
        ]);

        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'            => $room->id,
            'starts_at'          => '2030-02-01 10:00:00',
            'ends_at'            => '2030-02-01 12:00:00',
            'participants_count' => 3,
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_book_room_exceeding_capacity(): void
    {
        $user = User::factory()->create();
        $room = Room::create(['name' => 'Sala C', 'capacity' => 4]);

        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'            => $room->id,
            'starts_at'          => '2030-03-01 10:00:00',
            'ends_at'            => '2030-03-01 12:00:00',
            'participants_count' => 10,
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_cancel_own_booking(): void
    {
        $user = User::factory()->create();
        $room = Room::create(['name' => 'Sala D', 'capacity' => 10]);

        $booking = Booking::create([
            'room_id'            => $room->id,
            'user_id'            => $user->id,
            'starts_at'          => '2030-04-01 10:00:00',
            'ends_at'            => '2030-04-01 12:00:00',
            'participants_count' => 3,
            'status'             => 'pending',
        ]);

        $response = $this->actingAs($user)->patchJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
    }

    public function test_user_cannot_cancel_other_users_booking(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $room  = Room::create(['name' => 'Sala E', 'capacity' => 10]);

        $booking = Booking::create([
            'room_id'            => $room->id,
            'user_id'            => $owner->id,
            'starts_at'          => '2030-05-01 10:00:00',
            'ends_at'            => '2030-05-01 12:00:00',
            'participants_count' => 3,
            'status'             => 'pending',
        ]);

        $response = $this->actingAs($other)->patchJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(403);
    }
}
