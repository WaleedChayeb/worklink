<div class="card-plain card-blog col-xl-4 col-md-4 col-xs-12 col-sm-12 p-0">
    <div class="card-body">
        <div class="image-container pl-0">
            <a href="{{ route('blog.post.get', [$post->slug]) }}">
                <img alt="{{ $post->title }}" src="{{ GenericHelper::getStorageAssetLink($post->cover) }}"
                    class="img-fluid" />
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
            @php
                $user = optional($post->user);
                $userName = $user->name ?? '';
                $userLink = $userName ? GenericHelper::getUserFirstCompanyLink($userName) : '#';
            @endphp
            <p class="author mt-1">
                {{ __("by") }}
                <a href="{{ $userLink }}">
                    <b>{{ $userName }}</b>
                </a>,
                {{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}
            </p>
        </div>
    </div>
</div>