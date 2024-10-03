<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role' => 'restaurant',
            'name' => 'admin restaurant',
            'email' => 'admin@restaurant.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'role' => 'housekeeping',
            'name' => 'admin housekeeping',
            'email' => 'admin@housekeeping.com',
            'password' => Hash::make('password'),
        ]);
    }
}
