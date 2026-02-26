<?php
// ══════════════════════════════════════════════════════════════════
// MIGRATION 1: Drop unused tables
// File: database/migrations/2026_02_24_000001_drop_unused_tables.php
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ❌ Commission সরানো হয়েছে — এগুলো দরকার নেই
        Schema::dropIfExists('platform_revenues');
        Schema::dropIfExists('driver_payments');
        // payroll_records রাখুন — future এ কাজে লাগবে
    }

    public function down(): void
    {
        // rollback এ recreate করতে চাইলে এখানে লিখুন
    }
};
