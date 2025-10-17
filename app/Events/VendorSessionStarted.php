<?php

namespace App\Events;

use App\Models\CollabSession;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorSessionStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $vendor;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(CollabSession $session, Vendor $vendor, User $user)
    {
        $this->session = $session;
        $this->vendor = $vendor;
        $this->user = $user;
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
            'session_id' => $this->session->id,
            'session_uuid' => $this->session->session_uuid,
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name,
            'started_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'started_at' => $this->session->started_at->toIso8601String(),
            'message' => $this->user->name . ' started a collaboration session',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'session.started';
    }
}
