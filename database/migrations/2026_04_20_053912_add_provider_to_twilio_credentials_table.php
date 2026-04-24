<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('twilio_credentials', function (Blueprint $table) {
            $table->enum('provider', ['twilio', 'clicksend'])->default('twilio')->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('twilio_credentials', function (Blueprint $table) {
            $table->dropColumn('provider');
        });
    }
};
