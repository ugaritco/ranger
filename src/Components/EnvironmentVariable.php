<?php

namespace Ugarit\Ranger\Components;

class EnvironmentVariable
{
    public function __construct(
        public readonly string $key,
        public readonly mixed $value,
    ) {
        //
    }
}
