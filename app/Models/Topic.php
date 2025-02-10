<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get all of the categories for the Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
