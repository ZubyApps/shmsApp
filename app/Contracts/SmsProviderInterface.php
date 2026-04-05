<?php

declare(strict_types = 1);

namespace App\Contracts;

interface SmsProviderInterface
{
    /**
     * @return bool True if the message was accepted by the gateway
     */
    public function send(string $to, string $message): bool;

    /**
     * @return string|null A unique reference from the provider for tracking
     */
    public function getReference(): ?string;
}