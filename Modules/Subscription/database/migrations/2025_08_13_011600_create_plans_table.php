<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\PickUpType\App\Models\PickupType;

return new class extends Migration
{
    public function up(): void
    {
        // The main plans table with billing and feature attributes.
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_type_id')->constrained((new PickupType())->getTable());
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0); // Changed to decimal for better precision
            $table->decimal('sell_price', 10, 2)->default(0); // Changed to decimal for better precision
            $table->string('currency', 3)->default('AUD');
            $table->string('interval'); // e.g., 'month', 'year', 'week', 'day'
            $table->unsignedSmallInteger('interval_count')->default(1);
            $table->json('features')->nullable(); // A JSON column for a list of features
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status', 'sort_order']);
            $table->index('pickup_type_id');
        });

        // The subscriptions table links users to plans and stores all billing info.
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Foreign key to link to the users table
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Foreign key to link to the plans table
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();

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

        Schema::create('payway_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();

            // PayWay specific fields
            $table->string('payway_transaction_id')->unique()->index();
            $table->string('payway_customer_id')->index();

            // Transaction details
            $table->string('transaction_type'); // payment, refund, verification, preAuth, capture
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('aud');
            $table->string('status')->index(); // approved, declined, pending, voided, suspended

            // Response details
            $table->string('response_code')->nullable();
            $table->text('response_text')->nullable();
            $table->json('gateway_response')->nullable(); // Full PayWay response

            // Additional fields
            $table->string('order_number')->nullable()->index();
            $table->string('receipt_number')->nullable();
            $table->decimal('principal_amount', 10, 2)->nullable(); // Amount before surcharge
            $table->decimal('surcharge_amount', 10, 2)->nullable(); // Surcharge amount

            // Processing details
            $table->timestamp('processed_at')->nullable()->index();
            $table->date('settlement_date')->nullable()->index();

            // Timestamps
            $table->timestamps();

            // Additional indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['subscription_id', 'transaction_type']);
            $table->index(['status', 'processed_at']);
            $table->index(['payway_customer_id', 'transaction_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payway_transactions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
