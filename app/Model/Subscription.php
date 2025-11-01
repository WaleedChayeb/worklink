<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public const PENDING_STATUS = 'pending';
    public const ACTIVE_STATUS = 'completed';
    public const SUSPENDED_STATUS = 'suspended';
    public const UPDATE_NEEDED_STATUS = 'update-needed';
    public const CANCELED_STATUS = 'canceled';
    public const EXPIRED_STATUS = 'expired';

    // Renewal failed
    public const FAILED_STATUS = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'job_id', 'plan_id', 'stripe_subscription_id', 'paypal_agreement_id', 'paypal_plan_id',
        'amount', 'expires_at', 'canceled_at', 'ccbill_subscription_id', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Casts the dates to carbon objects.
     */
    protected $dates = ['expires_at', 'updated_at', 'deleted_at'];

    /*
     * Relationships
     */

    public function subscriber()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Model\Transaction');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo('App\Model\Plan', 'plan_id');
    }

    public function job()
    {
        return $this->belongsTo('App\Model\JobListing', 'job_id');
    }
}
