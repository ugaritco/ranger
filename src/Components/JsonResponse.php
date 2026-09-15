<?php

namespace Ugarit\Ranger\Components;

class JsonResponse
{
    public function __construct(
        public readonly array $data,
    ) {
        //
    }
}
