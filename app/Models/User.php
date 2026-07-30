<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $nickname
 * @property string|null $faculty
 * @property string|null $bio
 * @property \Carbon\Carbon|null $seller_deactivated_at
 * @property string|null $resignation_reason
 * @property string|null $availability_type
 * @property string|null $availability_start
 * @property string|null $availability_end
 * @property string|null $social_whatsapp
 * @property string|null $social_instagram
 * @property string|null $social_facebook
 * @property string|null $social_telegram
 * @property string|null $social_email
 * @property bool $is_banned
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Gig[] $gigs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SellerPortfolio[] $portfolios
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Badge[] $badges
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Gig[] $wishlistedGigs
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nickname',  
        'faculty',   
        'bio',       
        'seller_deactivated_at',
        'resignation_reason',
        'availability_type',
        'availability_start',
        'availability_end',
        'social_whatsapp',
        'social_instagram',
        'social_facebook',
        'social_telegram',
        'social_email',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed',
        'seller_deactivated_at' => 'datetime',
        'is_banned'             => 'boolean',
    ];

    /**
     * Define the one-to-many relationship linking this user to their created service listings.
     *
     * @return HasMany
     */
    public function gigs(): HasMany
    {
        return $this->hasMany(Gig::class);
    }

    /**
     * Define the one-to-many relationship tracking system notifications issued to the user.
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Define the one-to-many relationship tracking freelance procurement requests placed by this user client.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Define the one-to-many relationship tracking graphical portfolio flyers uploaded by the provider.
     *
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(SellerPortfolio::class);
    }

    /**
     * Define the many-to-many relationship tracking reputation badges earned by the user profile.
     *
     * @return BelongsToMany
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class)->withTimestamps();
    }

    /**
     * Define the many-to-many relationship mapping marketplace listings saved to the user's wishlist index.
     *
     * @return BelongsToMany
     */
    public function wishlistedGigs(): BelongsToMany
    {
        return $this->belongsToMany(Gig::class, 'wishlists', 'user_id', 'gig_id')->withTimestamps();
    }
}