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
            ['name' => 'Furnished',
                'user_id'=>'1'],
            ['name' => 'Attached Bathroom',
                'user_id'=>'1'],
            ['name' => 'Wi-Fi Included',
                'user_id'=>'1'],
            ['name' => 'Air Conditioner',
                'user_id'=>'1'],
            ['name' => 'Power Backup',
                'user_id'=>'1'],
            ['name' => 'Kitchen Access',
                'user_id'=>'1'],
            ['name' => 'Laundry Facility',
                'user_id'=>'1'],
            ['name' => 'Parking Available',
                'user_id'=>'1'],
            ['name' => 'CCTV Surveillance',
                'user_id'=>'1'],
            ['name' => 'Cleaning Service',
                'user_id'=>'1'],
        ]);
    }
}
