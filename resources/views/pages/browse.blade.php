@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Discover :category jobs', ['category' => $category->name]))
@section('share_url', route('browse.get',['slug' => $slug]))
@section('share_title',  __('Discover :category jobs', ['category' => $category->name]))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    {!! SEOSchemaHelper::searchResultsPage((object)['title' => getSetting('site.name'), 'description' => getSetting('site.description')]) !!}
@stop

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/pages/home.css',
            '/libs/@selectize/selectize/dist/css/selectize.css',
            '/libs/@selectize/selectize/dist/css/selectize.bootstrap4.css',
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/pages/search.js',
            '/libs/@selectize/selectize/dist/js/selectize.min.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')

    <div class="page-header min-vh-75 d-flex align-items-center justify-content-center" style="background: url('{{asset('/img/header-jobs-update.svg')}}')">
        <div class="header-gradient-wrapper d-flex w-100">
            <div class="container py-5">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-12 col-md-8 py-4">
                        <div class="d-flex justify-content-center align-items-center ">
                            <div>
                                <h2 class="font-weight-bolder text-center">
                                    @if($category->name === __("all_categories_label"))
                                        {{ __("all_categories_label")}}
                                    @else
                                        {{$category->name}} {{__('jobs')}}
                                        @endif
                                </h2>
                                <div class="d-flex justify-content-center">
                                    <div class="">
                                        <p class="mb-0">{{$category->description}}.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(getSetting('site.show_featured_clients_area') && count(GenericHelper::getAvailableFeaturedClients()))
        @include('elements.featured-customers',[
            'classes' => 'my-5'
        ])
    @else
        <div class="mt-5"></div>
    @endif

    <div class="container my-4">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    @if(!count($jobs))
                        <div class="row">
                            <div class="col-12">
                                @include('elements.listings.no-jobs-found')
                            </div>
                        </div>
                    @endif
                        @include('elements.subscribe-box', ['classes' => ' pl-3' ])

                        <div class="row {{count($jobs) ? 'mt-4 mt-md-5' : ''}} mb-3 mb-md-2 px-0">
                        @if(count($jobs))
                            <div class="col-12 pl-3 pl-md-5">
                                <div>
                                    @foreach($jobs as $job)
                                        @include('elements.listings.job-listing-box',['job' => $job])
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="col-12">
                                @include('elements.tags-box', ['tags' => GenericHelper::getPopularTags(20)])
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-row-reverse mt-1 mb-1">
                        {{ $jobs->onEachSide(1)->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
