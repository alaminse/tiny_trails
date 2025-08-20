<?php

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
        // Driver Commissions Table
        Schema::create('driver_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ride_assignment_id')->nullable()->constrained('ride_assignments')->onDelete('set null');
            
            // Commission Details
            $table->decimal('base_fare', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0); // Percentage (e.g., 15.50 for 15.5%)
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('total_earning', 10, 2)->default(0);
            
            // Payment Information
            $table->enum('commission_type', ['per_ride', 'daily_bonus', 'weekly_bonus', 'monthly_bonus', 'referral_bonus', 'penalty'])->default('per_ride');
            $table->enum('payment_status', ['pending', 'processing', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->date('earning_date');
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            
            // Bonus/Penalty Details
            $table->string('bonus_type')->nullable(); // 'completion_bonus', 'rating_bonus', etc.
            $table->string('penalty_type')->nullable(); // 'late_pickup', 'cancellation', etc.
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data like rating, completion percentage, etc.
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['driver_id', 'earning_date']);
            $table->index(['driver_id', 'payment_status']);
            $table->index(['earning_date', 'commission_type']);
            $table->index('payment_status');
        });

        // Driver Earnings Summary Table (for quick access to totals)
        Schema::create('driver_earnings_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            
            // Summary Period
            $table->date('summary_date');
            $table->enum('summary_type', ['daily', 'weekly', 'monthly'])->default('daily');
            
            // Earnings Summary
            $table->integer('total_rides')->default(0);
            $table->integer('completed_rides')->default(0);
            $table->integer('cancelled_rides')->default(0);
            $table->decimal('total_fare', 10, 2)->default(0);
            $table->decimal('total_commission', 10, 2)->default(0);
            $table->decimal('total_bonus', 10, 2)->default(0);
            $table->decimal('total_penalty', 10, 2)->default(0);
            $table->decimal('net_earnings', 10, 2)->default(0);
            
            // Performance Metrics
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_distance_km')->default(0);
            $table->integer('total_duration_minutes')->default(0);
            
            $table->timestamps();
            
            // Unique constraint to prevent duplicates
            $table->unique(['driver_id', 'summary_date', 'summary_type'], 'driver_summary_unique');
            $table->index(['driver_id', 'summary_type']);
            $table->index('summary_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_commissions');
        Schema::dropIfExists('driver_earnings_summary');
    }
};
