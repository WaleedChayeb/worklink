<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NewsletterEmail extends Model
{
    public $table = 'newsletter_emails';

    protected $fillable = [
        'email',
    ];
}
