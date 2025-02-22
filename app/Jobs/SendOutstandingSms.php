<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Services\ChurchPlusSmsService;
use App\Services\HelperService;
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
    public function handle(ChurchPlusSmsService $churchPlusSmsService, HelperService $helperService): void
    {
        $gateway = $helperService->nccTextTime() ? 1 : 2;

        $churchPlusSmsService->sendSms($this->message, $this->recipient, 'SandraHosp', $gateway);

        info('outstanding bill', ['sent to' => $this->reminder->visit->patient->first_name]);
    }
}
