@extends('layouts.user-no-nav')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Create company'))

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
            '/js/Company.js',
            '/js/suggestions.js',
             // (Route::currentRouteName() =='my.companies.create' ? '/js/posts/create.js' : '/js/pages/my/companies/edit.js'),
         ],(Route::currentRouteName() == 'my.companies.create' ? ['/js/pages/my/companies/create.js'] : ['/js/pages/my/companies/edit.js'])
         )
         )->withFullUrl()
    !!}
@stop

@section('content')
    @include('uploaded-file-preview-template')
    @include('elements.suggest-description')

    <div class="container">
        @include('elements.page-navigation-header',[
            'title' => (Route::currentRouteName() == 'my.companies.create' ? __('New') : __('Edit')) . ' ' . __('company'),
            'points' => [
                ['title' => __('Home'),    'route' => route('home')],
                ['title' => __('My companies'), 'route' => route('my.companies.get')],
                ['title' => __('Create')],
            ],

        ])

        <div class="row mb-5">
            <div class="col-md-12 col-lg-12 mb-5 mb-lg-0 mt-1 mt-md-0">
                <div class="card rounded-xl shadow-sm">
                    <div class="card-body">
                        <form action="{{route('my.companies.save', ['type' => Route::currentRouteName() == 'my.companies.create' ? 'create' : 'edit'])}}" method="POST" id="job-data-form">
                            @include('elements.create.company-details-form',['showGuide' => false])
                            <button class="btn btn-primary btn-block rounded mr-0 mb-0 mb-md-2" type="submit">{{__('Save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
