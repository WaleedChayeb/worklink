<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobSkill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'skill_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function job()
    {
        return $this->hasOne('App\Model\JobListing', 'id', 'job_id');
    }

    public function skill()
    {
        return $this->hasOne('App\Model\Skill', 'id', 'skill_id');
    }
}
