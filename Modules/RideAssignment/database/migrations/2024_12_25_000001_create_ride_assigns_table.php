<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Subscription\app\Models\Subscription;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ride_assigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained((new Subscription())->getTable());
            $table->decimal('fare', 10, 2);
            $table->decimal('driver_commission', 10, 2);
            $table->decimal('platform_commission', 10, 2);
            $table->enum('service_type', ['single_day', 'multiple_day']);
            $table->integer('total_days')->nullable();
            $table->text('selected_dates')->nullable();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ride_assigns');
    }
};
