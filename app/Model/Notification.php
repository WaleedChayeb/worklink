<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public const SUBSCRIPTIONS_FILTER = 'subscriptions';

    public $notificationTypes = [
        self::SUBSCRIPTIONS_FILTER,
    ];

    public const NEW_SUBSCRIPTION = 'new-subscription';

    // Disable auto incrementing as we set the id manually (uuid)
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type', 'id', 'message', 'read',
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
        'id' => 'string',
    ];

    /*
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}
