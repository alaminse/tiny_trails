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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('subscription_id')->nullable();

            $table->string('payway_transaction_id')->unique();
            $table->string('payway_customer_id')->nullable();
            $table->string('transaction_type');
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('currency', 3)->default('aud');
            $table->string('status');

            $table->string('response_code')->nullable();
            $table->text('response_text')->nullable();
            $table->longText('gateway_response')->nullable();

            $table->string('order_number')->nullable();
            $table->string('receipt_number')->nullable();

            $table->decimal('principal_amount', 12, 2)->nullable();
            $table->decimal('surcharge_amount', 12, 2)->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->date('settlement_date')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status'], 'payway_transactions_user_id_status_index');
            $table->index(['subscription_id', 'transaction_type'], 'payway_transactions_subscription_id_transaction_type_index');
            $table->index(['status', 'processed_at'], 'payway_transactions_status_processed_at_index');
            $table->index(['payway_customer_id', 'transaction_type'], 'payway_transactions_payway_customer_id_transaction_type_index');
            $table->index(['payway_transaction_id', 'status'], 'payway_transactions_payway_transaction_id_status_index');
            $table->index(['order_number', 'status'], 'payway_transactions_order_number_status_index');
            $table->index(['created_at', 'status'], 'payway_transactions_created_at_status_index');
            $table->index('payway_customer_id', 'payway_transactions_payway_customer_id_index');
            $table->index('status', 'payway_transactions_status_index');
            $table->index('order_number', 'payway_transactions_order_number_index');
            $table->index('receipt_number', 'payway_transactions_receipt_number_index');
            $table->index('processed_at', 'payway_transactions_processed_at_index');
            $table->index('settlement_date', 'payway_transactions_settlement_date_index');
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
