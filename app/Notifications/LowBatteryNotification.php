<?php

namespace App\Notifications;

use App\Models\KidDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBatteryNotification extends Notification
{
    use Queueable;

    public function __construct(
        private KidDevice $device
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('ডিভাইসের ব্যাটারি কম')
                    ->line("{$this->device->kid->name} এর ডিভাইসের ব্যাটারি কম ({$this->device->battery_level}%)")
                    ->line('অনুগ্রহ করে ডিভাইসটি চার্জ করুন।')
                    ->action('ডিভাইস দেখুন', url('/devices/' . $this->device->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'device_id' => $this->device->id,
            'imei' => $this->device->imei,
            'device_name' => $this->device->device_name,
            'battery_level' => $this->device->battery_level,
            'kid_name' => $this->device->kid->name,
            'message' => "ডিভাইসের ব্যাটারি কম ({$this->device->battery_level}%)"
        ];
    }
}
