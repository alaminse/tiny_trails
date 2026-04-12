<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twilio_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('account_sid');
            $table->string('auth_token');
            $table->string('from_number');
            $table->string('messaging_service_sid')->nullable();
            $table->enum('mode', ['demo', 'production'])->default('demo');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twilio_credentials');
    }
};
