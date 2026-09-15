<?php

namespace App\Events;

use Heritage\Broadcasting\Channel;
use Heritage\Contracts\Broadcasting\ShouldBroadcast;

class OrderShipped implements ShouldBroadcast
{
    public function __construct(protected string $name)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [new Channel('orders')];
    }

    public function broadcastAs(): string
    {
        return $this->name;
    }
}
