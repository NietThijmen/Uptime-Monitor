<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SitePlaywrite extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'script_content',
        'run_at',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
