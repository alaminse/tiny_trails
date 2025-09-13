<?php

// Migration: create_device_locations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address')->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->decimal('speed', 8, 2)->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->index(['device_id', 'timestamp']);
            $table->index(['latitude', 'longitude']);
            $table->index('timestamp');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_locations');
    }
};
