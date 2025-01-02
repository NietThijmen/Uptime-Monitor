<?php

namespace App\Jobs;

use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CssCheck implements ShouldQueue
{
    use Queueable;

    public Site $site;

    public int $batch;

    /**
     * Create a new job instance.
     */
    public function __construct(Site $site, int $batch)
    {
        $this->site = $site;
        $this->batch = $batch;
    }

    private function get_css_urls_from_html(string $html): array
    {
        $matches = [];
        preg_match_all('/<link.*?href="(.*?)".*?>/i', $html, $matches);

        return $matches[1];
    }

    private function check_url_up(string $url): bool
    {
        try {
            $status = \Http::head($url)->status();

            return $status === 200;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $http_response = \Http::get($this->site->base_url)->body();
        $css_urls = $this->get_css_urls_from_html($http_response);

        foreach ($css_urls as $css_url) {
            $active = true;
            if (! $this->check_url_up($css_url)) {
                $active = false;
            }

            $this->site->csses()->create([
                'site_id' => $this->site->id,
                'url' => $css_url,
                'active' => $active,
                'batch' => $this->batch,
            ]);
        }
    }
}
