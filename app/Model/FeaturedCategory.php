<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FeaturedCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
    ];

    protected $table = 'featured_categories';

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
    public function category()
    {
        return $this->hasOne('App\Model\JobCategory', 'id', 'category_id');
    }
}
