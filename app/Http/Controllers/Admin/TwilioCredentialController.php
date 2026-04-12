<?php
// app/Http/Controllers/Admin/TwilioCredentialController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TwilioCredential;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class TwilioCredentialController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────
    public function index()
    {
        $this->authorize('view-twilio-credentials');

        $credentials = TwilioCredential::latest()->get();

        return view('backend.twilio.index', compact('credentials'));
    }

    // ── Store ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('manage-twilio-credentials');

        $data = $request->validate([
            'label'                 => 'required|string|max:100',
            'account_sid'           => 'required|string',
            'auth_token'            => 'required|string',
            'from_number'           => 'required|string',
            'messaging_service_sid' => 'nullable|string',
            'mode'                  => 'required|in:demo,production',
        ]);

        TwilioCredential::create($data);

        return back()->with('success', '✅ Credential added successfully.');
    }

    // ── Update ────────────────────────────────────────────────────
    public function update(Request $request, TwilioCredential $twilioCredential)
    {
        $this->authorize('manage-twilio-credentials');

        $data = $request->validate([
            'label'                 => 'required|string|max:100',
            'account_sid'           => 'required|string',
            'auth_token'            => 'required|string',
            'from_number'           => 'required|string',
            'messaging_service_sid' => 'nullable|string',
            'mode'                  => 'required|in:demo,production',
        ]);

        $twilioCredential->update($data);

        return back()->with('success', '✅ Credential updated.');
    }

    // ── Activate ──────────────────────────────────────────────────
    public function activate(TwilioCredential $twilioCredential)
    {
        $this->authorize('manage-twilio-credentials');

        TwilioCredential::activate($twilioCredential->id);

        return back()->with('success',
            "✅ \"{$twilioCredential->label}\" is now the active credential."
        );
    }

    // ── Delete ────────────────────────────────────────────────────
    public function destroy(TwilioCredential $twilioCredential)
    {
        $this->authorize('manage-twilio-credentials');

        if ($twilioCredential->is_active) {
            return back()->with('error', '❌ Cannot delete the active credential. Activate another first.');
        }

        $twilioCredential->delete();

        return back()->with('success', '✅ Credential deleted.');
    }

    // ── Test Send ─────────────────────────────────────────────────
    public function testSend(Request $request)
    {
        $this->authorize('manage-twilio-credentials');

        $request->validate(['to' => 'required|string']);

        try {
            $twilio = new TwilioService();
            $result = $twilio->sendSms(
                $request->to,
                '🧪 Tiny Trails test message. Twilio is connected!'
            );

            return back()->with('success',
                "✅ Test SMS sent! SID: {$result['sid']} | Status: {$result['status']}"
            );

        } catch (\RuntimeException $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }

    // ── Validate Credentials (no SMS) ────────────────────────────
    public function validateCredentials()
    {
        $this->authorize('manage-twilio-credentials');

        try {
            $twilio = new TwilioService();
            $valid  = $twilio->validateCredentials();

            return back()->with(
                $valid ? 'success' : 'error',
                $valid ? '✅ Credentials are valid and connected!' : '❌ Invalid credentials.'
            );

        } catch (\RuntimeException $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
