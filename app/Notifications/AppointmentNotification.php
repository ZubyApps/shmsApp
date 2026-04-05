<?php

namespace App\Notifications;

use App\Channels\CustomSmsChannel;
use App\Contracts\CustomSmsNotificationInterface;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification implements CustomSmsNotificationInterface, ShouldQueue
{
    use Queueable;

    public $tries = 5;
    
    public $timeout = 12;

    public function __construct(private readonly Appointment $appointment, private string $role)
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
        $patientFirstName = $this->appointment->patient->first_name;
        $doctorUsername = $this->appointment->doctor->username;
        $appointmentTime = Carbon::parse($this->appointment->date)->format('g:iA');

        // Logic based on who is receiving the SMS
        if ($this->role === 'doctor') {
            $name = $doctorUsername;
            $message = "Dear {$doctorUsername}, your appointment with {$patientFirstName} is today by {$appointmentTime}. Courtesy- Sandra Hospital Management System.";
        } else {
            $name = $patientFirstName;
            $message = "Dear {$patientFirstName}, your appointment with {$doctorUsername} is today by {$appointmentTime}. Courtesy- Sandra Hospital Management System.";
        }

        
        return [
                'name'          => $name,
                'to'            => $notifiable->phone ?? $notifiable->phone_number,
                'messageType'   => 'Appointment reminder',
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
