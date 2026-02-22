<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_wages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                  ->constrained('drivers')
                  ->cascadeOnDelete();
            $table->enum('rate_type', ['daily', 'hourly'])->default('daily');
            $table->decimal('rate_amount', 10, 2);       // e.g. 150.00 per day
            $table->date('effective_from');               // wage valid from
            $table->date('effective_to')->nullable();     // null = ongoing
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')              // super admin only
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_wages');
    }
};
