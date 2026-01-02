<?php

namespace App\Providers;

use App\Events\BulkPrescriptionsCreated;
use App\Events\CapitationPaymentCreated;
use App\Events\CapitationPaymentDeleted;
use App\Events\PaymentCreated;
use App\Events\PaymentDestroyed;
use App\Events\PrescriptionBilled;
use App\Events\PrescriptionCreated;
use App\Events\PrescriptionDeleted;
use App\Events\PrescriptionTreated;
use App\Listeners\UpdateBulkPrescriptionCreated;
use App\Listeners\UpdateCapitationPayment;
use App\Listeners\UpdateCapitationPaymentCreated;
use App\Listeners\UpdateCapitationPaymentDeleted;
use App\Listeners\UpdateHmoTreated;
use App\Listeners\UpdateVisitBilling;
use Illuminate\Support\Facades\Event;
use App\Listeners\UpdateWalkInBilling;
use App\Listeners\UpdateMortuaryBilling;
use App\Listeners\UpdatePaymentCreated;
use App\Listeners\UpdatePaymentDestroyed;
use App\Listeners\UpdatePrescriptionBilled;
use App\Listeners\UpdatePrescriptionDeleted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
            PrescriptionCreated::class => [
            UpdateVisitBilling::class,
            UpdateWalkInBilling::class,
            UpdateMortuaryBilling::class,
        ],

        PrescriptionTreated::class => [
            UpdateHmoTreated::class
        ],

        PrescriptionBilled::class => [
            UpdatePrescriptionBilled::class
        ],

        PrescriptionDeleted::class => [
            UpdatePrescriptionDeleted::class
        ],

        PaymentCreated::class => [
            UpdatePaymentCreated::class,
        ],

        PaymentDestroyed::class => [
            UpdatePaymentDestroyed::class
        ],

        CapitationPaymentCreated::class => [
            UpdateCapitationPaymentCreated::class
        ],

        CapitationPaymentDeleted::class => [
            UpdateCapitationPaymentDeleted::class
        ],

        BulkPrescriptionsCreated::class => [
            UpdateBulkPrescriptionCreated::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
