<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Gig
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $category
 * @property float $price
 * @property float|null $max_price
 * @property bool $is_session
 * @property string $scheduling_type
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read float $average_rating
 * @property-read int $reviews_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GigSlot[] $slots
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $wishlistedBy
 */
class Gig extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'price',
        'max_price',
        'is_session',
        'scheduling_type',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'average_rating',
        'reviews_count'
    ];

    /**
     * Define the relationship linking this listing profile back to its provider account.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship tracking structural calendar slots allocated to this gig.
     *
     * @return HasMany
     */
    public function slots(): HasMany
    {
        return $this->hasMany(GigSlot::class);
    }

    /**
     * Define the relationship aggregating student reputation reviews left on this service.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Define the inverse relationship linking users who wishlisted this listing entry.
     *
     * @return BelongsToMany
     */
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists', 'gig_id', 'user_id')->withTimestamps();
    }

    /**
     * Accessor: Dynamically derive the computed aggregate review evaluation ranking.
     * Optimized to intercept N+1 relationship iteration paths.
     *
     * @return float
     */
    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }

        return round((float) $this->reviews()->avg('rating') ?: 0.0, 1);
    }

    /**
     * Accessor: Dynamically calculate total accumulated reviews left on this offering.
     * Optimized to check eager-loaded metadata indices first.
     *
     * @return int
     */
    public function getReviewsCountAttribute(): int
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }

        return $this->reviews()->count();
    }
}