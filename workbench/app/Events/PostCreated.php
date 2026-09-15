<?php

namespace App\Events;

use App\Models\Post;
use Heritage\Broadcasting\Channel;
use Heritage\Broadcasting\InteractsWithSockets;
use Heritage\Broadcasting\PrivateChannel;
use Heritage\Contracts\Broadcasting\ShouldBroadcast;
use Heritage\Contracts\Events\ShouldDispatchAfterCommit;
use Heritage\Foundation\Events\Dispatchable;
use Heritage\Queue\SerializesModels;

class PostCreated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(protected Post $post)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('posts'),
            new PrivateChannel('users.'.$this->post->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'post' => $this->post->only(['id', 'title', 'created_at']),
            'author' => $this->post->user->only(['id', 'name']),
        ];
    }
}
