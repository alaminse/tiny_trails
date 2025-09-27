<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\PickUpType\app\Models\PickupType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payway_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();

            // PayWay specific fields
            $table->string('payway_transaction_id')->unique()->index();
            $table->string('payway_customer_id')->nullable()->index(); // Made nullable since it can be null in your code

            // Transaction details
            $table->string('transaction_type'); // payment, refund, verification, preAuth, capture
            $table->decimal('amount', 12, 2)->default(0); // Increased precision for larger amounts
            $table->string('currency', 3)->default('aud');
            $table->string('status')->index(); // approved, declined, pending, voided, suspended

            // Response details
            $table->string('response_code')->nullable();
            $table->text('response_text')->nullable();
            $table->json('gateway_response')->nullable(); // Full PayWay response

            // Additional transaction fields
            $table->string('order_number')->nullable()->index();
            $table->string('receipt_number')->nullable()->index(); // Added index for receipt lookups
            $table->decimal('principal_amount', 12, 2)->nullable(); // Amount before surcharge (increased precision)
            $table->decimal('surcharge_amount', 12, 2)->nullable(); // Surcharge amount (increased precision)

            // Processing details
            $table->timestamp('processed_at')->nullable()->index(); // When transaction was processed
            $table->date('settlement_date')->nullable()->index(); // Bank settlement date

            // Audit and tracking
            $table->timestamps();

            // Performance indexes
            $table->index(['user_id', 'status']);
            $table->index(['subscription_id', 'transaction_type']);
            $table->index(['status', 'processed_at']);
            $table->index(['payway_customer_id', 'transaction_type']);
            $table->index(['payway_transaction_id', 'status']); // Added for transaction status lookups
            $table->index(['order_number', 'status']); // Added for order tracking
            $table->index(['created_at', 'status']); // Added for time-based queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payway_transactions');
    }
};
