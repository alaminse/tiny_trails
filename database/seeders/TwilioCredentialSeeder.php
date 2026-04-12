<?php
// database/seeders/TwilioCredentialSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TwilioCredential;

class TwilioCredentialSeeder extends Seeder
{
    public function run(): void
    {
        TwilioCredential::truncate();

        // Demo — active by default
        TwilioCredential::create([
            'label'                 => 'Demo Account',
            'account_sid'           => 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'auth_token'            => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'from_number'           => '+15005550006',
            'messaging_service_sid' => null,
            'mode'                  => 'demo',
            'is_active'             => true,
        ]);

        // Production — inactive until you click Activate
        TwilioCredential::create([
            'label'                 => 'Production Account',
            'account_sid'           => 'ACyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
            'auth_token'            => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
            'from_number'           => '+1987654321',
            'messaging_service_sid' => null,
            'mode'                  => 'production',
            'is_active'             => false,
        ]);

        $this->command->info('✅ Twilio credentials seeded. Demo is active.');
    }
}
