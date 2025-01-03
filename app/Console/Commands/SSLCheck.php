<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SSLCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkers:ssl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the SSL certificates of all sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sites = \App\Models\Site::all();
        foreach ($sites as $site) {
            \Log::info('[SSL] Adding site to the queue: '.$site->name);
            \App\Jobs\SSLCheck::dispatch($site);
        }
    }
}
