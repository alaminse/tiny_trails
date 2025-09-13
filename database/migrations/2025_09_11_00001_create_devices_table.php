<?php

// Migration: create_devices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kid_id')->constrained('kids')->onDelete('cascade');
            $table->string('device_name');
            $table->string('imei', 15)->unique();
            $table->enum('device_type', ['watch', 'phone', 'tracker']);
            $table->string('phone_number', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->integer('battery_level')->nullable();
            $table->integer('signal_strength')->nullable();
            $table->string('tracksolid_device_id')->nullable();
            $table->timestamp('last_update_time')->nullable();
            $table->timestamps();

            $table->index(['kid_id', 'is_active']);
            $table->index('imei');
            $table->index('device_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
