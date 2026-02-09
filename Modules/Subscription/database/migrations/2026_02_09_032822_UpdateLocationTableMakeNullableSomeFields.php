<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // These fields were NOT NULL in the original schema, so we make them nullable.
            $table->string('street1')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('postal_code')->nullable()->change();

            // street2 was already nullable, but including it here is harmless.
            // $table->string('street2')->nullable()->change();

            // country_code has a default, so it's less critical, but making it nullable is good practice.
            // $table->string('country_code', 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // WARNING: This rollback will FAIL if you have saved any records
            // with NULL values in these columns after running the 'up' migration.
            $table->string('street1')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('postal_code')->nullable(false)->change();
        });
    }
};
