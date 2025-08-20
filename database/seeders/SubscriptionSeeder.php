<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\Subscription\app\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('💳 Creating subscription data...');

        $parents = User::role('parent')->get();

        if ($parents->isEmpty()) {
            $this->command->warn('No parent users found. Skipping subscription seeder.');
            return;
        }

        // 70% of parents will have subscriptions
        $subscribedParents = $parents->random(intval($parents->count() * 0.7));

        foreach ($subscribedParents as $parent) {
            // Each parent has one subscription
            Subscription::factory()->create(['user_id' => $parent->id]);
        }

        // Create some canceled subscriptions
        Subscription::factory()
            ->canceled()
            ->count(5)
            ->create(['user_id' => $parents->random()->id]);

        $totalSubscriptions = Subscription::count();
        $this->command->info("✅ Created {$totalSubscriptions} subscriptions successfully");
    }
}