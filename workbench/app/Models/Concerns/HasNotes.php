<?php

namespace App\Models\Concerns;

use App\Attributes\Ignore;
use Heritage\Database\Eloquent\Casts\Attribute;

trait HasNotes
{
    public function publicNote(): Attribute
    {
        return Attribute::make(get: fn (): string => 'public');
    }

    #[Ignore]
    public function secretNote(): Attribute
    {
        return Attribute::make(get: fn (): string => 'secret');
    }
}
