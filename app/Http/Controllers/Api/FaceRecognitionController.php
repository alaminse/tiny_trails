<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\UserRolePermission\app\Models\Driver;
use Carbon\Carbon;

class FaceRecognitionController extends Controller
{
    use Upload;

    /**
     * Face registration - নতুন face store করা
     * POST /api/face/store
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'face_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
                'embedding' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();

            // Check if user is a driver
            $driver = Driver::where('user_id', $user->id)->first();

            if ($driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver Not found.',
                ], 422);
            }

            // Delete old face image if exists
            if ($request->file('face_image')) {
                $imagePath = $this->uploadFile($request->file('face_image'), 'driver/face');
                if ($driver->faceImage) {
                    $this->deleteFile($driver->faceImage);
                }
            }

            // Update driver record
            $driver->update([
                'face_embedding' => $request->embedding,
                'faceImage' => $imagePath,
                'is_verified' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Driver face registered successfully.',
                'data' => $driver->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register face',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get logged-in user's face data - verification এর জন্য
     * GET /api/face/my-face
     *
     * এই endpoint শুধুমাত্র current user এর face data return করবে
     */
    public function getMyFace(Request $request)
    {
        try {
            $driver = Auth::guard('driver')->user();

            if (!$driver) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Unauthenticated.',
                    'has_face' => false,
                ], 401);
            }

            // drivers table এ face_embedding না থাকলে
            if (empty($driver->face_embedding)) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'আপনার মুখ নিবন্ধিত নেই',
                    'has_face' => false,
                ], 404);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'মুখের তথ্য পাওয়া গেছে',
                'has_face' => true,
                'data'     => [
                    'id'          => $driver->id,
                    'user_id'     => $driver->user_id,
                    'driver_name' => optional($driver->user)->first_name . ' ' . optional($driver->user)->last_name,
                    'embedding'   => $driver->face_embedding, // drivers.face_embedding
                    'image_url'   => $driver->face_image
                        ? asset('storage/' . $driver->face_image)
                        : null,
                    'registered_at' => $driver->updated_at?->format('Y-m-d H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'তথ্য লোড করতে সমস্যা হয়েছে',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // /**
    //  * Check if user has registered face
    //  * GET /api/face/check
    //  */
    // public function checkFaceExists(Request $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         $exists = FaceData::where('user_id', $user->id)
    //             ->where('is_active', true)
    //             ->exists();

    //         return response()->json([
    //             'success' => true,
    //             'has_face' => $exists,
    //             'message' => $exists
    //                 ? 'মুখ নিবন্ধিত আছে'
    //                 : 'মুখ নিবন্ধিত নেই'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'চেক করতে সমস্যা হয়েছে',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Log verification attempt
     * POST /api/face/log-verification
     */
    public function logVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'similarity_score' => 'required|numeric|min:0|max:1',
                'verified' => 'required|boolean',
                'timestamp' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();

            // Verification log করা
            DB::table('face_verification_logs')->insert([
                'user_id' => $user->id,
                'similarity_score' => $request->similarity_score,
                'verified' => $request->verified,
                'device_info' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'verified_at' => $request->timestamp
                    ? \Carbon\Carbon::parse($request->timestamp)
                    : now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification logged',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ── Cosine similarity between two float[] ─────────────
    private function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    // ──────────────────────────────────────────────────────
    // POST /api/driver/face-verify
    // Body: { embeddings: [0.12, -0.34, ...] }   (512 or 128 floats)
    // ──────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $request->validate([
            'embeddings' => 'required|array|min:32',
            'embeddings.*' => 'numeric',
        ]);

        /** @var Driver $driver */
        $driver = Auth::guard('driver')->user();

        if (! $driver) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // ── Stored embedding ──────────────────────────────
        $storedRaw = $driver->face_embedding;

        if (empty($storedRaw)) {
            return response()->json([
                'success' => false,
                'is_verified' => false,
                'message' => 'No face registered for this driver. Please contact admin.',
            ], 422);
        }

        // face_embedding stored as JSON string or array
        $storedEmbedding = is_string($storedRaw)
            ? json_decode($storedRaw, true)
            : (array) $storedRaw;

        $liveEmbedding = $request->embeddings;

        // ── Compare ───────────────────────────────────────
        $similarity = $this->cosineSimilarity($storedEmbedding, $liveEmbedding);

        // Threshold: 0.75 cosine similarity (adjust as needed)
        $threshold = 0.75;
        $matched = $similarity >= $threshold;

        if (! $matched) {
            return response()->json([
                'success' => false,
                'is_verified' => false,
                'similarity' => round($similarity, 4),
                'message' => 'Face does not match. Please try again.',
            ], 422);
        }

        // ── Set verified until end of today ──────────────
        $now = Carbon::now();
        $verifiedUntil = $now->copy()->endOfDay(); // today 23:59:59

        $driver->update([
            'face_verified_at' => $now,
            'face_verified_until' => $verifiedUntil,
        ]);

        return response()->json([
            'success' => true,
            'is_verified' => true,
            'similarity' => round($similarity, 4),
            'face_verified_at' => $now->toDateTimeString(),
            'face_verified_until' => $verifiedUntil->toDateTimeString(),
            'message' => 'Face verified successfully! Valid until '.$verifiedUntil->format('h:i A'),
        ]);
    }

    // ──────────────────────────────────────────────────────
    // GET /api/driver/face-verify/status
    // Returns current verification status
    // ──────────────────────────────────────────────────────
    public function status(Request $request)
    {
        /** @var Driver $driver */
        $driver = Auth::guard('driver')->user();

        if (! $driver) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isVerified = $driver->isVerified; // uses accessor below

        return response()->json([
            'success' => true,
            'is_verified' => $isVerified,
            'face_verified_at' => $driver->face_verified_at,
            'face_verified_until' => $driver->face_verified_until,
            'message' => $isVerified
                ? 'Verified until '.Carbon::parse($driver->face_verified_until)->format('h:i A')
                : 'Not verified today. Please complete face verification.',
        ]);
    }
}
