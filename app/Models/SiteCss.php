<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteCss extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'url',
        'active',
        'batch',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
