<?php

namespace App\Notifications;

use App\Channels\CustomSmsChannel;
use App\Contracts\CustomSmsNotificationInterface;
use App\DataObjects\FormLinkParams;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FormLinkNotification extends Notification implements CustomSmsNotificationInterface, ShouldQueue
{
    use Queueable;

    public $tries = 3;
    
    public $timeout = 12;

    public function __construct(
        private string $link, 
        private readonly FormLinkParams $params
    )
    {
    }

    public function backoff(): array
    {
        return [10, 15, 20];
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
        $message = 'Sandra Hospital Patient Registration Form link ' . $this->link . '. This link expires in 5 minutes';
        return [
                'name'          => 'Pre-patient',
                'to'            => $this->params->phone,
                'messageType'   => 'Registration form link',
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
