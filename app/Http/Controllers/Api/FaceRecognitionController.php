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

    // ── Helper: get Driver from sanctum user ──────────────
    private function getDriver(): ?Driver
    {
        $user = Auth::user(); // sanctum user
        if (!$user) return null;
        return Driver::where('user_id', $user->id)->first();
    }

    // ──────────────────────────────────────────────────────
    // POST /api/auth/face/store
    // ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'face_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'embedding'  => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $driver = $this->getDriver();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found.',
                ], 404);
            }

            $imagePath = null;
            if ($request->file('face_image')) {
                // Delete old image
                if ($driver->face_image) {
                    $this->deleteFile($driver->face_image);
                }
                $imagePath = $this->uploadFile($request->file('face_image'), 'driver/face');
            }

            $updateData = [
                'face_embedding' => $request->embedding,
                'is_verified'    => 1,
            ];
            if ($imagePath) {
                $updateData['face_image'] = $imagePath;
            }

            $driver->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Driver face registered successfully.',
                'data'    => $driver->id,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register face',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────
    // GET /api/auth/face/my-face
    // ──────────────────────────────────────────────────────
    public function getMyFace(Request $request)
    {
        try {
            $driver = $this->getDriver();

            if (!$driver) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Driver not found.',
                    'has_face' => false,
                ], 404);
            }

            if (empty($driver->face_embedding)) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'আপনার মুখ নিবন্ধিত নেই',
                    'has_face' => false,
                ], 404);
            }

            $user = Auth::user();

            return response()->json([
                'success'  => true,
                'message'  => 'মুখের তথ্য পাওয়া গেছে',
                'has_face' => true,
                'data'     => [
                    'id'            => $driver->id,
                    'user_id'       => $driver->user_id,
                    'driver_name'   => $user->first_name . ' ' . $user->last_name,
                    'embedding'     => $driver->face_embedding,
                    'image_url'     => $driver->face_image
                        ? asset('storage/' . $driver->face_image)
                        : null,
                    'is_verified'   => (int) $driver->getRawOriginal('is_verified'),
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

    // ──────────────────────────────────────────────────────
    // GET /api/auth/face-verify/status
    // ──────────────────────────────────────────────────────
    public function status(Request $request)
    {
        $driver = $this->getDriver();

        if (!$driver) {
            return response()->json([
                'success'     => true,
                'is_verified' => false,
                'has_face'    => false,
                'message'     => 'Driver profile not found.',
            ]);
        }

        // Check if face_verified_until is still in future
        $isVerified = false;
        if ($driver->face_verified_until) {
            $isVerified = Carbon::now()->lessThanOrEqualTo(
                Carbon::parse($driver->face_verified_until)
            );
        }

        return response()->json([
            'success'             => true,
            'is_verified'         => $isVerified,
            'has_face'            => !empty($driver->face_embedding),
            'face_verified_at'    => $driver->face_verified_at,
            'face_verified_until' => $driver->face_verified_until,
            'message'             => $isVerified
                ? 'Verified until ' . Carbon::parse($driver->face_verified_until)->format('h:i A')
                : 'Not verified today. Please complete face verification.',
        ]);
    }

    // ──────────────────────────────────────────────────────
    // POST /api/auth/face-verify
    // Body: { embeddings: [...], distance: 0.9 }
    // ──────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $request->validate([
            'embeddings'   => 'required|array|min:32',
            'embeddings.*' => 'numeric',
        ]);

        $driver = $this->getDriver();

        if (!$driver) {
            return response()->json([
                'success'     => false,
                'is_verified' => false,
                'message'     => 'Driver profile not found.',
            ], 404);
        }

        // ── Stored embedding ──────────────────────────────
        $storedRaw = $driver->face_embedding;

        if (empty($storedRaw)) {
            return response()->json([
                'success'     => false,
                'is_verified' => false,
                'message'     => 'No face registered. Please contact admin.',
            ], 422);
        }

        // Parse stored embedding — comma-separated OR JSON
        if (is_string($storedRaw) && str_contains($storedRaw, ',') && !str_starts_with(trim($storedRaw), '[')) {
            // "0.123,-0.456,..." format
            $storedEmbedding = array_map('floatval', explode(',', $storedRaw));
        } elseif (is_string($storedRaw)) {
            $storedEmbedding = json_decode($storedRaw, true);
        } else {
            $storedEmbedding = (array) $storedRaw;
        }

        if (!is_array($storedEmbedding) || empty($storedEmbedding)) {
            return response()->json([
                'success'     => false,
                'is_verified' => false,
                'message'     => 'Stored face data is corrupted. Please contact admin.',
            ], 422);
        }

        $liveEmbedding = array_map('floatval', $request->embeddings);

        // ── Cosine similarity ─────────────────────────────
        $similarity = $this->cosineSimilarity($storedEmbedding, $liveEmbedding);
        $threshold  = 0.75;
        $matched    = $similarity >= $threshold;

        if (!$matched) {
            return response()->json([
                'success'     => false,
                'is_verified' => false,
                'similarity'  => round($similarity, 4),
                'message'     => 'Face does not match. Please try again.',
            ], 422);
        }

        // ── Set verified until end of today ──────────────
        $now           = Carbon::now();
        $verifiedUntil = $now->copy()->endOfDay();

        $driver->update([
            'face_verified_at'    => $now,
            'face_verified_until' => $verifiedUntil,
        ]);

        return response()->json([
            'success'             => true,
            'is_verified'         => true,
            'similarity'          => round($similarity, 4),
            'face_verified_at'    => $now->toDateTimeString(),
            'face_verified_until' => $verifiedUntil->toDateTimeString(),
            'message'             => 'Face verified! Valid until ' . $verifiedUntil->format('h:i A'),
        ]);
    }

    // ──────────────────────────────────────────────────────
    // POST /api/auth/face/log-verification
    // ──────────────────────────────────────────────────────
    public function logVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'similarity_score' => 'required|numeric|min:0|max:1',
                'verified'         => 'required|boolean',
                'timestamp'        => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();

            DB::table('face_verification_logs')->insert([
                'user_id'          => $user->id,
                'similarity_score' => $request->similarity_score,
                'verified'         => $request->verified,
                'device_info'      => $request->header('User-Agent'),
                'ip_address'       => $request->ip(),
                'verified_at'      => $request->timestamp
                    ? Carbon::parse($request->timestamp)
                    : now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification logged',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ── Cosine similarity ─────────────────────────────────
    private function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || empty($a)) return 0.0;

        $dot = $normA = $normB = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $dot   += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0 || $normB == 0) return 0.0;
        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
