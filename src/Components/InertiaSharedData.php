<?php

namespace Ugarit\Ranger\Components;

use Ugarit\Surveyor\Types\ArrayType;

class InertiaSharedData
{
    public function __construct(
        public readonly ArrayType $data,
        public readonly bool $withAllErrors = false,
    ) {
        //
    }
}
