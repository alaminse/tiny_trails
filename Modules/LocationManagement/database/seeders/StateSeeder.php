<?php

namespace Modules\LocationManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // টেবিলটি খালি করে নেওয়া হচ্ছে
        DB::table('states')->delete();

        // আমরা ধরে নিচ্ছি CountrySeeder আগে চলেছে এবং অস্ট্রেলিয়ার আইডি ১
        $australiaId = DB::table('countries')->where('name', 'Australia')->first()->id;

        if (!$australiaId) {
            $this->command->error('Australia not found in countries table. Please run CountrySeeder first.');
            return;
        }

        $states = [
            ['name' => 'New South Wales', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'Victoria', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'Queensland', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'South Australia', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'Western Australia', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'Tasmania', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'Australian Capital Territory', 'country_id' => $australiaId, 'is_active' => 1],
            ['name' => 'Northern Territory', 'country_id' => $australiaId, 'is_active' => 1],
        ];

        foreach ($states as $state) {
            DB::table('states')->insert(array_merge($state, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
