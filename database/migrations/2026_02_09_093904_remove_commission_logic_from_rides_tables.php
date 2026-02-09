// database/migrations/YYYY_MM_DD_HHMMSS_remove_commission_logic_from_rides_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the driver_commissions table if it exists
        if (Schema::hasTable('driver_commissions')) {
            Schema::dropIfExists('driver_commissions');
        }

        // Remove commission fields from ride_assigns table
        if (Schema::hasTable('ride_assigns')) {
            Schema::table('ride_assigns', function (Blueprint $table) {
                if (Schema::hasColumn('ride_assigns', 'driver_commission')) {
                    $table->dropColumn('driver_commission');
                }
                if (Schema::hasColumn('ride_assigns', 'platform_commission')) {
                    $table->dropColumn('platform_commission');
                }
            });
        }

        // Remove commission field from rides table
        if (Schema::hasTable('rides')) {
            Schema::table('rides', function (Blueprint $table) {
                if (Schema::hasColumn('rides', 'commission')) {
                    $table->dropColumn('commission');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add commission columns back to ride_assigns table
        if (Schema::hasTable('ride_assigns')) {
            Schema::table('ride_assigns', function (Blueprint $table) {
                $table->decimal('driver_commission', 10, 2)->after('fare')->default(0);
                $table->decimal('platform_commission', 10, 2)->after('driver_commission')->default(0);
            });
        }

        // Add commission column back to rides table
        if (Schema::hasTable('rides')) {
            Schema::table('rides', function (Blueprint $table) {
                $table->decimal('commission', 10, 2)->after('ride_type')->default(0);
            });
        }
    }
};
