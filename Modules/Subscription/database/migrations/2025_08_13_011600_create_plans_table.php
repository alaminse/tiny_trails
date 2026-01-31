<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\PickUpType\app\Models\PickupType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            // Foreign Key
            $table->foreignId('pickup_type_id')
                ->constrained('pickup_types')
                ->onDelete('restrict');

            // Plan Details
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sell_price', 10, 2)->default(0);
            $table->string('currency', 3)->default('AUD');

            // Billing Interval
            $table->string('interval');
            $table->unsignedSmallInteger('interval_count')->default(1);

            // Features & Status
            $table->json('features')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'sort_order']);
            $table->index('pickup_type_id');
            $table->index('interval');
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
