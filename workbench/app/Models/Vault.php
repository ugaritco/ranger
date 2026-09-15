<?php

namespace App\Models;

use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\HasMany;

class Vault extends Model
{
    public function secrets(): HasMany
    {
        return $this->hasMany(SecretModel::class);
    }
}
