<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('vehicle_type_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('vehicle_types')
                  ->nullOnDelete();

            $table->enum('availability_status', [
                'available',
                'on_trip',
                'ready_next_batch',
                'delayed',
                'offline'
            ])->default('offline')->after('vehicle_type_id');

            $table->timestamp('face_verified_at')->nullable()->after('availability_status');
            $table->timestamp('face_verified_until')->nullable()->after('face_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['vehicle_type_id']);
            $table->dropColumn([
                'vehicle_type_id',
                'availability_status',
                'face_verified_at',
                'face_verified_until',
            ]);
        });
    }
};
