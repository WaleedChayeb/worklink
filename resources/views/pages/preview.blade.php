@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Preview your new job listing'))
@section('share_url', route('jobs.preview'))
@section('share_title',  __('Preview your new job listing') . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    <meta name="robots" content="noindex">
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

    @include('uploaded-file-preview-template')

    <div class="home-header">
        @include('elements.create.steps-indicator',['step' => 2])
    </div>

    <div class="container mt-2">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    <div class="form-wrapper">
                        @if(session('jobRequest'))
                            @include('elements.create.preview-component')
                        @else
                            <p class="mt-4">{{__("Job preview could not be generated. Please go back and make sure you've filled in the job & company fields properly.")}}</p>
                        @endif
                        <div class="d-flex flex-row justify-content-between align-items-center mb-4 mt-3">
                            <a href="{{route('jobs.create')}}" class="">{{__("Back")}}</a>
                            <a class="btn btn-primary btn-next mb-0" href="{{route('jobs.packages')}}">{{__('Continue')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@stop
