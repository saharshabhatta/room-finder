<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('room_types')->insert([
            ['name'=>'Single Room ',
                'description'=>'One room for one person'],
            ['name'=>'Double Room ',
                'description'=>'One room for two people'],
            ['name'=>'Shared Room ',
                'description'=>'A room shared by multiple occupants'],
            ['name'=>'Studio ',
                'description'=>'A single room that combines living room, bedroom, and kitchen'],
            ['name'=>'1BHK',
                'description'=>' One Bedroom, Hall, Kitchen'],
            ['name'=>'2BHK',
                'description'=>' Two Bedroom, Hall, Kitchen'],
            ['name'=>'3BHK',
                'description'=>' Three Bedroom, Hall, Kitchen'],
            ['name'=>'Serviced Apartment',
                'description'=>'Fully furnished with amenities and services'],
        ]);
    }
}
