<?php

use App\Models\User;
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
        Schema::create('kids', function (Blueprint $table) {
            $table->id();

            // Foreign Key
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Personal Details
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // Physical Details
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('photo')->nullable();

            // School Details
            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();

            // Emergency & Device
            $table->string('emergency_contact')->nullable();
            $table->string('kit_imei')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('kit_imei');
            $table->index(['user_id', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kids');
    }
};
