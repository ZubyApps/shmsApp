<?php

namespace App\Notifications;

use App\Channels\CustomSmsChannel;
use App\Contracts\CustomSmsNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendSingleCustomSms extends Notification implements CustomSmsNotificationInterface, ShouldQueue
{
    use Queueable;

    public $tries = 5;
    
    public $timeout = 12;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $recipient, 
        private string $phone, 
        private string $smsDetails,
        private string $sender
    )
    {
       ;
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

    public function toCustomSms(mixed $notifiable): array
    {
        return [
                'name'          => $this->recipient,
                'to'            => $this->phone,
                'messageType'   => 'Custom-'. $this->sender,
                'message'       => $this->smsDetails
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
