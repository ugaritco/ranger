<?php

namespace App\Models;

use Heritage\Database\Eloquent\Attributes\Appends;
use Heritage\Database\Eloquent\Attributes\Hidden;
use Heritage\Database\Eloquent\Attributes\Visible;
use Heritage\Database\Eloquent\Model;

#[Visible(['id', 'email'])]
#[Hidden('password')]
#[Appends('full_name')]
class AttributeVisibility extends Model
{
    /**
     * @var list<string>
     */
    protected $visible = [
        'name',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'avatar_url',
    ];
}
