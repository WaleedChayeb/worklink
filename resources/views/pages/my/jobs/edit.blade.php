@extends('layouts.user-no-nav')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Edit job'))

@section('styles')
    <link rel="stylesheet" href="{{asset('/libs/trix/dist/trix.css')}}">
    {!!
        Minify::stylesheet([
            '/css/pages/home.css',
            '/libs/@selectize/selectize/dist/css/selectize.css',
            '/libs/@selectize/selectize/dist/css/selectize.bootstrap4.css',
            '/libs/dropzone/dist/dropzone.css',
            '/css/pages/create.css'
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript(
            array_merge(
            [
            '/libs/@selectize/selectize/dist/js/selectize.min.js',
            '/libs/trix/dist/trix.umd.min.js',
            '/js/CreateHelper.js',
            '/js/JobCreate.js',
            '/libs/dropzone/dist/dropzone.js',
            '/js/FileUpload.js',
            '/js/suggestions.js',
         ],(Route::currentRouteName() == 'my.companies.create' ? ['/js/pages/create.js'] : ['/js/pages/my/jobs/edit.js']) )

         )->withFullUrl()
    !!}
@stop

@section('content')

    @include('elements.suggest-description')

    <div class="container">
        @include('elements.page-navigation-header',[
            'title' => 'Edit job',
            'points' => [
                ['title' => __('Home'),    'route' => route('home')],
                ['title' => __('My jobs') , 'route' => route('my.jobs.get')],
                ['title' => __('Edit')],
            ]
        ])
        <div class="row mb-5">
            <div class="col-md-12 col-lg-12 mb-5 mb-lg-0 mt-1 mt-md-0">
                <div class="card rounded-xl shadow-sm">
                    <div class="card-body">
                        <form action="{{route('my.jobs.save')}}" method="POST" id="job-data-form">
                            @include('elements.create.job-details-form',['showGuide'=>false])
                            <button class="btn btn-primary btn-block rounded mr-0 mb-0 mb-md-2" type="submit">{{__('Save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
