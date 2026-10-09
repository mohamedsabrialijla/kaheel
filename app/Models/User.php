<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use DB;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'nafath_verified',
        'birth_year',
        'birth_month',
        'birth_date',
        'yakeen_transaction_id',
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


    protected $with = ['favorites','latestRating','country','city'];

    protected $appends=['average_rating','account_age','is_follow','is_block','following_count','followers_count','posted_count'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'online' => 'boolean',
            'birth_year' => 'integer',
            'birth_month' => 'integer',
            'birth_date' => 'date',
            'nafath_verified' => 'boolean',
        ];
    }


   public function getLogoAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return url($value);
    }

    public function setLogoAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['logo'] = null;
            return;
        }

        $value = (string) $value;
        $base = rtrim(url('/'), '/');
        if (str_starts_with($value, $base . '/')) {
            $value = substr($value, strlen($base) + 1);
        }

        $this->attributes['logo'] = ltrim($value, '/');
    }



    public function getCreatedAtAttribute($value){
        return date('Y-m-d', strtotime($value));
    }

    public function getAccountAgeAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }


    public function favorites()
    {
        return $this->belongsToMany(Ad::class, 'favorites')->withTimestamps();
    }



    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'user_id', 'blocked_user_id');
    }

    public function blockedBy()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_user_id', 'user_id');
    }



    public function ratings()
    {
        return $this->hasMany(Rating::class, 'seller_id'); 
    }


    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 2);
    }


 

    public function latestRating()
    {
        return $this->hasOne(Rating::class, 'user_id')->latestOfMany();
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
 



    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }


    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }


 
    public function getIsFollowAttribute()
    {
            $guard = request()->expectsJson() ? 'sanctum' : null;
            $currentUserId = auth($guard)->id();

            if (!$currentUserId) {
                return false;
            }else

            return $this->followers()
                ->where('follower_id', $currentUserId)
                ->exists();
    }
 

    public function getIsBlockAttribute()
    {

        $guard = request()->expectsJson() ? 'sanctum' : null;
        $currentUserId = auth($guard)->id();

        if (!auth($guard)->check()) {
            return false;
        }

        if ($this->id == auth($guard)->id()) {
            return false;
        }

        return \DB::table('blocks')
            ->where('user_id', auth($guard)->id())
            ->where('blocked_user_id', $this->id)
            ->exists();
    }


    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function country()
    {
        return $this->belongsTo(ItemsCategories::class,'country_id');
    }


    public function city()
    {
        return $this->belongsTo(ItemsCategories::class,'city_id');
    }



    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }


    public function getFollowingCountAttribute()
    {
        return $this->following()->count();
    }


    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot('read')
                    ->withTimestamps();
    }



    public function getPostedCountAttribute()
    {
        return DB::table('ads')->where('user_id',$this->id)->count();
    }

    public function ads() {
        return $this->hasMany(Ad::class);
    }


    // public function averageRating()
    // {
    //     return $this->ratings()->avg('rating');
    // }



}
