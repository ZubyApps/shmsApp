<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\DataObjects\FormLinkParams;
use App\Services\ChurchPlusSmsService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendFormLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 12;
    public $tries   = 3;
    
    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $link, private readonly FormLinkParams $params)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService): void
    {
        $recipientPhone = $this->params->phone;
        $gateway = 1;

        $message = 'Sandra Hospital Patient Registration Form link ' . $this->link . '. This link expires in 5 minutes';
        $churchPlusSmsService->sendSms($message, $recipientPhone, 'SandraHosp', $gateway);

        // $response == false ? '' : info('Link sent', ['recipient' => $recipientPhone]);
    }
}
