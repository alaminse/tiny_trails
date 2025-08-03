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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_expiry')->nullable();
            $table->string('driving_license_image')->nullable();

            $table->string('car_model')->nullable();
            $table->string('car_make')->nullable(); // added for completeness
            $table->year('car_year')->nullable();   // added for data integrity
            $table->string('car_color')->nullable();
            $table->string('car_plate_number')->nullable();
            $table->string('car_image')->nullable();

            $table->text('face_embedding')->nullable(); // encrypted face vector
            $table->boolean('is_verified')->default(false);
            $table->string('device_token')->nullable(); // for MFA or notifications

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();
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
