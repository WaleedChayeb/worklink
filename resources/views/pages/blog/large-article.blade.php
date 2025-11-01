<div class="card-plain card-blog featured-article">
    <div class="card-body">
        <div class="row no-gutters">
            <div class="image-container col-12 col-md-6">
                <a href="{{ route('blog.post.get', [$post->slug]) }}">
                    <img alt="{{ $post->title }}" src="{{ GenericHelper::getStorageAssetLink($post->cover) }}" class="img-fluid"/>
                </a>
            </div>
            
            <div class="col-12 col-md-6 pl-0 pl-md-3 pt-2 pt-md-0">
                <h3 class="card-title text-truncate text-wrap">
                    <a class="text-truncate text-wrap" href="{{ route('blog.post.get', [$post->slug]) }}">{{ $post->title }}</a>
                </h3>
                <div class="card-description">
                    {!! GenericHelper::getBlogArticleExcerpt($post->content, 450) !!}
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
</div> 
 