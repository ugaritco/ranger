<?php

namespace Ugarit\Ranger\Support;

class Verb
{
    public readonly string $formSafe;

    public readonly string $actual;

    public function __construct(public string $verb)
    {
        $this->actual = strtolower($verb);
        $this->formSafe = in_array($this->actual, ['get', 'head', 'options']) ? 'get' : 'post';
    }
}
