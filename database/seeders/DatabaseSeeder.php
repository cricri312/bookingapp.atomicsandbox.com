<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@bookingapp.com',
        ]);

        User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'test2@bookingapp.com',
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@bookingapp.com',
            'is_admin' => true,
        ]);

        $this->call(RoomSeeder::class);
    }
}
