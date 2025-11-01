@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Search for job listings'))
@section('share_url', route('search.get'))
@section('share_title',  __('Search for job listings') . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    <meta name="robots" content="noindex">
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
                                <h2 class="font-weight-bolder text-center">{{__('search_header')}}</h2>
                                <div class="d-flex justify-content-center">
                                    <div class="w-75 text-center">
                                        <p class="mb-0 mt-2">{{__('search_subheader')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12">
                    <div class="row mt-2 my-md-5 px-0">
                        <div class="col-12 col-md-3">

                            <div class="d-flex justify-content-between align-items-center d-block d-md-none mb-3 mb-md-0">
                                <h3>{{__("Results")}}</h3>
                                <div class="d-flex flex-row-reverse align-items-center">
                                <span class="h-pill h-pill-primary rounded search-back-button d-flex justify-content-center align-items-center ml-2" data-toggle="collapse" href="#colappsableFilters" role="button" aria-expanded="false" aria-controls="colappsableFilters">
                                     @include('elements.icon',['icon'=>'filter-outline','variant'=>'medium','centered'=>true])
                                </span>
                                </div>
                            </div>

                            <div class="mobile-search-filter collapse dont-collapse-sm mb-3 mb-md-0"  id="colappsableFilters">
                                @include('elements.search.search-filters')
                            </div>


                        </div>
                        <div class="col-12 col-md-9 {{count($jobs) ? 'pl-md-5' : ''}} mt-2 mt-md-0">
                            <div>
                                @if(count($jobs))
                                    @foreach($jobs as $job)
                                        @include('elements.listings.job-listing-box',['job' => $job])
                                    @endforeach
                                @else
                                    @include('elements.listings.no-jobs-found')
                                    @include('elements.tags-box', ['tags' => GenericHelper::getPopularTags(20)])
                                    @include('elements.subscribe-box')
                                @endif
                            </div>
                            <div class="d-flex flex-row-reverse mt-1 mb-1">
                                {{ $jobs->onEachSide(1)->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
