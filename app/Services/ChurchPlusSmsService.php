<?php

declare(strict_types = 1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\UrlHelper;

class ChurchPlusSmsService
{
    private $baseUrl;
    // private $endPoint;
    private $tenantId;

    public function __construct()
    {
        $this->baseUrl = config('broadcasting.connections.church_plus.base_url');
        $this->tenantId = config('broadcasting.connections.church_plus.tenant_id');
    }

    public function sendSms($message, $recipients, $subject) 
    {
        if ($recipients == '00000000000'){
            return;
        }

        $client = new Client();
        
        $completeUrl = $this->baseUrl.'recipients='.$recipients.'&message='.$message.'&subject='.$subject.'&cid='.$this->tenantId;

        // Log::info("", ["url1" => $completeUrl]);

        $response = $client->request('POST', $completeUrl, []);

        return $response->getBody()->getContents();

    }

    private function getRetryMiddleware(int $maxRetry)
    {
        return Middleware::retry(
            function (
                int $retries,
                RequestInterface $requestInterface,
                ?ResponseInterface $response = null,
                ?RuntimeException $e = null
            ) use ($maxRetry) {
                if ($retries >= $maxRetry) {
                    return false;
                }

                if ($response && in_array($response->getStatusCode(), [249, 429, 503])) {
                    return true;
                }

                if ($e instanceof ConnectionException){
                    return true;
                }
            }
        );
    }
}