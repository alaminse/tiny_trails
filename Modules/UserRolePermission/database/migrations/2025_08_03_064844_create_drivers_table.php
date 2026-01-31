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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            // Foreign Key
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Driving License Details
            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_expiry')->nullable();
            $table->string('driving_license_image')->nullable();

            // Car Details
            $table->string('car_model')->nullable();
            $table->string('car_make')->nullable();
            $table->year('car_year')->nullable();
            $table->string('car_color')->nullable();
            $table->string('car_plate_number')->nullable();
            $table->string('car_image')->nullable();

            // Face Recognition
            $table->text('face_embedding')->nullable();
            $table->string('face_image')->nullable();

            // Verification & Status
            $table->boolean('is_verified')->default(false);
            $table->string('device_token')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('status');
            $table->index('is_verified');
            $table->index('driving_license_number');
            $table->index('car_plate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
