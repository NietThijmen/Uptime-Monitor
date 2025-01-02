<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'response_time',
        'status_code',
        'up',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    protected function casts()
    {
        return [
            'up' => 'boolean',
        ];
    }
}
