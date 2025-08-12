<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The main plans table with billing and feature attributes.
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price'); // Store as cents to avoid floating-point issues
            $table->string('currency', 3)->default('USD');
            $table->string('interval'); // e.g., 'month', 'year'
            $table->unsignedSmallInteger('interval_count')->default(1);
            $table->string('stripe_plan')->nullable(); // ID from the payment gateway
            $table->json('features')->nullable(); // A JSON column for a list of features
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
             $table->softDeletes();
        });

        // The subscriptions table links users to plans and stores all billing info.
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Foreign key to link to the users table
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Foreign key to link to the plans table
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name'); // Descriptive name for the subscription, e.g., 'default'
            $table->string('stripe_id')->unique()->nullable(); // ID from the payment gateway
            $table->string('stripe_status'); // The status as reported by Stripe
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // The end of the current billing period
            $table->timestamp('canceled_at')->nullable(); // When the user canceled the subscription
            $table->text('cancellation_reason')->nullable(); // Reason for cancellation

            // Card details (optional, but useful for user-facing info)
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_expiration')->nullable();

            $table->timestamps();
             $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
