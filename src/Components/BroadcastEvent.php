<?php

namespace Ugarit\Ranger\Components;

use Ugarit\Ranger\Concerns\HasFilePath;
use Ugarit\Surveyor\Types\Contracts\Type;

class BroadcastEvent
{
    use HasFilePath;

    public function __construct(
        public readonly string $name,
        public readonly string $className,
        public readonly Type $data,
    ) {
        //
    }
}
