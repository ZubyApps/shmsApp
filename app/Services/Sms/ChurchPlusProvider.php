<?php

declare(strict_types = 1);

namespace App\Services\Sms;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChurchPlusProvider implements SmsProviderInterface
{
    private string $baseUrl;
    private string $tenantId;
    private ?string $lastReference = null;

    public function __construct()
    {
        $this->baseUrl = config('services.church_plus.base_url');
        $this->tenantId = config('services.church_plus.tenant_id');
    }

    public function send(string $to, string $message): bool
    {
        try {
            // Using withQuery handles URL encoding (e.g. spaces or & in messages)
            $response = Http::connectTimeout(10)
                ->timeout(10)
                ->post($this->baseUrl . http_build_query([
                    'recipients' => $to,
                    'message'    => $message,
                    'subject'    => 'SandraHosp',
                    'cid'        => $this->tenantId,
                    'gateway'    => 1,
                ]));

            $result = $response->body();
            // Store a reference for our logs (CHPLUS usually returns a string ID)
            $this->lastReference = "CH_" . substr($result, 0, 10);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("ChurchPlus SMS Failure: " . $e->getMessage());
            return false;
        }
    }

    public function getReference(): ?string
    {
        return $this->lastReference;
    }
}