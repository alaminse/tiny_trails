<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TrackSolidProService
{
    private $baseUrl;
    private $appKey;
    private $userId;
    private $userPwdMd5;
    private $accessToken;

    public function __construct()
    {
        // $this->baseUrl = config('tracksolid.base_url', 'https://hk-open.tracksolidpro.com');
        // $this->appKey = config('tracksolid.app_key');
        // $this->userId = config('tracksolid.user_id');
        // $this->userPwdMd5 = config('tracksolid.user_pwd_md5');


        $this->baseUrl = env('TRACKSOLID_BASE_URL');
        $this->appKey = env('TRACKSOLID_APP_KEY');
        // $this->appSecret = env('TRACKSOLID_APP_SECRET');
        $this->userId = env('TRACKSOLID_USER_ID');
        $this->userPwdMd5 = env('TRACKSOLID_USER_PASSWORD');
    }

    /**
     * Get access token from TrackSolidPro
     */
    private function getAccessToken()
    {
        // Check if token exists in cache
        $cacheKey = 'tracksolid_token_' . $this->userId;
        $token = Cache::get($cacheKey);

        if ($token) {
            return $token;
        }

        try {
            $timestamp = date('Y-m-d H:i:s');
            $sign = $this->generateSign([
                'method' => 'jimi.oauth.token.get',
                'app_key' => $this->appKey,
                'user_id' => $this->userId,
                'user_pwd_md5' => $this->userPwdMd5,
                'expires_in' => 7200,
                'timestamp' => $timestamp,
                'format' => 'json',
                'v' => '0.9',
                'sign_method' => 'md5',
            ]);

            $response = Http::get($this->baseUrl . '/route/rest', [
                'method' => 'jimi.oauth.token.get',
                'app_key' => $this->appKey,
                'user_id' => $this->userId,
                'user_pwd_md5' => $this->userPwdMd5,
                'expires_in' => 7200,
                'timestamp' => $timestamp,
                'format' => 'json',
                'v' => '0.9',
                'sign_method' => 'md5',
                'sign' => $sign,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']['accessToken'])) {
                    $token = $data['result']['accessToken'];
                    // Cache token for 2 hours minus 5 minutes for safety
                    Cache::put($cacheKey, $token, now()->addSeconds(7200 - 300));
                    return $token;
                }
            }

            Log::error('Failed to get TrackSolidPro access token', [
                'response' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error getting TrackSolidPro access token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate signature for API calls
     */
    private function generateSign($params)
    {
        // Remove sign parameter if exists
        unset($params['sign']);

        // Sort parameters
        ksort($params);

        // Build query string
        $queryString = '';
        foreach ($params as $key => $value) {
            $queryString .= $key . $value;
        }

        // Add app secret (this should be in your config)
        $appSecret = config('tracksolid.app_secret');
        $queryString = $appSecret . $queryString . $appSecret;

        return strtoupper(md5($queryString));
    }

    /**
     * Make authenticated API call
     */
    private function makeApiCall($method, $params = [])
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        try {
            $timestamp = date('Y-m-d H:i:s');
            $baseParams = [
                'method' => $method,
                'access_token' => $token,
                'timestamp' => $timestamp,
                'format' => 'json',
                'v' => '0.9',
                'sign_method' => 'md5',
            ];

            $allParams = array_merge($baseParams, $params);
            $sign = $this->generateSign($allParams);
            $allParams['sign'] = $sign;

            $response = Http::get($this->baseUrl . '/route/rest', $allParams);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TrackSolidPro API call failed', [
                'method' => $method,
                'params' => $params,
                'response' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error making TrackSolidPro API call: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get device information by IMEI
     */
    public function getDeviceInfo($imei)
    {
        $response = $this->makeApiCall('jimi.device.query', [
            'imei' => $imei
        ]);

        if ($response && isset($response['result'])) {
            return [
                'device_id' => $response['result']['device_id'] ?? null,
                'imei' => $response['result']['imei'] ?? null,
                'device_name' => $response['result']['device_name'] ?? null,
                'device_type' => $response['result']['device_type'] ?? null,
                'status' => $response['result']['status'] ?? null,
            ];
        }

        return null;
    }

    /**
     * Get device current status
     */
    public function getDeviceStatus($imei)
    {
        $response = $this->makeApiCall('jimi.device.location.get', [
            'imei' => $imei
        ]);

        if ($response && isset($response['result'])) {
            $data = $response['result'];
            return [
                'is_online' => ($data['status'] ?? '') === 'online',
                'battery_level' => $data['battery'] ?? null,
                'signal_strength' => $data['gsm'] ?? null,
                'location' => [
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lng'] ?? null,
                    'address' => $data['address'] ?? null,
                    'accuracy' => $data['accuracy'] ?? null,
                    'speed' => $data['speed'] ?? null,
                    'timestamp' => isset($data['locate_time']) ?
                        date('Y-m-d H:i:s', $data['locate_time']) : null,
                ],
            ];
        }

        return null;
    }

    /**
     * Start device tracking
     */
    public function startTracking($imei)
    {
        $response = $this->makeApiCall('jimi.device.track.start', [
            'imei' => $imei,
            'track_interval' => 60, // Track every 60 seconds
        ]);

        return $response && isset($response['code']) && $response['code'] === 0;
    }

    /**
     * Stop device tracking
     */
    public function stopTracking($imei)
    {
        $response = $this->makeApiCall('jimi.device.track.stop', [
            'imei' => $imei
        ]);

        return $response && isset($response['code']) && $response['code'] === 0;
    }

    /**
     * Get device location history
     */
    public function getLocationHistory($imei, $startTime, $endTime)
    {
        $response = $this->makeApiCall('jimi.device.track.get', [
            'imei' => $imei,
            'map_type' => 1,
            'start_time' => strtotime($startTime),
            'end_time' => strtotime($endTime),
        ]);

        if ($response && isset($response['result']['tracks'])) {
            $locations = [];
            foreach ($response['result']['tracks'] as $track) {
                $locations[] = [
                    'latitude' => $track['lat'],
                    'longitude' => $track['lng'],
                    'address' => $track['address'] ?? null,
                    'speed' => $track['speed'] ?? null,
                    'timestamp' => date('Y-m-d H:i:s', $track['locate_time']),
                ];
            }
            return $locations;
        }

        return [];
    }

    /**
     * Send command to device
     */
    public function sendCommand($imei, $command, $params = [])
    {
        $commandParams = [
            'imei' => $imei,
            'command' => $command,
        ];

        // Add specific command parameters
        switch ($command) {
            case 'locate':
                // Request immediate location
                $method = 'jimi.device.command.send';
                $commandParams['content'] = 'LOCATE';
                break;

            case 'reboot':
                // Restart device
                $method = 'jimi.device.command.send';
                $commandParams['content'] = 'REBOOT';
                break;

            case 'set_interval':
                // Set tracking interval
                $method = 'jimi.device.command.send';
                $interval = $params['interval'] ?? 60;
                $commandParams['content'] = "UPLOAD,{$interval}";
                break;

            default:
                return false;
        }

        $response = $this->makeApiCall($method, $commandParams);
        return $response && isset($response['code']) && $response['code'] === 0;
    }

    /**
     * Set geofence for device
     */
    public function setGeofence($imei, $latitude, $longitude, $radius, $name = 'Geofence')
    {
        $response = $this->makeApiCall('jimi.device.fence.create', [
            'imei' => $imei,
            'fence_name' => $name,
            'fence_type' => 1, // Circle fence
            'center_lat' => $latitude,
            'center_lng' => $longitude,
            'radius' => $radius,
            'in_alarm' => 1, // Alert when entering
            'out_alarm' => 1, // Alert when leaving
        ]);

        return $response && isset($response['code']) && $response['code'] === 0;
    }

    /**
     * Get device alarms/alerts
     */
    public function getDeviceAlarms($imei, $startTime, $endTime)
    {
        $response = $this->makeApiCall('jimi.device.alarm.get', [
            'imei' => $imei,
            'start_time' => strtotime($startTime),
            'end_time' => strtotime($endTime),
        ]);

        if ($response && isset($response['result']['alarms'])) {
            $alarms = [];
            foreach ($response['result']['alarms'] as $alarm) {
                $alarms[] = [
                    'type' => $alarm['alarm_type'],
                    'message' => $alarm['alarm_msg'],
                    'latitude' => $alarm['lat'] ?? null,
                    'longitude' => $alarm['lng'] ?? null,
                    'timestamp' => date('Y-m-d H:i:s', $alarm['alarm_time']),
                ];
            }
            return $alarms;
        }

        return [];
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        $token = $this->getAccessToken();
        return $token !== null;
    }
}
