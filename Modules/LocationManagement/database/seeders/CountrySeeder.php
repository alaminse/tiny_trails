<?php

namespace Modules\LocationManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // টেবিলটি খালি করে নেওয়া হচ্ছে, যাতে ডুপ্লিকেট ডেটা না হয়
        DB::table('countries')->delete();

        DB::table('countries')->insert([
            'name' => 'Australia',
            'status' => 'active', // আপনার enum অনুযায়ী স্ট্যাটাস দিন
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
