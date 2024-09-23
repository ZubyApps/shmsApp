<?php

namespace App\Notifications;

use App\Services\ChurchPlusSmsService;
use App\Services\HelperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InvestigationNotifier extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly ChurchPlusSmsService $churchPlusSmsService, private readonly HelperService $helperService)
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
        $gateway = $this->helperService->nccTextTime() ? 1 : 2;
        $firstName = $notifiable->visit->patient->first_name;

        Log::info('investigation', ['sent to' => $firstName]);

        return $this->churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', your test result is ready. This notification is courtesy of our Hospital Management System. To opt out, visit reception', $notifiable->visit->patient->phone, 'SandraHosp', $gateway);
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
