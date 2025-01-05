<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        'name',
    ];



    public function stats(): HasMany
    {
        return $this->hasMany(ServerStats::class);
    }
}
