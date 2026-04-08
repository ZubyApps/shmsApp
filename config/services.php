<?php

use App\Services\Sms\ChurchPlusProvider;
use App\Services\Sms\TermiiProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'church_plus' => [
        'base_url' => env('CHURCH_PLUS_BASEURL'),
        'tenant_id' => env('CHURCH_PLUS_TENANTID'),
    ],

    'termii' => [
        'api_key'  => env('TERMII_API_KEY'),
        'base_url' => env('TERMII_BASE_URL'),
    ],

    'sms' => [
        'default' => env('SMS_PROVIDER_DEFAULT', 'church_plus'),
        'providers' => [
            'church_plus' => ChurchPlusProvider::class,
            'termii'     => TermiiProvider::class,
        ],
    ],



];
