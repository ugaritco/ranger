<?php

namespace Ugarit\Ranger\Components;

use Ugarit\Ranger\Validation\Rule;

class Validator
{
    /**
     * @param  array<string, list<Rule>>  $rules
     */
    public function __construct(
        public readonly array $rules,
    ) {
        //
    }
}
