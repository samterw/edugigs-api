<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupPastSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-past-slots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete slots that are in the past AND were never booked
        $deleted = \App\Models\GigSlot::where('start_time', '<', now())
            ->where('is_booked', false)
            ->delete();

        $this->info("Cleaned up {$deleted} expired slots.");
    }
}
