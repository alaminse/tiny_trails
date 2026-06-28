<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    public function sendPhoneCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with this phone number.',
            ], 404);
        }

        $user->verificationCodes()->where('type', 'phone')->delete();

        $code = random_int(100000, 999999);

        $user->verificationCodes()->create([
            'type' => 'phone',
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        try {
            $sms = new SmsService;
            $sms->sendSms(
                $user->phone,
                "Your TinyTrails verification code is: {$code}. Valid for 15 minutes. Do not share this code.\n\nLet's use TinyTrails."
            );

            Log::info('Verification SMS sent', [
                'phone' => $user->phone,
                'provider' => $sms->getProvider(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your phone.',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send verification SMS: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not send verification code. Please try again.',
            ], 500);
        }
    }

    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'code' => 'required|integer|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $verificationCode = $user->verificationCodes()
            ->where('type', 'phone')
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $verificationCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 400);
        }

        $user->update([
            'phone_verified_at' => now(),
            'verification_status' => 'phone_verified',
        ]);

        $verificationCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully.',
        ]);
    }

    public function sendEmailVerification(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->verification_status !== 'phone_verified') {
            return response()->json(['success' => false, 'message' => 'Please verify your phone number first.'], 403);
        }

        $user->verificationCodes()->where('type', 'email')->delete();

        $code = random_int(100000, 999999);

        $user->verificationCodes()->create([
            'type' => 'email',
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code, 'email'));
        } catch (\Exception $e) {
            Log::error('Failed to send email verification email: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Could not send verification email.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to your email.',
            'code' => $code,
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $validator = Validator::make($request->all(), [
            'code' => 'required|integer|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        $verificationCode = $user->verificationCodes()
            ->where('type', 'email')
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $verificationCode) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired verification code.'], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_status' => 'email_verified',
        ]);
        $verificationCode->delete();

        return response()->json(['success' => true, 'message' => 'Email verified successfully.']);
    }

    public function verifyPin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->verification_status === 'fully_verified') {
            return response()->json(['success' => false, 'message' => 'PIN has already been set.'], 403);
        }

        // Ensure the user has verified their email first.
        if ($user->verification_status !== 'email_verified') {
            return response()->json(['success' => false, 'message' => 'Please verify your email first.'], 403);
        }

        // Validate the incoming PIN.
        $validator = Validator::make($request->all(), [
            'pin' => 'required|integer|digits:4', // Example: 4-digit PIN
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        // ✅ THE CORE CHANGE: Hash the new PIN and save it.
        $hashedPin = Hash::make($request->pin);

        $user->update([
            'security_pin' => $hashedPin,
            'verification_status' => 'fully_verified', // Mark as fully verified
        ]);

        return response()->json(['success' => true, 'message' => 'PIN set successfully! Your account is now fully verified.']);

    }
}
