<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $buyer_id
 * @property int $gig_id
 * @property int|null $slot_id
 * @property string|null $notes
 * @property string $status
 * @property float|null $final_price
 * @property string|null $billplz_id
 * @property string|null $payment_status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Gig $gig
 * @property-read \App\Models\User $buyer
 * @property-read \App\Models\GigSlot|null $slot
 * @property-read \App\Models\Review|null $review
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'buyer_id', 
        'gig_id', 
        'slot_id', 
        'notes', 
        'status',
        'final_price',
        'billplz_id',      
        'payment_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'final_price' => 'float',
    ];

    /**
     * Define the relationship mapping this order back to its parent service offering.
     *
     * @return BelongsTo
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }

    /**
     * Define the relationship identifying the user client who initiated this procurement request.
     *
     * @return BelongsTo
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Define the relationship associating this order with its secured calendar allocation slot.
     *
     * @return BelongsTo
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(GigSlot::class, 'slot_id');
    }

    /**
     * Define the one-to-one relationship tracking the optional review evaluation left on this order.
     *
     * @return HasOne
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}