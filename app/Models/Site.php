<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'base_url',
        'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusses()
    {
        return $this->hasMany(SiteStatus::class);
    }

    public function lighthouses()
    {
        return $this->hasMany(SiteLighthouse::class);
    }

    public function playwrites()
    {
        return $this->hasMany(SitePlaywright::class);
    }

    public function csses()
    {
        return $this->hasMany(SiteCss::class);
    }

    public function uptime(Carbon $carbon): float
    {

        $up = $this->statusses->where('up', 1)->where('created_at', '>', $carbon)->count();
        $down = $this->statusses->where('up', 0)->where('created_at', '>', $carbon)->count();

        if ($down === 0) {
            return 100; // divide by zero is not allowed
        }

        return number_format($up / ($up + $down) * 100, 2);
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
