<?php

namespace App\Model;

use App\Providers\GenericHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role_id',
        'email',
        'password',
        'bio',
        'birthdate',
        'location',
        'website',
        'avatar',
        'cover',
        'enable_2fa',
        'public_profile',
        'email_verified_at',
        'identity_verified_at',
        'auth_provider',
        'auth_provider_id',
        'auth_provider',
        'auth_provider_id',
        'billing_address',
        'first_name',
        'last_name',
        'city',
        'country',
        'state',
        'postcode',
        'settings',
        'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'public_profile' => 'boolean',
        'settings' => 'array',
    ];

    public function jobs()
    {
        return $this->hasMany('App\Model\JobListing', 'user_id');
    }

    public function companies()
    {
        return $this->hasMany('App\Model\Company', 'user_id');
    }

    /*
    * Virtual attributes
    */
    public function getAvatarAttribute($value)
    {
        return GenericHelperServiceProvider::getStorageAvatarPath($value);
    }

    public function getCoverAttribute($value)
    {
        if ($value) {
            if (getSetting('storage.driver') == 's3') {
                return 'https://'.getSetting('storage.aws_bucket_name').'.s3.'.getSetting('storage.aws_region').'.amazonaws.com/'.$value;
            } elseif (getSetting('storage.driver') == 'wasabi' || getSetting('storage.driver') == 'do_spaces') {
                return Storage::url($value);
            } else {
                return Storage::disk('public')->url($value);
            }
        } else {
            return asset(config('voyager.user.default_cover', '/img/default-cover.png'));
        }
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Model\Subscription');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany('App\Model\Subscription', 'user_id')->where('status', 'completed');
    }

    public function activeCanceledSubscriptions()
    {
        return $this->hasMany('App\Model\Subscription', 'user_id')->where('status', 'canceled')->where('expire_at', '<', Carbon::now());
    }

    public function transactions()
    {
        return $this->hasMany('App\Model\Transaction');
    }

    public function attachments()
    {
        return $this->hasMany('App\Model\Attachment');
    }

    public function verification()
    {
        return $this->hasOne('App\Model\UserVerify');
    }
}
