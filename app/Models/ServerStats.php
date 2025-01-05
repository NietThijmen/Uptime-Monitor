<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerStats extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'server_id',
        'total_cpu',
        'used_cpu',
        'total_memory',
        'used_memory',
        'disks',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    protected function casts(): array
    {
        return [
            'disks' => 'array',
        ];
    }
}
