<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Plan extends Model
{
    public const PENDING_STATUS = 'pending';
    public const ACTIVE_STATUS = 'published';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'status', 'price', 'yearly_price', 'trial_days', 'order', 'default_plan',
        'display_logo',
        'highlight_ad',
        'main_page_pin',
        'share_on_slack',
        'share_on_newsletter',
        'share_on_partner_network',
        'share_on_social_media',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * Check if this plan have a payment made by the logged user.
     * @return bool
     */
    public function getHasPaymentForPlanAttribute()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $plan = $this;
            $paymentForPlan = Transaction::query()->where(['user_id' => $user->id, 'plan_id' => $plan->id])->first();

            return $paymentForPlan instanceof Transaction;
        }

        return false;
    }
}
