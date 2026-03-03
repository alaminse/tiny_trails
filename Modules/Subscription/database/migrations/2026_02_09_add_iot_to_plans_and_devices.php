<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------
        // 1️⃣ Add IoT fields to plans
        // -----------------------------
        Schema::table('plans', function (Blueprint $table) {
            $table->string('plan_tier')->nullable()->after('features');
            $table->enum('iot_level', ['none', 'basic', 'advanced'])->default('none')->after('plan_tier');
            $table->boolean('includes_hardware')->default(false)->after('iot_level');
            $table->decimal('hardware_price', 10, 2)->nullable()->after('includes_hardware');
        });

        // -----------------------------
        // 2️⃣ Create IoT devices table
        // -----------------------------
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('image')->nullable();
            $table->enum('iot_level', ['basic','advanced']);
            $table->boolean('supports_sos')->default(false);
            $table->boolean('supports_geofence')->default(false);
            $table->string('battery_type')->nullable();
            $table->enum('status', ['active','deprecated'])->default('active');
            $table->timestamps();
        });

        // -----------------------------
        // 3️⃣ Create pivot table plan_iot_devices
        // -----------------------------
        Schema::create('plan_iot_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->foreignId('iot_device_id')->constrained('iot_devices')->cascadeOnDelete();
            $table->boolean('is_included')->default(true);
            $table->decimal('extra_price', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['plan_id','iot_device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_iot_devices');
        Schema::dropIfExists('iot_devices');

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['plan_tier','iot_level','includes_hardware','hardware_price']);
        });
    }
};
