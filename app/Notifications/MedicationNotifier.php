<?php

namespace App\Notifications;

use App\Services\ChurchPlusSmsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MedicationNotifier extends Notification
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable)
    {
        $firstName = $notifiable->visit->patient->first_name;
        
        Log::info('medications', ['sent to' => $firstName]);

        return $this->churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', pls be reminded of your medication by '. (new Carbon($notifiable->scheduled_time))->format('g:iA') . ' today, courtesy of our Hospital Management System', $notifiable->visit->patient->phone, 'SandraHosp');
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
