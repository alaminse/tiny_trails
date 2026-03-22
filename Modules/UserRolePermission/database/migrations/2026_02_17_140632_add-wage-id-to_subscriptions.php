<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('wage_id')
                  ->nullable()
                  ->after('kid_id')
                  ->constrained('kid_wages')
                  ->nullOnDelete(); // ON DELETE SET NULL
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['wage_id']);
            $table->dropColumn('wage_id');
        });
    }
};
