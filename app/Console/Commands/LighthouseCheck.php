<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LighthouseCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkers:lighthouse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the uptime of all sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sites = \App\Models\Site::all();
        foreach ($sites as $site) {
            \Log::info('[Lighthouse] Adding site to the queue: '.$site->name);
            \App\Jobs\LighthouseCheck::dispatch($site)->onQueue(
                'lighthouse'
            );
        }
    }
}
