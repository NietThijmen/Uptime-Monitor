<?php

namespace App\Jobs;

use App\Helpers\MagicArray;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Lighthouse\Lighthouse;

class LighthouseCheck implements ShouldQueue
{
    use Queueable;

    //    public $queue = 'lighthouse'; // since this requires node modules to be installed and a browser we can't run this on all servers, so we need a separate queue

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

        if ($this->site->statusses->last()->up === false) {
            // Don't run lighthouse if the site is down (just a waste of resources)
            return;
        }

        $result = Lighthouse::url($this->site->base_url)
            ->run();

        $scores = new MagicArray($result->scores());

        // get latest lighthouse scores
        $old_scores = $this->site->lighthouses->last();

        // check if scores dropped more than 10 points
        if ($old_scores && $old_scores->performance - $scores->performance > 10) {
            // create an incident
            Incident::create([
                'site_id' => $this->site->id,
                'title' => 'Performance dropped',
                'description' => 'Performance dropped from '.$old_scores->performance.' to '.$scores->performance,
            ]);
        }

        $this->site->lighthouses()->create([
            'performance' => $scores->performance,
            'accessibility' => $scores->accessibility,
            'best_practices' => $scores->a['best-practices'], // FFS Spatie
            'seo' => $scores->seo,
            'site_id' => $this->site->id,
        ]);
    }
}
