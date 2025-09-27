<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->decimal('latitude', 10, 8);  // latitude store করার জন্য
            $table->decimal('longitude', 11, 8); // longitude store করার জন্য
            $table->string('street1');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country_code', 2)->default('AU');
            $table->enum('type', ['pickup', 'dropoff']); // pickup না dropoff বোঝানোর জন্য
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
