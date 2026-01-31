<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();

            // Foreign Keys - nullable করা যেতে পারে প্রয়োজন অনুযায়ী
            $table->foreignId('ride_assign_id')
                ->constrained('ride_assigns')
                ->onDelete('cascade');

            $table->foreignId('driver_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('parent_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('pickup_location_id')
                ->constrained('locations')
                ->onDelete('restrict');

            $table->foreignId('dropoff_location_id')
                ->constrained('locations')
                ->onDelete('restrict');

            // Ride Details
            $table->enum('ride_type', ['pickup', 'return_home'])->default('pickup');
            $table->decimal('commission', 10, 2)->default(0);
            $table->date('date')->nullable();
            $table->time('pickup')->nullable();
            $table->time('drop_off')->nullable();
            $table->dateTime('end_at')->nullable();

            // Verification Images
            $table->string('face_verification1')->nullable();
            $table->string('selfie')->nullable();
            $table->string('face_verification2')->nullable();
            $table->string('end_pic')->nullable();

            // Status
            $table->enum('status', [
                'assigned',
                'pending',
                'going_to_pickup',
                'arrived_at_pickup',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('assigned');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('ride_assign_id');
            $table->index('driver_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('date');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
