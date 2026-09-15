<?php

namespace App\Models;

use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\BelongsTo;

class ApiResponse extends Model
{
    public static $snakeAttributes = false;

    protected $fillable = [
        'responseData',
        'statusCode',
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
