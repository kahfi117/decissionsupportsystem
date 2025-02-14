<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Topic extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Get all of the categories for the Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get all of the alternatifs for the Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function alternatifs(): HasMany
    {
        return $this->hasMany(Alternatif::class);
    }

    /**
     * The methods that belong to the Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function methods(): BelongsToMany
    {
        return $this->belongsToMany(Method::class, 'topic_methods');
    }

    /**
     * Get all of the rankings for the Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function rankings(): HasManyThrough
    {
        return $this->hasManyThrough(Rangking::class, Alternatif::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
