<?php

namespace Modules\LocationManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // টেবিলটি খালি করে নেওয়া হচ্ছে
        DB::table('cities')->delete();

        // প্রথমে সব রাজ্যের তথ্য নিয়ে আসি
        $states = DB::table('states')->pluck('id', 'name');

        if ($states->isEmpty()) {
            $this->command->error('No states found. Please run StateSeeder first.');
            return;
        }

        $cities = [
            // New South Wales
            ['state_id' => $states['New South Wales'], 'name' => 'Sydney'],
            ['state_id' => $states['New South Wales'], 'name' => 'Newcastle'],
            ['state_id' => $states['New South Wales'], 'name' => 'Wollongong'],
            ['state_id' => $states['New South Wales'], 'name' => 'Central Coast'],

            // Victoria
            ['state_id' => $states['Victoria'], 'name' => 'Melbourne'],
            ['state_id' => $states['Victoria'], 'name' => 'Geelong'],
            ['state_id' => $states['Victoria'], 'name' => 'Ballarat'],
            ['state_id' => $states['Victoria'], 'name' => 'Bendigo'],

            // Queensland
            ['state_id' => $states['Queensland'], 'name' => 'Brisbane'],
            ['state_id' => $states['Queensland'], 'name' => 'Gold Coast'],
            ['state_id' => $states['Queensland'], 'name' => 'Sunshine Coast'],
            ['state_id' => $states['Queensland'], 'name' => 'Townsville'],

            // South Australia
            ['state_id' => $states['South Australia'], 'name' => 'Adelaide'],
            ['state_id' => $states['South Australia'], 'name' => 'Mount Gambier'],
            ['state_id' => $states['South Australia'], 'name' => 'Whyalla'],

            // Western Australia
            ['state_id' => $states['Western Australia'], 'name' => 'Perth'],
            ['state_id' => $states['Western Australia'], 'name' => 'Bunbury'],
            ['state_id' => $states['Western Australia'], 'name' => 'Geraldton'],

            // Tasmania
            ['state_id' => $states['Tasmania'], 'name' => 'Hobart'],
            ['state_id' => $states['Tasmania'], 'name' => 'Launceston'],
            ['state_id' => $states['Tasmania'], 'name' => 'Burnie'],

            // Australian Capital Territory
            ['state_id' => $states['Australian Capital Territory'], 'name' => 'Canberra'],

            // Northern Territory
            ['state_id' => $states['Northern Territory'], 'name' => 'Darwin'],
            ['state_id' => $states['Northern Territory'], 'name' => 'Alice Springs'],
            ['state_id' => $states['Northern Territory'], 'name' => 'Katherine'],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert(array_merge($city, [
                'status' => 'active', // আপনার enum অনুযায়ী স্ট্টাস
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
