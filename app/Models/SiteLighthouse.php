<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteLighthouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'performance',
        'accessibility',
        'best_practices',
        'seo',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
