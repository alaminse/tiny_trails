<?php
// app/Http/Controllers/Admin/FaceVerificationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaceVerificationService;
use Modules\UserRolePermission\app\Models\Driver;

class FaceVerificationController extends Controller
{
    public function __construct(
        protected FaceVerificationService $faceService
    ) {}

    public function index()
    {
        $drivers = Driver::with('user')
            ->where('status', 'active')
            ->get()
            ->sortBy(function ($d) {
                return match ($d->face_verification_status) {
                    'expiring' => 1,
                    'verified' => 2,
                    'expired' => 3,
                    'unverified' => 4,
                    default => 5,
                };
            })
            ->values();

        $verifiedCount = $drivers->where('face_verification_status', 'verified')
            ->filter(fn ($d) => $this->faceService->isVerified($d->id))
            ->count();

        $expiredCount = $drivers
            ->whereIn('face_verification_status', ['expired', 'unverified'])
            ->count();

        $expiringCount = $drivers->filter(function ($d) {
            if ($d->face_verification_status !== 'verified') return false;
            if (!$d->face_verified_until) return false;

            return now()->diffInMinutes($d->face_verified_until, false) <= 30
                && now()->diffInMinutes($d->face_verified_until, false) > 0;
        })->count();

        return view('backend.face-verification.index', compact(
            'drivers',
            'verifiedCount',
            'expiredCount',
            'expiringCount'
        ));
    }
}
