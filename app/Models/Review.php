<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property int $gig_id
 * @property int $rating
 * @property string|null $comment
 * @property bool $is_flagged
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Gig $gig
 * @property-read \App\Models\User $user
 */
class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'gig_id',
        'rating',
        'comment',
        'is_flagged',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_flagged' => 'boolean',
        'rating'     => 'integer',
    ];

    /**
     * Define the structural relationship linking this review back to its parent gig offering.
     *
     * @return BelongsTo
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }

    /**
     * Define the relationship identifying the unique author profile behind this review entry.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}