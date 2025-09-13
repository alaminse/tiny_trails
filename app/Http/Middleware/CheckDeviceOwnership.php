<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\KidDevice;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $kidId = $request->route('kid');
        $imei = $request->route('imei');

        if ($imei) {
            $device = KidDevice::where('kid_id', $kidId)
                              ->where('imei', $imei)
                              ->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'ডিভাইস খুঁজে পাওয়া যায়নি বা আপনার অধিকারে নেই'
                ], 404);
            }

            $request->merge(['device' => $device]);
        }

        return $next($request);
    }
}
