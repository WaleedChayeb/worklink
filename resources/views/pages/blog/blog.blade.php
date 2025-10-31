@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('المناقصات'))
@section('share_url', route('blog.get'))
@section('share_title',  __('المناقصات') . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

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

    <div class="page-header min-vh-75 d-flex align-items-center justify-content-center" style="background: url('{{asset('/img/header-jobs-update.svg')}}')">
        <div class="header-gradient-wrapper  d-flex w-100">

            <div class="container py-5">
                <h2>المناقصات</h2>
                <h6 class="p-0 m-0">{{__("Blog sub-header")}}</h6>
            </div>
        </div>
    </div>

    <div class="container my-2">
        <div class="">
            @if(isset($latestPost))
                @include('pages.blog.large-article',['post'=>$latestPost])
            @endif
        </div>
    </div>

    <div class="container my-2">
        <div class="row no-gutters">
            @if($articles->count() > 0)
                @foreach($articles as $article)
                    @include('pages.blog.box-article',['post'=>$article])
                @endforeach
            @endif

            @if($articles->count() === 0 && !isset($latestPost))
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="row w-75 min-vh-65">
                            <div class="col-auto d-flex align-items-center">
                                <img src="{{asset('/img/no-jobs-found.svg')}}" class="image-250">
                            </div>
                            <div class="col d-flex align-items-center pl-5">
                                <div class="">
                                    <div>
                                        <h4>{{__('No blog posts available yet.')}}</h4>
                                        <p>{{__('Our team is diligently crafting new content for you. Check back soon for updates!')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="d-flex flex-row-reverse mt-1 ">
            {{ $articles->links() }}
        </div>
    </div>
@stop
