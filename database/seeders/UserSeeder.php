<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@gmail.com',
                'role' => 'Super-Admin',
            ],
            [
                'first_name' => 'IT',
                'last_name' => 'Manager',
                'email' => 'it@gmail.com',
                'role' => 'BOH-IT',
            ],
            [
                'first_name' => 'Marketing',
                'last_name' => 'User',
                'email' => 'marketing@gmail.com',
                'role' => 'BOH-Marketing',
            ],
            [
                'first_name' => 'Sales',
                'last_name' => 'User',
                'email' => 'sales@gmail.com',
                'role' => 'BOH-Sales',
            ],
            [
                'first_name' => 'Support',
                'last_name' => 'User',
                'email' => 'support@gmail.com',
                'role' => 'BOH-Support',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // assign role
            $user->syncRoles([$data['role']]);
        }
    }
}
