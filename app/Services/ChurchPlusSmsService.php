<?php

declare(strict_types = 1);

namespace App\Services;

use GuzzleHttp\Client;

class ChurchPlusSmsService
{
    private $baseUrl;
    private $tenantId;

    public function __construct()
    {
        $this->baseUrl = config('broadcasting.connections.church_plus.base_url');
        $this->tenantId = config('broadcasting.connections.church_plus.tenant_id');
    }

    public function sendSms($message, $recipients, $subject, $gateway) 
    {
        if ($recipients == '00000000000'){
            return;
        }

        $client = new Client(['request.options' => ['timeout' => 5, 'connection_timeout' => 5]]);
        
        $completeUrl = $this->baseUrl.'recipients='.$recipients.'&message='.$message.'&subject='.$subject.'&cid='.$this->tenantId.'&gateway='.$gateway;

        $response = $client->request('POST', $completeUrl);

        return $response->getBody()->getContents();

    }
}
