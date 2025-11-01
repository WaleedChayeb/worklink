<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FeaturedClient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'client_name', 'client_logo', 'order', 'hyperlink'];

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

    public function getLogoAttribute()
    {
        $file = json_decode($this->client_logo);
        if ($file) {
            $file = Storage::disk(config('filesystems.defaultFilesystemDriver'))->url(str_replace('\\', '/', $file[0]->download_link));
        }

        return $file;
    }

    /*
     * Relationships
     */
    public function company()
    {
        return $this->hasOne('App\Model\Company', 'id', 'company_id');
    }
}
