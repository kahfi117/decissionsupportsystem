<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Method extends Model
{
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, Method::class);
    }
}
