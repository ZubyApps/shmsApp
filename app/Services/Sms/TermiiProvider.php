<?php

declare(strict_types = 1);

namespace App\Services\Sms;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TermiiProvider implements SmsProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    private ?string $lastReference = null;


    public function __construct()
    {
        $this->apiKey = config('services.termii.api_key');
        $this->baseUrl = config('services.termii.base_url');
    }

    public function send(string $to, string $message): bool
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/api/sms/send", [
            "api_key" => $this->apiKey,
            "to"      => $this->formatNumber($to),
            "from"    => 'SandraHosp',
            "sms"     => $message,
            "type"    => "plain",
            "channel" => "generic",
        ]);

        if ($response->successful()) {
            return true;
        }

        Log::error("Termii SMS Failed", [
            'to'       => $to,
            'response' => $response->body(),
            'status'   => $response->status()
        ]);

        return false;
    }

    protected function formatNumber(string $number): string
    {
            // 1. Remove any non-numeric characters (spaces, dashes, plus signs)
        $number = preg_replace('/[^0-9]/', '', $number);

        // 2. If it starts with '0', replace that first '0' with '234'
        if (str_starts_with($number, '0')) {
            return '234' . substr($number, 1);
        }

        // 3. If it starts with '234', it's already perfect
        if (str_starts_with($number, '234')) {
            return $number;
        }

        // 4. If it's a 10-digit number (like 803...), prepending 234
        if (strlen($number) === 10) {
            return '234' . $number;
        }

        // Default return if it doesn't match common patterns
        return $number;
    }

    public function getReference(): ?string
    {
        return $this->lastReference;
    }
}