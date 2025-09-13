<?php

// Migration: create_geofences_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('geofences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignId('kid_id')->constrained('kids')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['circle', 'polygon'])->default('circle');
            $table->decimal('center_latitude', 10, 8);
            $table->decimal('center_longitude', 11, 8);
            $table->integer('radius')->nullable(); // in meters, for circle type
            $table->json('polygon_points')->nullable(); // for polygon type
            $table->boolean('alert_on_enter')->default(true);
            $table->boolean('alert_on_exit')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['device_id', 'is_active']);
            $table->index(['kid_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('geofences');
    }
};
