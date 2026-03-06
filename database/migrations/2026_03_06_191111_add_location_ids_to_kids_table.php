<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kids', function (Blueprint $table) {

            if (!Schema::hasColumn('kids', 'pickup_location_id')) {
                $table->unsignedBigInteger('pickup_location_id')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('kids', 'dropoff_location_id')) {
                $table->unsignedBigInteger('dropoff_location_id')->nullable()->after('pickup_location_id');
            }

        });
    }

    public function down()
    {
        Schema::table('kids', function (Blueprint $table) {
            $table->dropForeign(['pickup_location_id']);
            $table->dropForeign(['dropoff_location_id']);

            $table->dropColumn([
                'pickup_location_id',
                'dropoff_location_id'
            ]);
        });
    }
};
