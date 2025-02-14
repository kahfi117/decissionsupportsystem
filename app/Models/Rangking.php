<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rangking extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    /**
     * Get the alternatif that owns the Rangking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function alternatif(): BelongsTo
    {
        return $this->belongsTo(Alternatif::class);
    }

    /**
     * Get the method that owns the Rangking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function method(): BelongsTo
    {
        return $this->belongsTo(Method::class);
    }

}
