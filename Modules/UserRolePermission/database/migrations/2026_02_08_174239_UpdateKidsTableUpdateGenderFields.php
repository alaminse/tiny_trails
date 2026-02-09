<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('kids', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('gender');

            // Add the new enum column
            $table->enum('gender', ['Boy', 'Girl', 'Prefer not to say'])->nullable()->after('dob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kids', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('gender');

            // Add back the old enum column
            $table->enum('gender', ['Boy', 'Girl', 'Prefer not to say'])->nullable()->after('dob');
        });
    }
};
