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
            ['name' => 'New South Wales', 'country_id' => $australiaId],
            ['name' => 'Victoria', 'country_id' => $australiaId],
            ['name' => 'Queensland', 'country_id' => $australiaId],
            ['name' => 'South Australia', 'country_id' => $australiaId],
            ['name' => 'Western Australia', 'country_id' => $australiaId],
            ['name' => 'Tasmania', 'country_id' => $australiaId],
            ['name' => 'Australian Capital Territory', 'country_id' => $australiaId],
            ['name' => 'Northern Territory', 'country_id' => $australiaId],
        ];

        foreach ($states as $state) {
            DB::table('states')->insert(array_merge($state, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
