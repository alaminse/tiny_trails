<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ])->assignRole('super-admin'); // <-- এখানে 'super-admin'
    }
}
