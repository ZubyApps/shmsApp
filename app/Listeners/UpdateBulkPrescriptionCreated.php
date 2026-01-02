<?php

namespace App\Listeners;

use App\DataObjects\SponsorCategoryDto;
use App\Events\BulkPrescriptionsCreated;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateBulkPrescriptionCreated
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly PaymentService $paymentService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BulkPrescriptionsCreated $event): void
    {
            /** @var Visit $visit */
        $visit = $event->visit->load('sponsor:id,category_name');
        
        $isNhis = $visit->sponsor->category_name === 'NHIS';
        $totalPayments = $visit->totalPayments();

        $dto = new SponsorCategoryDto(isNhis: $isNhis);
        
        // 1. Run Optimized Waterfall Logic
        $this->paymentService->applyPaymentsWaterfall($visit, $totalPayments, $dto);

        $visit->update([
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_paid'        => $visit->totalPayments(),
                'total_nhis_bill'   => $isNhis ? $visit->totalNhisBills() : 0, 
                'total_capitation'  => $visit->totalPrescriptionCapitations()
            ]);
    }
}
