<?php

namespace App\Notifications;

use App\Channels\CustomSmsChannel;
use App\Contracts\CustomSmsNotificationInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MedicationReminderNotification extends Notification implements CustomSmsNotificationInterface, ShouldQueue
{
    use Queueable;

    public $tries = 5;
    
    public $timeout = 12;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $firstName,
        private string $phone, 
        private string $scheduledTime)
    {
        
    }

    public function backoff(): array
    {
        return [10, 30, 60, 120, 300];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [CustomSmsChannel::class];
    }

    public function toCustomSms($notifiable): array
    {
        $firstName = $notifiable->first_name;
        $phoneNumber = $notifiable->phone;

        $time = (new Carbon($this->scheduledTime))->format('g:iA');

        $message = 'Dear ' .$this->firstName. ', pls be reminded of your medication by '. $time . ' today. Courtesy: Sandra Hospital Management System';
        return [
            'name'          => $firstName,
            'to'            => $phoneNumber,
            'messageType'   => 'Medication reminder',
            'message'       => $message
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function withDelay($notifiable): array
    {
        return [
            \App\Channels\CustomSmsChannel::class => now()->addSeconds(5),
        ];
    }
}
