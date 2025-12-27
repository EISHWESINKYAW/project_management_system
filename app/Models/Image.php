<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'url'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
