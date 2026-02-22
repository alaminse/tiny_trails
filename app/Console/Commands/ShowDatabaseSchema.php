<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShowDatabaseSchema extends Command
{
    // php artisan db:schema
    protected $signature = 'db:schema';
    protected $description = 'Show all tables and their columns';

    public function handle()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";

        foreach ($tables as $table) {
            $tableName = $table->$key;
            $this->info("\n📋 Table: {$tableName}");
            $this->line(str_repeat('=', 60));

            $columns = Schema::getColumnListing($tableName);

            foreach ($columns as $column) {
                $columnType = Schema::getColumnType($tableName, $column);
                $this->line("  • {$column} ({$columnType})");
            }
        }

        return 0;
    }
}
