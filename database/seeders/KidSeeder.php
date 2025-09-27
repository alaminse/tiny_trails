<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\UserRolePermission\app\Models\Kid;

class KidSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('👶 Creating kids data...');

        $parents = User::role('parent')->get();

        if ($parents->isEmpty()) {
            $this->command->warn('No parent users found. Skipping kids seeder.');
            return;
        }

        foreach ($parents as $parent) {
            // Each parent has 1-3 kids
            Kid::factory()
                ->count(rand(1, 3))
                ->create(['user_id' => $parent->id]);
        }

        $totalKids = Kid::count();
        $this->command->info("✅ Created {$totalKids} kids successfully");
    }
}
