<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    public const DRAFT_STATUS = 1;
    public const PUBLISHED_STATUS = 2;

    /**
     * The attributes that are mass assignable.getUpdateItem.
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'cover', 'content', 'status', 'tags'];

    /**
     * Relations.
     */
    public function user()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id');
    }

    public function getDecodedTagsAttribute() {
        return explode(',', $this->tags);
    }
}
