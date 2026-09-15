<?php

namespace App\Events;

use App\Models\User;
use Heritage\Broadcasting\Channel;
use Heritage\Broadcasting\InteractsWithSockets;
use Heritage\Broadcasting\PrivateChannel;
use Heritage\Contracts\Broadcasting\ShouldBroadcast;
use Heritage\Contracts\Events\ShouldDispatchAfterCommit;
use Heritage\Foundation\Events\Dispatchable;
use Heritage\Queue\SerializesModels;

class UserCreated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(protected User $user)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->user->id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['id', 'name']),
        ];
    }
}
