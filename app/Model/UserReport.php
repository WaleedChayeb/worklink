<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    public static $typesMap = [
        "I don't like this post",
        'Content is offensive or violates Terms of Service.',
        'Content contains stolen material (DMCA)',
        'Content is spam',
        'Report abuse',
    ];

    public static $statusMap = [
        'received',
        'seen',
        'solved',
        'false',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'job_id', 'company_id',  'type', 'details', 'status'];

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

    /*
     * Relationships
     */

    public function reporterUser()
    {
        return $this->belongsTo('App\Model\User', 'from_user_id');
    }

    public function reportedPost()
    {
        return $this->belongsTo('App\Model\JobListing', 'job_id');
    }

    public function reportedCompany()
    {
        return $this->belongsTo('App\Model\Company', 'company_id');
    }
}
