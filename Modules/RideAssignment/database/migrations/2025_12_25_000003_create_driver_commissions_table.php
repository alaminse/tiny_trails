<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_commissions', function (Blueprint $table) {
            $table->id();

            // Foreign Keys - hardcoded table names
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');

            $table->foreignId('ride_id')
                ->constrained('rides')
                ->onDelete('cascade');

            // Commission Details
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('total_fare', 10, 2);

            // Payment Status
            $table->enum('payment_status', ['unpaid', 'paid', 'pending'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('driver_id');
            $table->index('ride_id');
            $table->index('payment_status');
            $table->index('paid_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_commissions');
    }
};
