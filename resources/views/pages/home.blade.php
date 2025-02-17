@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_description', getSetting('site.description'))
@section('share_url', route('home'))
@section('share_title', getSetting('site.name') . ' - ' . getSetting('site.slogan'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())
@section('meta')
    {!! SEOSchemaHelper::homepage() !!}
@stop

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/pages/home.css',
         ])->withFullUrl()
    !!}
@stop

@section('content')

    <div class="page-header min-vh-75 d-flex align-items-center justify-content-center" style="background: url('{{asset('/img/header-jobs-update.svg')}}')">
        <div class="header-gradient-wrapper  d-flex w-100">

            <div class="container py-5">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-12 col-md-7 py-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <h1 class="font-weight-bolder text-center d-none d-md-flex">{{__('Header Title')}}</h1>
                                <h1 class="font-weight-bolder text-center d-flex d-md-none">{{__('Header Title')}}</h1>
                            </div>
                        </div>
                        <p class="text-center my-3">{{__('Header Sub Title')}}</p>
                        <div class="d-flex justify-content-center align-items-center">
                            <a class="btn btn-primary btn-grow mr-2 mb-0" href="{{route('search.get')}}">{{__('Search a job')}}</a>
                            <a class="btn btn-outline-primary btn-grow  mb-0" href="{{ route('pages.get', ['slug' => 'about']) }}">{{ __('Learn more about us') }}</a>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(getSetting('site.show_featured_clients_area') && count(GenericHelper::getAvailableFeaturedClients()))
        @include('elements.featured-customers',[
            'classes' => getSetting('site.newsletter_homepage_position') !== 'top' ? 'mt-5 mb-1' : 'my-5'
        ])
    @endif

    <div class="container mb-4">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    @if(getSetting('site.newsletter_homepage_position') === 'top')
                        @include('elements.subscribe-box', ['classes' => (getSetting('site.show_featured_clients_area') && count(GenericHelper::getAvailableFeaturedClients()) ? 'mt-md-0 mb-5' : 'my-5') . ' pl-3'])
                    @endif
                    {{-- Pinned jobs --}}
                    @if(count($pinnedJobListings))
                        @include('elements.listings.featured-category-box', ['listings' => $pinnedJobListings, 'category' => (object)['name' => __('Featured Jobs'), 'id' => null], 'categoryName' => __('Featured Jobs')])
                    @endif
                    {{-- Featured categories --}}
                    @if(count($featuredCategoriesListings))
                        @foreach($featuredCategoriesListings as $categoryListings)
                            @include('elements.listings.featured-category-box', [
                                            'listings' => $categoryListings['listings'],
                                            'category' => isset($categoryListings['category']->category) ? $categoryListings['category']->category : $categoryListings['category'],
                                            'categoryName' => isset($categoryListings['category']->category->name) ? $categoryListings['category']->category->name : $categoryListings['category']->name
                                     ])
                        @endforeach
                    @else
                        @if( (getSetting('site.disable_featured_categories_on_homepage') && count($jobs) > 0))
                            @include('elements.listings.featured-category-box', [
                                    'listings' => $jobs,
                                    'category' => 'all',
                                    'categoryName' => __('All jobs'),
                             ])
                        @endif
                    @endif

                    @if(count($featuredCategoriesListings) === 0 && count($pinnedJobListings) === 0 || (getSetting('site.disable_featured_categories_on_homepage') && count($jobs) === 0))
                        <div class="{{getSetting('site.newsletter_homepage_position') === 'top' ? '' : 'mt-5'}}">
                            <div class="card mb-5 rounded-xl shadow-sm">
                                <div class="card-body">
                                    <div class="h4 font-weight-bold mb-2">{{__("No content available")}}</div>
                                    <p class="mb-0 text-muted">{{__("Looks like there are no jobs posted or featured categories yet. Be the first one to post a job, over the job create page, at")}} <a href="#">{{__("this link")}}</a>.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(getSetting('site.newsletter_homepage_position') === 'bottom')
                        @include('elements.subscribe-box', ['classes' => (count($featuredCategoriesListings) ? 'mb-5' : 'mb-5 mt-5') . ' pl-3' ])
                    @endif
                    @if(getSetting('site.show_popular_tags_box'))
                        @include('elements.tags-box', ['tags' => GenericHelper::getPopularTags(23)])
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
