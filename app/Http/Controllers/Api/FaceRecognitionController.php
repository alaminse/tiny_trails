<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaceData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\Upload;
use Modules\UserRolePermission\app\Models\Driver;

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
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check if user is a driver
        $driver = Driver::where('user_id', $user->id)->first();

        if ($driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver Not found.'
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
                'is_verified' => 1
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
            'error' => $e->getMessage()
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
            $user = Auth::user();

            // শুধুমাত্র logged-in user এর face data
            $faceData = FaceData::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$faceData) {
                return response()->json([
                    'success' => false,
                    'message' => 'আপনার মুখ নিবন্ধিত নেই',
                    'has_face' => false
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'মুখের তথ্য পাওয়া গেছে',
                'has_face' => true,
                'data' => [
                    'id' => $faceData->id,
                    'user_id' => $faceData->user_id,
                    'driver_name' => $faceData->driver_name,
                    'embedding' => $faceData->embedding, // এই embedding verify করার সময় ব্যবহার হবে
                    'image_url' => $faceData->face_image_path
                        ? asset('storage/' . $faceData->face_image_path)
                        : null,
                    'registered_at' => $faceData->created_at->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'তথ্য লোড করতে সমস্যা হয়েছে',
                'error' => $e->getMessage()
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
                    'errors' => $validator->errors()
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
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
