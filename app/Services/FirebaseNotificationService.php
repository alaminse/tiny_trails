<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        if (!$fcmToken) {
            return false;
        }

        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification($notification)
            ->withData($data);

        try {
            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase notification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToMultipleDevices($fcmTokens, $title, $body, $data = [])
    {
        if (empty($fcmTokens)) {
            return false;
        }

        $notification = Notification::create($title, $body);

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        try {
            $this->messaging->sendMulticast($message, $fcmTokens);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase multicast notification failed: ' . $e->getMessage());
            return false;
        }
    }
}
