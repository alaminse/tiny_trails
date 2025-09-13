<?php

// Migration: create_device_alerts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignId('kid_id')->constrained('kids')->onDelete('cascade');
            $table->enum('alert_type', [
                'low_battery',
                'device_offline',
                'geofence_exit',
                'geofence_enter',
                'sos',
                'speed_limit'
            ]);
            $table->string('title');
            $table->text('message');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('triggered_at');
            $table->timestamps();

            $table->index(['kid_id', 'is_read']);
            $table->index(['device_id', 'alert_type']);
            $table->index('triggered_at');
            $table->index('severity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_alerts');
    }
};
