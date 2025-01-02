<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SitePlaywriteStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_playwrite_id',
        'passes',
        'failed_reason',
    ];

    public function sitePlaywrite()
    {
        return $this->belongsTo(SitePlaywrite::class);
    }

    protected function casts()
    {
        return [
            'passes' => 'boolean',
        ];
    }
}
