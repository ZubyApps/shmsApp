<?php

declare(strict_types = 1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChurchPlusSmsService
{
    private $baseUrl;
    private $tenantId;

    public function __construct(private readonly HelperService $helperService)
    {
        $this->baseUrl = config('broadcasting.connections.church_plus.base_url');
        $this->tenantId = config('broadcasting.connections.church_plus.tenant_id');
    }

    public function sendSms($message, $recipients, $subject, $gateway) 
    {
        if (!$this->helperService->nccTextTime()){ 
            info('Not sent', ['reason' => "it's not NCC text time"]);
            return false;
        }
        
        if ($recipients == '00000000000'){
            info('Not sent', ['reason' => 'no phone number for recipient']);
            return false;
        }

        $completeUrl = $this->baseUrl.'recipients='.$recipients.'&message='.$message.'&subject='.$subject.'&cid='.$this->tenantId.'&gateway='.$gateway;

        $response = Http::connectTimeout(10)->timeout(10)->post($completeUrl);
        return $response->getBody()->getContents();

    }
}
