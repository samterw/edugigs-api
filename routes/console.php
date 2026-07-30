<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\GigSlot;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

/**
 * FYP DATA MAINTENANCE COMMAND (SOFT DELETE)
 * -------------------------------------------------------------------------
 * Logic: "Hide" unbooked time slots that have already passed.
 * These records remain in the DB with a deleted_at timestamp for audit.
 * Run manually via: php artisan slots:cleanup
 */
Artisan::command('slots:cleanup', function () {
    $this->info('Starting database maintenance: Archiving expired slots...');

    // Because SoftDeletes is enabled in the Model, this won't physically erase the row.
    $deletedCount = GigSlot::where('start_time', '<', now())
        ->where('is_booked', false)
        ->delete();

    if ($deletedCount > 0) {
        $this->info("Success: {$deletedCount} expired slots archived (Soft Deleted).");
    } else {
        $this->comment("Database is already clean. No expired slots found.");
    }
})->purpose('Soft-delete unbooked past slots to keep the marketplace clean while preserving data');

/**
 * AUTOMATED SCHEDULING
 * -------------------------------------------------------------------------
 * This ensures the cleanup runs hourly without human intervention.
 */
Schedule::command('slots:cleanup')->hourly();