<?php

namespace App\Observers;

use App\Model\BlogPost;
use App\Providers\JobListingsServiceProvider;

class BlogPostObserver
{
    /**
     * Listen to the User updating event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function creating(BlogPost $blogPost)
    {
        $blogPost->slug = JobListingsServiceProvider::slugify($blogPost->title);
    }
}
