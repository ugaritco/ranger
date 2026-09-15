<?php

namespace App\Models;

use App\Models\Concerns\HasNotes;
use Heritage\Database\Eloquent\Factories\HasFactory;
use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory, HasNotes;

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
