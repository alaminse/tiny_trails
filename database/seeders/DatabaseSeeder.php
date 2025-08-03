<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Modules\LocationManagement\App\Models\City;
use Modules\LocationManagement\App\Models\Country;
use Modules\LocationManagement\App\Models\State;
use Illuminate\Support\Str;
use Modules\PickUpType\App\Models\PickupType;
use Modules\UserRolePermission\App\Models\Driver;
use Modules\UserRolePermission\App\Models\Kid;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Seed countries
        $country = Country::create([
            'name' => 'Australia',
            'status' => 'active',
        ]);

        // Seed states
        $states = collect(['New South Wales', 'Victoria', 'Queensland', 'Tasmania'])->map(function ($name) use ($country) {
            return State::create([
                'country_id' => $country->id,
                'name' => $name,
                'status' => 'active',
            ]);
        });

        // Seed cities
        $cities = $states->map(function ($state) use ($faker) {
            return City::create([
                'state_id' => $state->id,
                'name' => $faker->city,
                'status' => 'active',
            ]);
        });

        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $driverRole = Role::firstOrCreate(['name' => 'driver']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);

        // 1️⃣ Admin user
        $admin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'dob' => '1990-01-01',
            'address' => 'Admin Address',
            'gender' => 'male',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'status' => 'active',
        ]);
        $admin->assignRole($adminRole);

        // 2️⃣ Random users
        $users = collect(range(1, 20))->map(function () use ($faker) {
            return User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'dob' => $faker->date(),
                'address' => $faker->address,
                'gender' => $faker->randomElement(['male', 'female']),
                'country_id' => 1,
                'state_id' => rand(1, 10),
                'city_id' => rand(1, 10),
                'photo' => null,
                'status' => 'active',
            ]);
        });

        $assignedUserIds = [];

        // 3️⃣ Assign driver role and create drivers (first 10 users)
        $driverUsers = $users->take(10);
        foreach ($driverUsers as $user) {
            $user->assignRole($driverRole);
            Driver::create([
                'user_id' => $user->id,
                'driving_license_number' => strtoupper(Str::random(10)),
                'driving_license_expiry' => $faker->dateTimeBetween('+1 year', '+5 years'),
                'driving_license_image' => null, // or you can assign a fake image path
                'car_model' => $faker->randomElement(['Toyota', 'Honda', 'Ford']),
                'car_make' => $faker->word,
                'car_year' => $faker->year,
                'car_color' => $faker->safeColorName,
                'car_plate_number' => strtoupper(Str::random(6)),
                'car_image' => null, // or use $faker->imageUrl()
                'face_embedding' => null, // or some encrypted mock string
                'device_token' => null,
                'is_verified' => false,
                'status' => 'active',
            ]);
            $assignedUserIds[] = $user->id;
        }

        // 4️⃣ Assign parent role and create kids (next 10 users)
        $kidUsers = $users->filter(fn($user) => !in_array($user->id, $assignedUserIds))->take(10);

        foreach ($kidUsers as $user) {
            $user->assignRole($parentRole);

            Kid::create([
                'user_id' => $user->id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'dob' => $faker->date(),
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'height_cm' => $faker->randomFloat(2, 80, 170), // e.g., 120.45 cm
                'weight_kg' => $faker->randomFloat(2, 15, 70),  // e.g., 35.67 kg
                'photo' => null, // or $faker->imageUrl() if you want a fake image
                'school_name' => $faker->company,
                'school_address' => $faker->address,
                'emergency_contact' => $faker->phoneNumber,
            ]);

            $assignedUserIds[] = $user->id;
        }

        // Seed pickup types
        foreach (range(1, 5) as $i) {
            PickupType::create([
                'name' => $faker->randomElement(['Standard', 'Express', 'Scheduled']),
                'amount' => $faker->randomFloat(2, 10, 50),
                'min_notice_minutes' => $faker->numberBetween(15, 60),
                'requires_instant_notification' => $faker->boolean,
                'status' => 'active',
            ]);
        }
    }
}
