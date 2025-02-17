@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Choose your job package'))
@section('share_url', route('jobs.packages'))
@section('share_title',  __('Choose your job package') . ' - ' .getSetting('site.name'))
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
            '/libs/dropzone/dist/dropzone.css',
            '/css/checkout.css'
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/libs/@selectize/selectize/dist/js/selectize.min.js',
            '/js/PricingPacks.js',
            '/js/CreateHelper.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')

    @include('uploaded-file-preview-template')
    <div class="home-header">

        @include('elements.create.steps-indicator',['step' => 3])
    </div>

    <div class="container mt-2">

        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    <div class="form-wrapper">
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card mb-4 shadow-sm rounded-xl">
                                    <div class="card-body">

                                        <div class="d-flex d-flex justify-content-between mb-3">
                                            <div><h5 class="font-weight-bolder  text-gradient bg-gradient-primary">{{__('Help Your Listing Stand Out')}}</h5></div>
                                            <div class="d-none d-md-flex align-items-center">
                                                <div class="text-muted mr-2 required-field-label text-right">{{__('OPTIONAL, BUT HIGHLY RECOMMENDED')}}</div>
                                            </div>
                                        </div>
                                        <p>{{__('Get your job in front of millions of targeted candidates all around the world.')}} {{__("Enhance your listing's exposure with our optional upgrades!")}}</p>

                                        <div class="row mb-2">

                                            @if($plans)
                                                @foreach($plans as $i => $plan)
                                                    @include('elements.pricing-pack-box', ['plan' => $plan, 'active' => $plan->default_plan ? true : false, 'increment' => $i])
                                                @endforeach
                                            @else
                                                <p>{{__('No active plans. Contact the admin.')}}</p>
                                            @endif

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-row {{!$jobID ? 'justify-content-between' : 'flex-row-reverse'}} align-items-center mb-4 ">
                            @if(!$jobID)
                                <a href="{{route('jobs.preview')}}" class="">{{__("Back")}}</a>
                            @endif
                            <a class="btn btn-primary btn-next mb-0" href="{{route('jobs.purchase', ($jobID ? ['jobID' => $jobID] : []))}}">{{__('Continue')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
