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
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // --- New Licence Details ---
            $table->string('middle_name')->nullable()->after('user_id');
            $table->string('licence_card_number')->nullable()->after('driving_license_number');
            $table->string('licence_type')->nullable()->after('licence_card_number');

            // --- New Licence Address ---
            $table->string('licence_address_line_1')->nullable()->after('licence_type');
            $table->string('licence_address_line_2')->nullable()->after('licence_address_line_1');
            $table->string('licence_city')->nullable()->after('licence_address_line_2');
            $table->string('licence_state')->nullable()->after('licence_city');
            $table->string('licence_postal_code')->nullable()->after('licence_state');
            $table->string('licence_country')->nullable()->after('licence_postal_code');

            // --- New Compliance Documents ---
            $table->string('wwc_card_number')->nullable()->after('licence_country');
            $table->date('wwc_expiry_date')->nullable()->after('wwc_card_number');
            $table->string('wwc_card_image')->nullable()->after('wwc_expiry_date');
            $table->string('police_clearance_ref')->nullable()->after('wwc_card_image');
            $table->string('police_clearance_image')->nullable()->after('police_clearance_ref');
            $table->json('other_qualifications')->nullable()->after('police_clearance_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // --- Drop New Licence Details ---
            $table->dropColumn('licence_card_number');
            $table->dropColumn('licence_type');

            // --- Drop New Licence Address ---
            $table->dropColumn('licence_address_line_1');
            $table->dropColumn('licence_address_line_2');
            $table->dropColumn('licence_city');
            $table->dropColumn('licence_state');
            $table->dropColumn('licence_postal_code');
            $table->dropColumn('licence_country');

            // --- Drop New Compliance Documents ---
            $table->dropColumn('wwc_card_number');
            $table->dropColumn('wwc_expiry_date');
            $table->dropColumn('wwc_card_image');
            $table->dropColumn('police_clearance_ref');
            $table->dropColumn('police_clearance_image');
            $table->dropColumn('other_qualifications');
        });
    }
};
