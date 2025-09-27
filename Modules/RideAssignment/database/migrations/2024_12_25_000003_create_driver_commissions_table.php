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
            $table->foreignId('driver_id')->constrained((new Driver())->getTable());
            $table->foreignId('ride_id')->constrained((new Ride())->getTable());
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('total_fare', 10, 2);
            $table->enum('payment_status', ['unpaid', 'paid', 'pending'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_commissions');
    }
};
