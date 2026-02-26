<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('rides')->whereNull('driver_id')->update(['driver_id' => 0]);

        Schema::table('rides', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable(false)->change();
        });
    }
};
