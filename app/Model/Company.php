<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'hq',
        'website_url',
        'email',
        'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function getLogoAttribute()
    {
        if ($this->logoAttachment) {
            $decodedAttachment = json_decode($this->logoAttachment);

            return $decodedAttachment->path;
        }

        return asset('/img/default-avatar.jpg');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function user()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id');
    }

    public function jobs()
    {
        return $this->hasMany('App\Model\JobListing', 'company_id');
    }

    public function logoAttachment()
    {
        return $this->hasOne('App\Model\Attachment', 'company_id', 'id');
    }
}
