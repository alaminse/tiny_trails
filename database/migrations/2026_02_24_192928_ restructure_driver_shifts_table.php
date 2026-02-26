<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK on driver_id
        $fks = DB::select("
            SELECT kcu.CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE kcu
            JOIN information_schema.TABLE_CONSTRAINTS tc
              ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
             AND tc.TABLE_SCHEMA    = kcu.TABLE_SCHEMA
             AND tc.TABLE_NAME      = kcu.TABLE_NAME
            WHERE kcu.TABLE_SCHEMA   = DATABASE()
              AND kcu.TABLE_NAME     = 'driver_shifts'
              AND kcu.COLUMN_NAME    = 'driver_id'
              AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `driver_shifts` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // 2. Drop all indexes that include driver_id
        $indexes = DB::select("
            SELECT DISTINCT s.INDEX_NAME
            FROM information_schema.STATISTICS s
            WHERE s.TABLE_SCHEMA = DATABASE()
              AND s.TABLE_NAME   = 'driver_shifts'
              AND s.INDEX_NAME  != 'PRIMARY'
              AND s.INDEX_NAME IN (
                  SELECT INDEX_NAME FROM information_schema.STATISTICS
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME   = 'driver_shifts'
                    AND COLUMN_NAME  = 'driver_id'
              )
        ");
        foreach ($indexes as $idx) {
            DB::statement("ALTER TABLE `driver_shifts` DROP INDEX `{$idx->INDEX_NAME}`");
        }

        // 3. Make driver_id nullable (keep column, just nullable)
        DB::statement("ALTER TABLE `driver_shifts` MODIFY COLUMN `driver_id` BIGINT UNSIGNED NULL");

        // 4. Add new unique: one shift_number per date
        $exists = DB::select("
            SELECT INDEX_NAME FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'driver_shifts'
              AND INDEX_NAME   = 'uniq_date_shift'
            LIMIT 1
        ");
        if (empty($exists)) {
            DB::statement("ALTER TABLE `driver_shifts` ADD UNIQUE KEY `uniq_date_shift` (`date`, `shift_number`)");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `driver_shifts` DROP INDEX IF EXISTS `uniq_date_shift`");
        DB::statement("ALTER TABLE `driver_shifts` MODIFY COLUMN `driver_id` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `driver_shifts` ADD CONSTRAINT `driver_shifts_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`)");
        DB::statement("ALTER TABLE `driver_shifts` ADD UNIQUE KEY `uniq_driver_date_shift` (`driver_id`, `date`, `shift_number`)");
    }
};
