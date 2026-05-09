<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountDeletionRequest;          // Form Request
use App\Models\DeletionRequest;                        // ← Model rename করুন
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeleteAccountController extends Controller
{
    public function index(): View
    {
        return view('frontend.account');
    }

    public function store(AccountDeletionRequest $request): RedirectResponse
{
    $existing = DeletionRequest::where('email', $request->email)
        ->whereIn('status', [
            DeletionRequest::STATUS_PENDING,
            DeletionRequest::STATUS_PROCESSING,
        ])
        ->first();

    if ($existing) {
        return redirect()
            ->to('/delete-account')
            ->with('warning', 'A deletion request for this email is already being processed. We will contact you shortly.');
    }

    DeletionRequest::create([
        'email'      => $request->email,
        'reason'     => $request->reason,
        'comments'   => $request->comments,
        'status'     => DeletionRequest::STATUS_PENDING,
        'ip_address' => $request->ip(),
    ]);

    return redirect()
        ->to('/delete-account')
        ->with('success', 'Your deletion request has been submitted. We will process it within 30 days and send a confirmation to your email.');
}
}
