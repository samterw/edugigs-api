<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\GigSlot
 *
 * @property int $id
 * @property int $gig_id
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon|null $end_time
 * @property bool $is_booked
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\Gig $gig
 */
class GigSlot extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gig_id',
        'start_time',
        'end_time',
        'is_booked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'is_booked'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Define the relationship linking this individual temporal slot back to its parent gig offering.
     *
     * @return BelongsTo
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }
}