<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SitePlaywrightRuns extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_playwright_id',
        'batch',
        'passes',
        'failed_reason',
    ];

    public function sitePlaywright(): BelongsTo
    {
        return $this->belongsTo(SitePlaywright::class);
    }

    protected function casts(): array
    {
        return [
            'passes' => 'boolean',
        ];
    }
}
