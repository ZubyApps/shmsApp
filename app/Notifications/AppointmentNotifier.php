<?php

namespace App\Notifications;

use App\Services\ChurchPlusSmsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentNotifier extends Notification
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
        return ['sms'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable)
    {
        $firstName = $notifiable->patient->first_name;
        $doctor    = $notifiable->doctor->username;
        
        Log::info('appointments', ['patient' => $firstName, 'doctor' => $doctor]);

        $this->churchPlusSmsService
        ->sendSms('Dear ' .$doctor. ', your appointment with ' . $firstName . ' is today by '. (new Carbon($notifiable->date))->format('g:iA') . '. Courtesy- Sandra Hospital Management System', $notifiable->doctor->phone_number, 'SandraH', 2);

        return $this->churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', your appointment with ' .$doctor. ' is today by '. (new Carbon($notifiable->date))->format('g:iA') . '. Courtesy- Sandra Hospital Management System', $notifiable->patient->phone, 'SandraH', 2);
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
