<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\UserRolePermission\app\Models\Kid;

return new class extends Migration
{
    public function up(): void
    {
        // The subscriptions table links users to plans and stores all billing info.
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Foreign key to link to the users table
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Foreign key to link to the plans table
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kid_id')->nullable()->constrained((new Kid())->getTable());
            $table->foreignId('pickup_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('dropoff_location_id')->nullable()->constrained('locations')->onDelete('set null');

            $table->string('name'); // Descriptive name for the subscription, e.g., 'default'

            // PayWay specific fields (updated from Stripe)
            $table->string('payway_customer_id')->nullable()->index(); // PayWay customer ID
            $table->string('payway_subscription_id')->nullable()->unique(); // PayWay recurring billing ID
            $table->string('payway_status'); // The status as reported by PayWay

            $table->string('trial_days', 0);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // The end of the current billing period
            $table->timestamp('canceled_at')->nullable(); // When the user canceled the subscription
            $table->text('cancellation_reason')->nullable(); // Reason for cancellation
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('assign_ride', ['assigned', 'unassigned'])->default('unassigned');

            // Card details (optional, but useful for user-facing info)
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_expiration')->nullable(); // Format: MM/YY

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index(['payway_status', 'status']);
            $table->index('trial_ends_at');
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
