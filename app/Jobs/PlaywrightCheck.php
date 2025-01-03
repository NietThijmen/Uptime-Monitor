<?php

namespace App\Jobs;

use App\Models\Incident;
use App\Models\SitePlaywright;
use App\Models\SitePlaywrightRuns;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Ramsey\Uuid\Uuid;

class PlaywrightCheck implements ShouldQueue
{
    use Queueable;

    public SitePlaywright $playwright;
    public int $batch;
    /**
     * Create a new job instance.
     */
    public function __construct(SitePlaywright $playwright, int $batch)
    {
        $this->playwright = $playwright;
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // the playwright folder is located in the root of the project /playwright
        $playwrightFolder = base_path('playwright');

        $playwrightScript = $this->playwright->script;
        // place the script into $playwrightFolder/tests/${site_id}/${playwright_id}.spec.js
        $playwrightScriptPath = $playwrightFolder . '/tests/' . $this->playwright->site_id . '_' . $this->playwright->id . '.spec.js';
        file_put_contents($playwrightScriptPath, $playwrightScript);

        // run the playwright test
        $output = shell_exec('cd ' . $playwrightFolder . ' && npx playwright test tests/' . $this->playwright->site_id . '_' . $this->playwright->id . '.spec.js --quiet --reporter=list');
        $output = trim($output);

        unlink($playwrightScriptPath); // remove the playwright script

        \Log::info($output);

        $passes = true;
        $failedReason = null;

        if (str_contains($output, 'failed')) {
            $passes = false;
            $failedReason = $output;
        }

        SitePlaywrightRuns::create([
            'site_playwright_id' => $this->playwright->id,
            'batch' => $this->batch,
            'passes' => $passes,
            'failed_reason' => $passes ? null : $failedReason,
        ]);

        if(!$passes) {
            Incident::create([
                'site_id' => $this->playwright->site_id,
                'title' => 'Playwright test failed',
                'description' => "A playwright test failed for playwright id {$this->playwright->id} on site id {$this->playwright->site_id}\n\n{$failedReason}",
            ]);
        }


    }
}
