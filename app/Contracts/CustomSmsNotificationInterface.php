<?php

declare(strict_types = 1);

namespace App\Contracts;

interface CustomSmsNotificationInterface
{
    public function toCustomSms($notifiable): array;
}