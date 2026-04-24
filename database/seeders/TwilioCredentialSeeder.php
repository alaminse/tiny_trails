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

        // Twilio Demo — active by default
        TwilioCredential::create([
            'label'                 => 'Twilio Demo',
            'provider'              => 'twilio',
            'account_sid'           => 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'auth_token'            => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'from_number'           => '+15005550006',
            'messaging_service_sid' => null,
            'mode'                  => 'demo',
            'is_active'             => true,
        ]);

        // Twilio Production
        TwilioCredential::create([
            'label'                 => 'Twilio Production',
            'provider'              => 'twilio',
            'account_sid'           => 'ACyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
            'auth_token'            => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy',
            'from_number'           => '+1987654321',
            'messaging_service_sid' => null,
            'mode'                  => 'production',
            'is_active'             => false,
        ]);

        // ClickSend Demo — uses official ClickSend test credentials
        TwilioCredential::create([
            'label'                 => 'ClickSend Demo',
            'provider'              => 'clicksend',
            'account_sid'           => 'nocredit',                            // ClickSend test username
            'auth_token'            => 'D83DED51-9E35-4D42-9BB9-0E34B7CA85AE', // ClickSend test api key
            'from_number'           => 'TinyTrail',                           // max 11 chars
            'messaging_service_sid' => null,
            'mode'                  => 'demo',
            'is_active'             => false,
        ]);

        // ClickSend Production
        TwilioCredential::create([
            'label'                 => 'ClickSend Production',
            'provider'              => 'clicksend',
            'account_sid'           => 'your_clicksend_username',  // replace
            'auth_token'            => 'your_clicksend_api_key',   // replace
            'from_number'           => 'TinyTrail',                // max 11 chars, no spaces
            'messaging_service_sid' => null,
            'mode'                  => 'production',
            'is_active'             => false,
        ]);

        $this->command->info('✅ SMS credentials seeded.');
    }
}
