<?php

namespace App\Notifications;

use App\Channels\CustomSmsChannel;
use App\Contracts\CustomSmsNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PatientRegistered extends Notification implements CustomSmsNotificationInterface, ShouldQueue
{
    use Queueable;

    public $tries = 5;
    
    public $timeout = 12;

    public function __construct()
    {
        //
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

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    public function toCustomSms($notifiable): array
    {
        $firstName = $notifiable->first_name;
        $cardNumber = $notifiable->card_no;
        $phoneNumber = $notifiable->phone;

        $message = 'Dear ' . $firstName . ', welcome to Sandra Hospital, your Hospital Card Number is (' . $cardNumber . ') courtesy: Sandra Hospital Management System';
        return [
            'name'          => $firstName,
            'to'            => $phoneNumber,
            'messageType'   => 'Card no.',
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
            // 'mail' => now()->addMinutes(5),
        ];
    }
}
