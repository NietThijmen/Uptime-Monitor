<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SitePlaywright extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'label',
        'script',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteCss::class, 'site_id');
    }
}
