<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\UserRolePermission\app\Models\Driver;

class FaceVerificationService
{
    // ✅ How long face verification stays valid (hours)
    const VERIFICATION_WINDOW_HOURS = 5;

    /**
     * ✅ Check if driver's face verification is still valid
     */
    public function isVerified(int $driverId): bool
    {
        $driver = Driver::find($driverId);

        if (!$driver) return false;

        return $driver->face_verification_status === 'verified'
            && $driver->face_verified_until
            && $driver->face_verified_until->isFuture();
    }

    /**
     * ✅ Verify driver face — compare selfie against stored profile photo
     * Called once at shift start, valid for VERIFICATION_WINDOW_HOURS
     */
    public function verify(int $driverId, string $selfieBase64): array
    {
        $driver = Driver::find($driverId);

        if (!$driver) {
            return ['success' => false, 'message' => 'Driver not found.'];
        }

        // ✅ Check if already verified and still valid
        if ($this->isVerified($driverId)) {
            return [
                'success'        => true,
                'already_valid'  => true,
                'message'        => 'Face already verified.',
                'valid_until'    => $driver->face_verified_until->toDateTimeString(),
            ];
        }

        // ✅ Compare selfie against stored face_image
        $matchResult = $this->compareFaces(
            $selfieBase64,
            $driver->face_image
        );

        if (!$matchResult['matched']) {
            Log::warning("Face verification failed for driver #{$driverId}");
            return [
                'success'    => false,
                'message'    => 'Face verification failed. Please try again.',
                'confidence' => $matchResult['confidence'] ?? null,
            ];
        }

        // ✅ Save selfie image
        $selfiePath = $this->saveSelfie($driverId, $selfieBase64);

        // ✅ Mark driver as verified for the window
        $verifiedUntil = now()->addHours(self::VERIFICATION_WINDOW_HOURS);

        $driver->update([
            'face_verified_at'        => now(),
            'face_verified_until'     => $verifiedUntil,
            'face_verification_status'=> 'verified',
            'selfie'                  => $selfiePath,  // store latest selfie
        ]);

        Log::info("Driver #{$driverId} face verified until {$verifiedUntil}");

        return [
            'success'     => true,
            'message'     => 'Face verified successfully!',
            'valid_until' => $verifiedUntil->toDateTimeString(),
            'valid_hours' => self::VERIFICATION_WINDOW_HOURS,
        ];
    }

    /**
     * ✅ Expire verification manually or via scheduler
     */
    public function expireVerification(int $driverId): void
    {
        Driver::where('id', $driverId)->update([
            'face_verification_status' => 'expired',
        ]);
    }

    /**
     * ✅ Expire all drivers whose window has passed
     * Run via scheduler every 15 minutes
     */
    public function expireAllStale(): int
    {
        return Driver::where('face_verification_status', 'verified')
            ->where('face_verified_until', '<', now())
            ->update([
                'face_verification_status' => 'expired',
            ]);
    }

    /**
     * ✅ Face comparison logic
     * Swap this out for AWS Rekognition / Azure Face API / DeepFace etc.
     */
    private function compareFaces(string $selfieBase64, ?string $storedImagePath): array
    {
        if (!$storedImagePath) {
            return ['matched' => false, 'confidence' => 0];
        }

        // -------------------------------------------------------
        // PLACEHOLDER — replace with real face comparison API
        // Example with AWS Rekognition:
        //
        // $rekognition = app('aws')->createClient('rekognition');
        // $result = $rekognition->compareFaces([
        //     'SourceImage' => ['Bytes' => base64_decode($selfieBase64)],
        //     'TargetImage' => ['S3Object' => ['Bucket' => '...', 'Name' => $storedImagePath]],
        //     'SimilarityThreshold' => 90,
        // ]);
        // $match = count($result['FaceMatches']) > 0;
        // $confidence = $result['FaceMatches'][0]['Similarity'] ?? 0;
        // return ['matched' => $match, 'confidence' => $confidence];
        // -------------------------------------------------------

        // Temporary: always return true for development
        return ['matched' => true, 'confidence' => 99.0];
    }

    /**
     * ✅ Save selfie image to storage
     */
    private function saveSelfie(int $driverId, string $base64): string
    {
        $imageData = base64_decode(
            preg_replace('#^data:image/\w+;base64,#i', '', $base64)
        );

        $path = "drivers/selfies/{$driverId}/" . now()->format('Ymd_His') . '.jpg';
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}
