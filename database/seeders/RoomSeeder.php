<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Sala Konferencyjna A', 'capacity' => 10],
            ['name' => 'Sala Konferencyjna B', 'capacity' => 20],
            ['name' => 'Sala Szkoleniowa', 'capacity' => 30],
            ['name' => 'Pokój Spotkań 1', 'capacity' => 7],
            ['name' => 'Pokój Spotkań 2', 'capacity' => 8],
            ['name' => 'Pokój Spotkań 3', 'capacity' => 9],
            ['name' => 'Pokój Spotkań 4', 'capacity' => 10],
            ['name' => 'Pokój Spotkań 5', 'capacity' => 11],
            ['name' => 'Pokój Spotkań 6', 'capacity' => 12],
            ['name' => 'Pokój Spotkań 7', 'capacity' => 13],
            ['name' => 'Pokój Spotkań 8', 'capacity' => 14],
            ['name' => 'Pokój Spotkań 9', 'capacity' => 15],
            ['name' => 'Pokój Spotkań 10', 'capacity' => 16],
            ['name' => 'Pokój Spotkań 11', 'capacity' => 17],
            ['name' => 'Pokój Spotkań 12', 'capacity' => 18],
            ['name' => 'Pokój Spotkań 13', 'capacity' => 19],
            ['name' => 'Pokój Spotkań 14', 'capacity' => 20],
            ['name' => 'Pokój Spotkań 15', 'capacity' => 21],
            ['name' => 'Pokój Spotkań 16', 'capacity' => 22],
            ['name' => 'Pokój Spotkań 17', 'capacity' => 23],
            ['name' => 'Pokój Spotkań 18', 'capacity' => 24],
            ['name' => 'Pokój Spotkań 19', 'capacity' => 25],
            ['name' => 'Pokój Spotkań 20', 'capacity' => 26],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
