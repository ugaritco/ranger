<?php

namespace App\Events;

use App\Models\User;
use Heritage\Broadcasting\InteractsWithSockets;
use Heritage\Broadcasting\PrivateChannel;
use Heritage\Contracts\Broadcasting\ShouldBroadcast;
use Heritage\Contracts\Events\ShouldDispatchAfterCommit;
use Heritage\Foundation\Events\Dispatchable;
use Heritage\Queue\SerializesModels;

class UserUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(protected User $user, protected array $changes = [])
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->user->only(['id', 'name', 'email']),
            'changes' => $this->changes,
        ];
    }
}
