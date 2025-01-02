<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UptimeCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkers:uptime';

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
            \Log::info('[Uptime] Adding site to the queue: '.$site->name);
            \App\Jobs\ProcessUptimeCheck::dispatch($site);
        }
    }
}
