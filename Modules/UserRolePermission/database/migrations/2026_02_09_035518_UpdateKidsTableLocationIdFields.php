<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kids', function (Blueprint $table) {
            // Add the columns as unsigned integers (standard for foreign keys)
            $table->unsignedBigInteger('pickup_location_id')->nullable();
            $table->unsignedBigInteger('dropoff_location_id')->nullable();

            // Add the foreign key constraints
            $table->foreign('pickup_location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('set null'); // If a location is deleted, set the kid's location_id to null

            $table->foreign('dropoff_location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('set null'); // If a location is deleted, set the kid's location_id to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kids', function (Blueprint $table) {
            // Drop the foreign keys first
            $table->dropForeign(['pickup_location_id']);
            $table->dropForeign(['dropoff_location_id']);

            // Then drop the columns
            $table->dropColumn(['pickup_location_id', 'dropoff_location_id']);
        });
    }
};
