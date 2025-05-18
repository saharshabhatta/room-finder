<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('features')->insert([
            ['name' => 'Furnished'],
            ['name' => 'Attached Bathroom'],
            ['name' => 'Wi-Fi Included'],
            ['name' => 'Air Conditioner'],
            ['name' => 'Power Backup'],
            ['name' => 'Kitchen Access'],
            ['name' => 'Laundry Facility'],
            ['name' => 'Parking Available'],
            ['name' => 'CCTV Surveillance'],
            ['name' => 'Cleaning Service'],
        ]);
    }
}
