<?php

namespace App\Console\Commands;

use App\Models\SitePlaywright;
use App\Models\SitePlaywrightRuns;
use Illuminate\Console\Command;

class PlaywrightCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkers:playwright';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run playwright checks on all sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playwrights = SitePlaywright::all();
        foreach ($playwrights as $playwright) {
            $batch = SitePlaywrightRuns::where('site_playwright_id', $playwright->id)->max('batch') + 1 ?? 1;

            \Log::info('[Playwright] Adding playwright to the queue: '.$playwright->id);
            \App\Jobs\PlaywrightCheck::dispatch($playwright, $batch)->onQueue(
                'playwright'
            );
        }
    }
}
