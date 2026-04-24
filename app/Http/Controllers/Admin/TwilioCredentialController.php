<?php
// app/Http/Controllers/Admin/TwilioCredentialController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TwilioCredential;
use App\Services\SmsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TwilioCredentialController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('view-twilio-credentials');
        $credentials = TwilioCredential::latest()->get();
        return view('backend.twilio.index', compact('credentials'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-twilio-credentials');

        $data = $request->validate([
            'label'                 => 'required|string|max:100',
            'provider'              => 'required|in:twilio,clicksend',
            'account_sid'           => 'required|string',
            'auth_token'            => 'required|string',
            'from_number'           => 'required|string',
            'messaging_service_sid' => 'nullable|string',
            'mode'                  => 'required|in:demo,production',
        ]);

        TwilioCredential::create($data);

        return back()->with('success', '✅ Credential added successfully.');
    }

    public function update(Request $request, TwilioCredential $twilioCredential)
    {
        $this->authorize('manage-twilio-credentials');

        $data = $request->validate([
            'label'                 => 'required|string|max:100',
            'provider'              => 'required|in:twilio,clicksend',
            'account_sid'           => 'required|string',
            'auth_token'            => 'required|string',
            'from_number'           => 'required|string',
            'messaging_service_sid' => 'nullable|string',
            'mode'                  => 'required|in:demo,production',
        ]);

        $twilioCredential->update($data);

        return back()->with('success', '✅ Credential updated.');
    }

    public function activate(TwilioCredential $twilioCredential)
    {
        $this->authorize('manage-twilio-credentials');
        TwilioCredential::activate($twilioCredential->id);
        return back()->with('success',
            "✅ \"{$twilioCredential->label}\" is now active."
        );
    }

    public function destroy(TwilioCredential $twilioCredential)
    {
        $this->authorize('manage-twilio-credentials');

        if ($twilioCredential->is_active) {
            return back()->with('error', '❌ Cannot delete the active credential.');
        }

        $twilioCredential->delete();
        return back()->with('success', '✅ Credential deleted.');
    }

    public function testSend(Request $request)
    {
        $this->authorize('manage-twilio-credentials');
        $request->validate(['to' => 'required|string']);

        try {
            $sms    = new SmsService();
            $result = $sms->sendSms(
                $request->to,
                '🧪 Tiny Trails test message. SMS provider is connected!'
            );

            return back()->with('success',
                "✅ Test SMS sent via {$sms->getProvider()}!"
            );

        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }

    public function validateCredentials()
    {
        $this->authorize('manage-twilio-credentials');

        try {
            $sms   = new SmsService();
            $valid = $sms->validateCredentials();

            return back()->with(
                $valid ? 'success' : 'error',
                $valid
                    ? "✅ {$sms->getProvider()} credentials are valid!"
                    : '❌ Invalid credentials.'
            );

        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
