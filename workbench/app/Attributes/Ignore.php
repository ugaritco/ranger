<?php

namespace App\Attributes;

use Attribute;
use Ugarit\Surveyor\Contracts\Ignored;

#[Attribute(Attribute::TARGET_ALL)]
class Ignore implements Ignored
{
    //
}
