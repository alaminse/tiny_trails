<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ride_assignments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kid_id')->nullable()->constrained('kids')->onDelete('set null');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->onDelete('set null');
            
            // Basic ride information
            $table->string('ride_title');
            $table->text('pickup_location');
            $table->text('dropoff_location');

            // GPS coordinates
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->decimal('dropoff_latitude', 10, 8)->nullable();
            $table->decimal('dropoff_longitude', 11, 8)->nullable();
            
            // Schedule information
            $table->date('ride_date');
            $table->time('pickup_time');
            $table->time('estimated_dropoff_time')->nullable();
            
            // Recurring ride settings
            $table->json('recurring_days')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->date('recurring_end_date')->nullable();
            
            // Trip details
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            
            // Financial information
            $table->decimal('ride_fare', 10, 2);
            $table->decimal('driver_commission', 10, 2)->nullable();
            $table->decimal('platform_fee', 10, 2)->nullable();
            
            // Status and type
            $table->enum('status', [
                'assigned', 
                'accepted', 
                'in_progress', 
                'completed', 
                'cancelled', 
                'no_show'
            ])->default('assigned');
            $table->enum('ride_type', [
                'one_time', 
                'daily', 
                'weekly', 
                'custom'
            ])->default('one_time');
            
            // Additional information
            $table->text('special_instructions')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Status timestamps
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Standard timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['driver_id', 'ride_date']);
            $table->index(['parent_id', 'ride_date']);
            $table->index(['status', 'ride_date']);
            $table->index(['ride_date', 'pickup_time']);
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_assignments');
    }
};
