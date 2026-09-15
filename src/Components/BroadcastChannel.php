<?php

namespace Ugarit\Ranger\Components;

class BroadcastChannel
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $resolvesTo,
    ) {
        //
    }
}
