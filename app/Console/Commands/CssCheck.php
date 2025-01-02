<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CssCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkers:css';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the css of all sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sites = \App\Models\Site::all();
        foreach ($sites as $site) {
            \Log::info('[Css] Adding site to the queue: '.$site->name);

            if ($site->csses->count() > 0) {
                $batch = $site->csses->max('batch') + 1;
            } else {
                $batch = 1;
            }

            \App\Jobs\CssCheck::dispatch($site, $batch);
        }
    }
}
