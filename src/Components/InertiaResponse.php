<?php

namespace Ugarit\Ranger\Components;

use Ugarit\Surveyor\Types\Contracts\Type;

class InertiaResponse
{
    /**
     * @param  array<string, Type>  $data
     */
    public function __construct(
        public readonly string $component,
        public readonly array $data,
    ) {
        //
    }
}
