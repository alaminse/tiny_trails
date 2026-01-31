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

            // Foreign Keys
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('plan_id')
                ->nullable()
                ->constrained('plans')
                ->onDelete('set null');

            $table->foreignId('kid_id')
                ->nullable()
                ->constrained('kids')
                ->onDelete('set null');

            $table->foreignId('pickup_location_id')
                ->nullable()
                ->constrained('locations')
                ->onDelete('set null');

            $table->foreignId('dropoff_location_id')
                ->nullable()
                ->constrained('locations')
                ->onDelete('set null');

            // Subscription Details
            $table->string('name'); // e.g., 'default'

            // PayWay specific fields
            $table->string('payway_customer_id')->nullable()->index();
            $table->string('payway_subscription_id')->nullable()->unique();
            $table->string('payway_status');

            // Trial & Subscription Period
            $table->unsignedInteger('trial_days')->default(0);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('assign_ride', ['assigned', 'unassigned'])->default('unassigned');

            // Card details
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_expiration')->nullable(); // MM/YY

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index(['payway_status', 'status']);
            $table->index('trial_ends_at');
            $table->index('ends_at');
            $table->index('kid_id');
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
