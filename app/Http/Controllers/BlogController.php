<?php

namespace App\Http\Controllers;

use App\Model\BlogPost;
use App\Helpers\PaymentHelper;
use Illuminate\Http\Request;
use JavaScript;

class BlogController extends Controller
{
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(PaymentHelper $paymentHelper)
    {

    }

    // Get blog posts and paginate them
    public function index(Request $request) {
        if(!$request->query('page') || $request->query('page') == '1'){
            $data['latestPost'] = BlogPost::orderby('created_at', 'DESC')->whereIn('status', session('isAdmin') ? [0, 1] : [BlogPost::PUBLISHED_STATUS])
                ->limit(1)->first();
        }
        $data['articles'] = $this->getArticles((isset($data['latestPost']) ? $data['latestPost'] : false), 6);
        return view('pages.blog.blog', $data);
    }

    public function getBlogPost(Request $request) {

        $slug = $request->route('slug');
        $blogPost = BlogPost::where('slug', $slug)->first();
        if (!$blogPost) {
            abort(404);
        }

        return view('pages.blog.blog-post', ['post' => $blogPost]);
    }

    public function getArticles($latestPost, $pageNumber = 6) {
        // Getting the optional tag url param
        $excludedPosts = [];
        if($latestPost){
            $excludedPosts[] = $latestPost->id;
        }
        $articles = BlogPost::orderBy('created_at', 'desc')
            ->whereNotIn('id', $excludedPosts)
            ->whereIn('status', session('isAdmin') ? [0, 1] : [BlogPost::PUBLISHED_STATUS])
            ->paginate($pageNumber);
        return $articles;
    }
}
