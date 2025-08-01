<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@b2b.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Customer One',
            'email' => 'customer1@b2b.com',
            'password' => Hash::make('123456'),
            'role' => 'customer',
        ]);

        User::factory()->create([
            'name' => 'Customer Two',
            'email' => 'customer2@b2b.com',
            'password' => Hash::make('123456'),
            'role' => 'customer',
        ]);
    }
}
