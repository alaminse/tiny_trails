<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\PickUpType\app\Models\PickupType;

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
