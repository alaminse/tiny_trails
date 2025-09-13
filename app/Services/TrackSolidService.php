<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrackSolidService
{
    private $baseUrl;
    private $appKey;
    private $appSecret;
    private $userId;
    private $userPassword;

    public function __construct()
    {
        $this->baseUrl = env('TRACKSOLID_BASE_URL');
        $this->appKey = env('TRACKSOLID_APP_KEY');
        $this->appSecret = env('TRACKSOLID_APP_SECRET');
        $this->userId = env('TRACKSOLID_USER_ID');
        $this->userPassword = env('TRACKSOLID_USER_PASSWORD');
    }

    /**
     * Get or refresh access token
     */
    public function getAccessToken()
    {
        $cachedToken = Cache::get('tracksolid_access_token');

        if ($cachedToken) {
            return [
                'success' => true,
                'access_token' => $cachedToken,
                'from_cache' => true
            ];
        }

        try {
            $timestamp = Carbon::now('UTC')->format('Y-m-d H:i:s');

            $params = [
                'method' => 'jimi.oauth.token.get',
                'app_key' => $this->appKey,
                'user_id' => $this->userId,
                'user_pwd_md5' => $this->userPassword,
                'expires_in' => 7200,
                'timestamp' => $timestamp,
                'format' => 'json',
                'v' => '0.9',
                'sign_method' => 'md5'
            ];

            $queryString = http_build_query($params);
            $response = Http::timeout(30)->get($this->baseUrl . '?' . $queryString);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['code']) && $data['code'] === 0) {
                    $accessToken = $data['result']['accessToken'];
                    $expiresIn = $data['result']['expiresIn'];

                    // Cache for expires_in - 60 seconds (buffer)
                    Cache::put('tracksolid_access_token', $accessToken, now()->addSeconds($expiresIn - 60));

                    return [
                        'success' => true,
                        'access_token' => $accessToken,
                        'expires_in' => $expiresIn,
                        'account' => $data['result']['account'],
                        'from_cache' => false
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Failed to get access token',
                'response' => $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Make authenticated API request
     */
    private function makeApiRequest($method, $extraParams = [])
    {
        $tokenResult = $this->getAccessToken();

        if (!$tokenResult['success']) {
            return $tokenResult;
        }

        $timestamp = Carbon::now('UTC')->format('Y-m-d H:i:s');

        $params = array_merge([
            'method' => $method,
            'app_key' => $this->appKey,
            'access_token' => $tokenResult['access_token'],
            'timestamp' => $timestamp,
            'format' => 'json',
            'v' => '0.9',
            'sign_method' => 'md5'
        ], $extraParams);

        try {
            $queryString = http_build_query($params);
            $response = Http::timeout(30)->get($this->baseUrl . '?' . $queryString);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'API request failed',
                'status' => $response->status(),
                'response' => $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all devices for account
     */
    public function getDevices($target = null)
    {
        $target = $target ?: $this->userId;

        return $this->makeApiRequest('jimi.user.device.list', [
            'target' => $target
        ]);
    }

    /**
     * Get device details by IMEI
     */
    public function getDeviceDetails($imei)
    {
        return $this->makeApiRequest('jimi.track.device.detail', [
            'imei' => $imei
        ]);
    }

    /**
     * Get device location(s)
     */
    public function getDeviceLocation($imeis, $mapType = null)
    {
        $params = ['imeis' => $imeis];
        if ($mapType) {
            $params['map_type'] = $mapType;
        }

        return $this->makeApiRequest('jimi.device.location.get', $params);
    }

    /**
     * Get devices location by account
     */
    public function getDevicesLocationByAccount($target = null, $mapType = null)
    {
        $target = $target ?: $this->userId;
        $params = ['target' => $target];

        if ($mapType) {
            $params['map_type'] = $mapType;
        }

        return $this->makeApiRequest('jimi.user.device.location.list', $params);
    }

    /**
     * Get device track data
     */
    public function getDeviceTrack($imei, $beginTime, $endTime, $mapType = null)
    {
        $params = [
            'imei' => $imei,
            'begin_time' => $beginTime,
            'end_time' => $endTime
        ];

        if ($mapType) {
            $params['map_type'] = $mapType;
        }

        return $this->makeApiRequest('jimi.device.track.list', $params);
    }

    /**
     * Update device information
     */
    public function updateDevice($imei, $updateData)
    {
        $params = array_merge(['imei' => $imei], $updateData);

        return $this->makeApiRequest('jimi.open.device.update', $params);
    }

    /**
     * Get device alarms
     */
    public function getDeviceAlarms($imei, $beginTime = null, $endTime = null, $pageNo = 1, $pageSize = 50)
    {
        $params = [
            'imei' => $imei,
            'page_no' => $pageNo,
            'page_size' => $pageSize
        ];

        if ($beginTime && $endTime) {
            $params['begin_time'] = $beginTime;
            $params['end_time'] = $endTime;
        }

        return $this->makeApiRequest('jimi.device.alarm.list', $params);
    }
}
