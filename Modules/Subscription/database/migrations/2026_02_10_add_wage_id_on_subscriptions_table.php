<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('wage_id')->nullable()->after('kid_id');
            $table->foreign('wage_id')->references('id')->on('kid_wages')->onDelete('set null');
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
