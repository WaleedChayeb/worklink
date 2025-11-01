<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Str;

class JobCategory extends Model
{
    public $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slogan',
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    //Str::slug

    /*
    * Virtual attributes
    */
    public function getSlugAttribute()
    {
        return Str::slug($this->name);
    }

    public function jobs()
    {
        return $this->hasMany('App\Model\JobListing', 'category_id');
    }
}
