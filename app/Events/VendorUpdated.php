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

class VendorUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vendor;
    public $session;
    public $user;
    public $field;
    public $value;

    /**
     * Create a new event instance.
     */
    public function __construct(Vendor $vendor, CollabSession $session, User $user, string $field, $value)
    {
        $this->vendor = $vendor;
        $this->session = $session;
        $this->user = $user;
        $this->field = $field;
        $this->value = $value;
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
            'field' => $this->field,
            'value' => $this->value,
            'updated_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'updated_at' => now()->toIso8601String(),
            'vendor_state' => $this->vendor->only([
                'name', 'email', 'phone', 'website', 'address', 'description', 'metadata'
            ]),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'vendor.updated';
    }
}
