<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    public $table = 'applicants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'signature',
    ];
}
