<?php

namespace Ugarit\Ranger\Collectors;

use Heritage\Broadcasting\Broadcasters\Broadcaster;
use Heritage\Broadcasting\BroadcastManager;
use Heritage\Support\Collection;
use Ugarit\Ranger\Components\BroadcastChannel;
use Ugarit\Ranger\Support\Ignores;

class BroadcastChannels extends Collector
{
    public function __construct(protected BroadcastManager $broadcastManager)
    {
        //
    }

    /**
     * @return Collection<string, BroadcastChannel>
     */
    public function collect(): Collection
    {
        $broadcaster = $this->broadcastManager->driver();

        // getChannels() is on the abstract broadcaster rather than the
        // contract, so a driver that only implements the contract has none.
        $channels = $broadcaster instanceof Broadcaster ? $broadcaster->getChannels() : [];

        return collect($channels)
            ->reject(fn ($channel) => is_string($channel) && Ignores::markedClass($channel))
            ->map(fn ($channel, $name) => new BroadcastChannel($name, $channel));
    }
}
