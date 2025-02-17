<div class="card-plain card-blog col-xl-4 col-md-4 col-xs-12 col-sm-12 p-0">
    <div class="card-body">
        <div class="image-container pl-0">
            <a href="{{ route('blog.post.get', [$post->slug]) }}">
                <img alt="{{ $post->title }}" src="{{ GenericHelper::getStorageAssetLink($post->cover) }}" class="img-fluid"/>
            </a>
        </div>
        <div class="">
            <h5 class="card-title mt-3 text-truncate text-wrap">
                <a href="{{ route('blog.post.get', [$post->slug]) }}" class="">{{ $post->title }}</a>
            </h5>
            <div class="card-description text-truncate text-wrap">
                <div class="text-truncate text-wrap">
                    {!! GenericHelper::getBlogArticleExcerpt($post->content, 200) !!}
                </div>
            </div>
            <p class="author mt-1">
                {{__("by")}} <a href="{{GenericHelper::getUserFirstCompanyLink($post->user)}}"><b>{{$post->user->name}}</b></a>, {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->diffForHumans() }}
            </p>
        </div>
    </div>
</div>
