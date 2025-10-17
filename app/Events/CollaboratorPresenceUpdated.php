<?php

namespace App\Events;

use App\Models\CollabSession;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CollaboratorPresenceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $vendor;
    public $user;
    public $presenceData;

    /**
     * Create a new event instance.
     */
    public function __construct(CollabSession $session, Vendor $vendor, User $user, array $presenceData)
    {
        $this->session = $session;
        $this->vendor = $vendor;
        $this->user = $user;
        $this->presenceData = $presenceData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('vendor.' . $this->vendor->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'vendor_id' => $this->vendor->id,
            'session_id' => $this->session->id,
            'presence' => $this->presenceData,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'presence.updated';
    }
}
