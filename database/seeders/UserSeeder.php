<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'phone' => '9813733877',
            'password' => Hash::make('123456789'),
        ]);

        $user->roles()->attach(2);
    }
}
