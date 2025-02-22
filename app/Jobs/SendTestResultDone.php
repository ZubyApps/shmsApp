<?php

namespace App\Jobs;

use App\Models\Prescription;
use App\Services\ChurchPlusSmsService;
use App\Services\HelperService;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTestResultDone implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 12;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Prescription $prescription)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChurchPlusSmsService $churchPlusSmsService, HelperService $helperService): void
    {
        $firstName = $this->prescription->visit->patient->first_name;

        if ($this->recentlySent($this->prescription)) {
            info('Investigation not sent', ['recently sent (less than 30min ago)' => $firstName]);
            return;
        }
        
        $gateway = $helperService->nccTextTime() ? 1 : 2;

        $churchPlusSmsService
        ->sendSms('Dear ' .$firstName. ', your test result is ready. This notification is courtesy of our Hospital Management System. To opt out, visit reception', $this->prescription->visit->patient->phone, 'SandraHosp', $gateway);

        info('Investigation', ['sent to' => $firstName, 'gateway' => $gateway]);
    }

    private function recentlySent(Prescription $prescription): bool
    {
        $end = CarbonImmutable::now();
        $start = $end->subMinutes(30);
        $visit = $prescription->visit;

        return $visit->prescriptions
            ->where('result', '!=', null)
            ->whereBetween('result_date', [$start, $end])
            ->isNotEmpty();
    }
}
