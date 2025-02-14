<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alternatif extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get the topic that owns the Alternatif
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AlternatifScore::class);
    }

    /**
     * Get all of the rankings for the Alternatif
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rankings(): HasMany
    {
        return $this->hasMany(Rangking::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($alternatif) {
            $alternatif->rankings()->delete(); // Soft delete semua ranking terkait
        });

        static::restoring(function ($alternatif) {
            $alternatif->rankings()->restore(); // Restore jika alternatif dikembalikan
        });
    }
}
