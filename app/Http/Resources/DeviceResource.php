<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'imei' => $this->imei,
            'device_name' => $this->device_name,
            'device_model' => $this->device_model,
            'is_connected' => $this->is_connected,
            'status' => $this->status,
            'is_live' => $this->is_live,
            'last_seen_at' => $this->last_seen_at,
            'last_seen_minutes_ago' => $this->last_seen_at ?
                $this->last_seen_at->diffInMinutes(now()) : null,

            // Battery Information
            'battery' => [
                'level' => $this->battery_level,
                'status' => $this->battery_status,
                'health' => $this->battery_health,
                'is_charging' => $this->is_charging,
                'is_low_battery' => $this->isLowBattery(),
                'is_critical_battery' => $this->isCriticalBattery(),
                'last_updated' => $this->last_battery_update,
            ],

            // Connection Information
            'connection' => [
                'signal_strength' => $this->signal_strength,
                'quality' => $this->connection_quality,
                'network_type' => $this->network_type,
                'has_good_connection' => $this->hasGoodConnection(),
            ],

            // Location Information
            'location' => $this->last_location,
            'gps_enabled' => $this->gps_enabled,

            // Additional Info
            'device_info' => $this->device_info,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
