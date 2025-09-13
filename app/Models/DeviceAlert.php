<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\UserRolePermission\App\Models\Kid;

class DeviceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'kid_id',
        'alert_type',
        'title',
        'message',
        'severity',
        'latitude',
        'longitude',
        'is_read',
        'triggered_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_read' => 'boolean',
        'triggered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    const TYPE_LOW_BATTERY = 'low_battery';
    const TYPE_DEVICE_OFFLINE = 'device_offline';
    const TYPE_GEOFENCE_EXIT = 'geofence_exit';
    const TYPE_GEOFENCE_ENTER = 'geofence_enter';
    const TYPE_SOS = 'sos';
    const TYPE_SPEED_LIMIT = 'speed_limit';

    /**
     * Get the device that triggered this alert
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the kid associated with this alert
     */
    public function kid()
    {
        return $this->belongsTo(Kid::class);
    }

    /**
     * Get severity color
     */
    public function getSeverityColorAttribute()
    {
        switch ($this->severity) {
            case self::SEVERITY_CRITICAL:
                return 'red';
            case self::SEVERITY_HIGH:
                return 'orange';
            case self::SEVERITY_MEDIUM:
                return 'yellow';
            default:
                return 'blue';
        }
    }

    /**
     * Scope for unread alerts
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for alerts by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for recent alerts
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('triggered_at', '>=', now()->subHours($hours));
    }

    /**
     * Mark alert as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
