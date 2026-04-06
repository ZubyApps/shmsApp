<?php

namespace App\Notifications;

use App\Channels\CustomSmsChannel;
use App\Contracts\CustomSmsNotificationInterface;
use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestResultNotification extends Notification implements CustomSmsNotificationInterface, ShouldQueue
{
    use Queueable;

    public $tries = 5;
    
    public $timeout = 12;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Prescription $prescription)
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

    public function toCustomSms($notifiable): array
    {
        $visit = $this->prescription?->visit;
        
        $walkIn = $this->prescription?->walkIn;

        $firstName = $visit->patient?->first_name ?? $walkIn->first_name;

        // $phone = $visit?->patient?->phone ?? $walkIn?->phone;
        
        $model = $visit ?? $walkIn; 
        
        $totalInvestigations = $model->prescriptions()->labInvestigations();

        $totalInvestigationsC = (clone $totalInvestigations)->count();

        $totalInvestigationsDone = (clone $totalInvestigations)->whereNotNull('result')->count();

        if ($totalInvestigationsDone <= 0 || $totalInvestigationsC <= 0){
            return [];
        }

        $message = 'Dear ' .$firstName. ' ' . $totalInvestigationsDone . ' out of ' . $totalInvestigationsC . ' of your test result(s) are ready. This notification is courtesy of Sandra Hospital Management System. To opt out, visit reception';

        return [
            'name'          => $firstName,
            'to'            => $notifiable->phone,
            'messageType'   => 'Test result',
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
