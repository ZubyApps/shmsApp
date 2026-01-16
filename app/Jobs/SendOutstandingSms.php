<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Services\ChurchPlusSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOutstandingSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 12;
    public $tries   = 5;
    public $backoff = [10, 30, 60, 120, 300];
    
    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Reminder $reminder, private readonly string $message, private readonly string $recipient)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService): void
    {
        $gateway = 1;

        $churchPlusSmsService->sendSms($this->message, $this->recipient, 'SandraHosp', $gateway);

        // $response == false ? '' : info('outstanding bill', ['sent to' => $this->reminder->visit->patient->first_name]);
    }
}
