@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __($post->title))
@section('share_url', route('blog.post.get',['slug' => $post->slug]))
@section('share_title',  __($post->title) . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getStorageAssetLink($post->cover))

@section('meta')
    {!! SEOSchemaHelper::getBlogPostSchema($post) !!}
@stop

@section('styles')
    {!!
        Minify::stylesheet([
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
         ])->withFullUrl()
    !!}
@stop

@section('content')

    <div class="container pt-5 pb-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <div class="col-12 col-md-9">
                        <h2 class="title text-center text-bold">{{$post->title}}</h2>
                        <p class="pl-1 mb-3 text-center">{{__("by")}} <a href="{{GenericHelper::getUserFirstCompanyLink($post->user)}}"><b>{{$post->user->name}}</b></a>, {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->diffForHumans() }}</p>
                        <div class="d-flex justify-content-center">
                            <img alt="{{ $post->title }}" src="{{ GenericHelper::getStorageAssetLink($post->cover) }}" class="w-90"/>
                        </div>

                        <div class="text-truncate text-wrap mt-4">
                            {!! GenericHelper::getCleanedBlogContent($post->content) !!}
                        </div>
                    </div></div>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <div class="col-12 col-md-9">
                        <div class="tags-box  mb-4 rounded-xl">
                            <div class="">
                                <div class=" mb-0">
                                    <div class="h4 font-weight-bold {{count($post->decoded_tags) ? 'mb-4 mt-1' : 'mb-2'}}">{{__('Tags')}}</div>
                                </div>
                                <div class="pl-0">
                                    <div class="d-flex flex-row">
                                        @if(count($post->decoded_tags))
                                            <div class="row">
                                                <div class="col-12">
                                                    @foreach($post->decoded_tags as $tag)
                                                        <span class="badge badge-primary badge-primary-outline badge badge-pill mr-2 mb-2">{{$tag}}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <p class="mb-0">{{__('No tags available')}}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@stop
