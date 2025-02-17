@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Create a new job'))
@section('share_url', route('jobs.create'))
@section('share_title',  __('Create a new job') . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

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
        Minify::javascript([
            '/libs/@selectize/selectize/dist/js/selectize.min.js',
            '/libs/trix/dist/trix.umd.min.js',
            '/js/LoginModal.js',
            '/js/pages/create.js',
            '/js/Company.js',
            '/js/CreateHelper.js',
            '/js/JobCreate.js',
            '/libs/dropzone/dist/dropzone.js',
            '/js/FileUpload.js',
            '/js/suggestions.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')

    @include('uploaded-file-preview-template')
    @include('elements.create.steps-indicator',['step' => 1])
    @include('elements.suggest-description')

    <div class="container mt-4">

        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    <div class="form-wrapper">
                        @include('elements.message-alert', ['classes'=>'mb-4'])
                        <form action="{{route('jobs.save.draft')}}" method="POST" id="job-data-form">
                            @csrf
                            @include('elements.create.job-details-form',['showGuide' => true])
                            @include('elements.create.company-details-form',['showGuide' => true])
                            <div class="d-flex flex-row-reverse mt-4">
                            </div>
                            <div class="d-flex flex-row justify-content-between align-items-center mb-4">
                                <a href="javascript:void(0)" class="draft-clear-button">{{__("Clear draft")}}</a>
                                <button class="btn btn-primary btn-next mb-0">{{__('Continue')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if(Auth::check())
    @else
        @include('elements.modal-login')
    @endif

    @include('elements.standard-dialog',[
        'dialogName' => 'draft-clear-dialog',
        'title' => __('Clear draft'),
        'content' => __('Are you sure you want to clear your current draft data?.'),
        'actionLabel' => __('Clear'),
        'actionFunction' => 'CreateHelper.clearDraft();',
    ])

@stop
