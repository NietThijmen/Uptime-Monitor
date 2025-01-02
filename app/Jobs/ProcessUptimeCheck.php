<?php

namespace App\Jobs;

use App\Models\Incident;
use App\Models\Site;
use App\Models\SiteStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessUptimeCheck implements ShouldQueue
{
    use Queueable;

    public $timeout = 120;

    public Site $site;

    /**
     * Create a new job instance.
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start = microtime(true);
        $up = true;
        $status = 500;
        $response_time = 0;

        try {
            $status = \Http::head($this->site->base_url)->status();
            $up = $status === 200;
            $response_time = microtime(true) - $start;

        } catch (\Throwable $e) {
            $up = false;
        }

        if (! $up) {
            // check if the previous check was successful
            if ($this->site->statusses->last()->up) {
                // create an incident
                Incident::create([
                    'site_id' => $this->site->id,
                    'title' => 'Site is down',
                    'description' => 'Site went down at '.Carbon::now()->toDateTimeString(),
                ]);
            }
        }

        SiteStatus::create([
            'up' => $up,
            'status' => $status,
            'response_time' => $response_time,
            'site_id' => $this->site->id,
        ]);
    }
}
