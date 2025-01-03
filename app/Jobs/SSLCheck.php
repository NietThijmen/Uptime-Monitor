<?php

namespace App\Jobs;

use App\Models\Incident;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\SslCertificate\SslCertificate;

class SSLCheck implements ShouldQueue
{
    use Queueable;

    protected Site $site;

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
        $certificate = SslCertificate::createForHostName($this->site->base_url);
        $is_valid = $certificate->isValidUntil(
            now()->addDays(7)
        );

        if(!$is_valid) {
            Incident::create([
                'site_id' => $this->site->id,
                'title' => 'SSL Certificate is about to expire',
                'description' => 'The SSL Certificate for ' . $this->site->base_url . ' is about to expire in ' . $certificate->expirationDate()->diffForHumans(),
            ]);
        }
    }
}
