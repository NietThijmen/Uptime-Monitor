<?php

namespace App\Console\Commands;

use App\Models\Incident;
use Illuminate\Console\Command;

class CleanupIncidents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-incidents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old incidents which are older than 30 days and have status closed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $incidents = Incident::where('status', 'closed')
            ->where('created_at', '<', now()->subDays(30))
            ->get();

        $incidents->each->delete();

        $this->info('Incidents cleaned up successfully');
    }
}
