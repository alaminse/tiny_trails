<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('👤 Creating users...');

        
        // Create Manager User
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'first_name' => 'Rahim',
                'last_name' => 'Manager',
                'phone' => '01700000001',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('manager');

        // Create sample drivers
        $drivers = [
            ['Ahmed', 'Karim', 'ahmed.karim@example.com'],
            ['Rahim', 'Uddin', 'rahim.uddin@example.com'],
            ['Faruk', 'Hossain', 'faruk.hossen@example.com'],
            ['Nasir', 'Ali', 'nasir.ali@example.com'],
            ['Kamal', 'Ahmed', 'kamal.ahmed@example.com'],
        ];

        foreach ($drivers as $index => $driverData) {
            $driver = User::firstOrCreate(
                ['email' => $driverData[2]],
                [
                    'first_name' => $driverData[0],
                    'last_name' => $driverData[1],
                    'phone' => '017' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $driver->assignRole('driver');
        }

        // Create sample parents
        $parents = [
            ['Salma', 'Khatun', 'salma.khatun@example.com'],
            ['Rokeya', 'Begum', 'rokeya.begum@example.com'],
            ['Tania', 'Rahman', 'tania.rahman@example.com'],
            ['Sumaiya', 'Akter', 'sumaiya.akter@example.com'],
        ];

        foreach ($parents as $index => $parentData) {
            $parent = User::firstOrCreate(
                ['email' => $parentData[2]],
                [
                    'first_name' => $parentData[0],
                    'last_name' => $parentData[1],
                    'phone' => '018' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $parent->assignRole('parent');
        }

        // Create additional random users
        User::factory()->driver()->count(15)->create();
        User::factory()->parent()->count(10)->create();

        $totalUsers = User::count();
        $this->command->info("✅ Created {$totalUsers} users successfully");
    }
}
