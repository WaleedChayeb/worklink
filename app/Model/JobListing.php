<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobListing extends Model
{
    // TODO: Maybe add pre-approved (free jobs) and implement these ones
    public const PENDING_STATUS = 0;
    public const APPROVED_STATUS = 1;
    public const DISAPPROVED_STATUS = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'title',
        'slug',
        'company_id',
        'category_id',
        'application_link',
        'description',
        'salary',
        'location',
        'type', //TODO: Remove in the future
        'type_id',
    ];

    //TOD: Refactor this to jobListing / Conflicts with laravel queue jobs
    protected $table = 'jobs';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * Relations.
     */
    public function user()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id');
    }

    public function company()
    {
        return $this->hasOne('App\Model\Company', 'id', 'company_id');
    }

    public function category()
    {
        return $this->hasOne('App\Model\JobCategory', 'id', 'category_id');
    }

    public function type()
    {
        return $this->hasOne('App\Model\JobType', 'id', 'type_id');
    }

    public function applicants()
    {
        return $this->hasMany('App\Model\Applicant', 'job_id', 'id');
    }

    public function jobSkills()
    {
        return $this->hasMany('App\Model\JobSkill', 'job_id', 'id');
    }

    public function activeSubscription()
    {
        return $this->hasOne('App\Model\Subscription', 'job_id')
            ->where(function ($query) {
                $query->whereIn('status', [Subscription::ACTIVE_STATUS, Subscription::CANCELED_STATUS]);
            })
            ->whereDate('expires_at', '>', Carbon::now())
            ->orderBy('id', 'DESC');
    }

    public function plan()
    {
        // If job has not active sub/plan, return an empty relationship
        if (!$this->activeSubscription) {
            return new BelongsTo($this->newQuery(), $this, '', '', '');
        }

        return $this->activeSubscription->plan();
    }

    /**
     * Virtual attributes.
     */
    public function getJsDecodedSkillsAttribute()
    {
        $skills = [];
        foreach ($this->jobSkills as $skill) {
            $skills[] = $skill->skill->name;
        }

        return json_encode($skills);
    }

    /**
     * Virtual attributes.
     */
    public function getSkillsHumanFormatAttribute()
    {
        $skills = [];
        foreach ($this->jobSkills as $skill) {
            $skills[] = $skill->skill->name;
        }

        return implode(', ', $skills);
    }

    public function getDecodedSkillsAttribute()
    {
        $skills = [];
        foreach ($this->jobSkills as $skill) {
            $skills[] = (object) ['id' => $skill->id, 'name' => $skill->skill->name];
        }

        return $skills;
    }

    public function getCategoryNameAttribute()
    {
        return json_decode($this->category);
    }
}
