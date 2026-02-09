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
            // Add new columns
            $table->string('middle_name')->nullable()->after('first_name');
            $table->json('emergency_contacts')->nullable()->after('school_address');
            $table->string('pickup_location')->nullable()->after('emergency_contacts');
            $table->string('dropoff_location')->nullable()->after('pickup_location');
            $table->decimal('distance_between_locations', 8, 2)->nullable()->after('dropoff_location');
            $table->string('hair_color')->nullable()->after('distance_between_locations');
            $table->string('eye_color')->nullable()->after('hair_color');
            $table->string('birthmarks')->nullable()->after('eye_color');

            // Drop the old emergency_contact column
            $table->dropColumn('emergency_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kids', function (Blueprint $table) {
            // Add back the old emergency_contact column
            $table->string('emergency_contact')->nullable()->after('school_address');

            // Drop the new columns
            $table->dropColumn([
                'middle_name',
                'emergency_contacts',
                'pickup_location',
                'dropoff_location',
                'distance_between_locations',
                'hair_color',
                'eye_color',
                'birthmarks'
            ]);
        });
    }
};
