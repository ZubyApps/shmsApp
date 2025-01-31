<?php

namespace App\Notifications;

use App\Services\ChurchPlusSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PatientCardNumber extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly ChurchPlusSmsService $churchPlusSmsService)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [ChurchPlusSmsService::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable)
    {
        $firstName = $notifiable->first_name;

        Log::info('card number', ['sent to' => $firstName]);

        return $this->churchPlusSmsService
                    ->sendSms('Dear ' .$firstName. ', welcome to Sandra Hospital, this is your Hospital Card Number '.'('.$notifiable->card_no.') courtesy of our Hospital Management System', $notifiable->phone, 'SandraHosp', 2);
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
}
