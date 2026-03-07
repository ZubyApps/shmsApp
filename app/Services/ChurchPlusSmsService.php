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

    public function sendSms($message, $recipients, $subject) 
    {
        // if ($this->helperService->isAirtel($recipients)) {
        //     info('its airtel', ['phone => ' => $recipients]);
        //     return;
        // }
        // $gateway = $this->helperService->isAirtel($recipients) ? '2' : '1';
        $gateway = 1;

        // info('info', ['gateway' => $gateway, 'receipients' => $recipients]);
        
        $completeUrl = $this->baseUrl.'recipients='.$recipients.'&message='.$message.'&subject='.$subject.'&cid='.$this->tenantId.'&gateway='.$gateway;

        $response = Http::connectTimeout(10)->timeout(10)->post($completeUrl);
        return $response->getBody()->getContents();

    }
}
