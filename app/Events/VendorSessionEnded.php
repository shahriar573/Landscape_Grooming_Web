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

class VendorSessionEnded implements ShouldBroadcast
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
            'vendor_id' => $this->vendor->id,
            'ended_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'ended_at' => $this->session->ended_at->toIso8601String(),
            'duration_minutes' => $this->session->started_at->diffInMinutes($this->session->ended_at),
            'message' => $this->user->name . ' ended the collaboration session',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'session.ended';
    }
}
