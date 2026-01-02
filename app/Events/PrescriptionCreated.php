<?php

namespace App\Events;

use App\Models\Visit;
use App\Models\WalkIn;
use App\Models\Resource;
use App\Models\Prescription;
use App\Models\MortuaryService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PrescriptionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Prescription $prescription,
        public ?Visit $visit = null,
        public ?WalkIn $walkIn = null,
        public ?MortuaryService $mortuary = null,
        public ?Resource $resource = null,
        public bool $isNhis
    )
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
