<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => '4-Seater Sedan',  'max_capacity' => 3,  'description' => 'Standard sedan'],
            ['name' => '7-Seater Van',    'max_capacity' => 6,  'description' => 'Large van'],
            ['name' => '12-Seater Minibus','max_capacity' => 11, 'description' => 'Minibus'],
        ];

        foreach ($types as $type) {
            VehicleType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
