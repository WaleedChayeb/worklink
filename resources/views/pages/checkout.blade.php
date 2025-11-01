@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Checkout your order'))
@section('share_url', route('jobs.purchase'))
@section('share_title',  __('Checkout your order') . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@if(getSetting('security.captcha_driver') !== 'none' && !Auth::check())
    @section('meta')
        <x-captcha-js />
    @stop
@endif

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
            '/js/checkout.js',
            '/js/CreateHelper.js',
            '/js/JobCreate.js',
            '/js/pages/purchase.js',
            '/js/LoginModal.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')

    @include('uploaded-file-preview-template')

    <div class="home-header">
        @include('elements.create.steps-indicator',['step' => 4])
    </div>

    @if(Auth::check())
        <div class="checkout-info"
             data-type="one-month-subscription"
             data-amount="{{$selectedPlan->price}}"
             data-first-name="{{Auth::user()->first_name}}"
             data-last-name="{{Auth::user()->last_name}}"
             data-billing-address="{{Auth::user()->billing_address}}"
             data-country="{{Auth::user()->country}}"
             data-city="{{Auth::user()->city}}"
             data-state="{{Auth::user()->state}}"
             data-postcode="{{Auth::user()->postcode}}"
             data-name="{{$selectedPlan->name}}"
             data-job-id="{{$jobID ?? 'null'}}"
             data-plan-id="{{$selectedPlan->id}}"
        ></div>
    @endif

    <div class="container mt-2">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    <div class="form-wrapper my-4">

                        @include('elements.message-alert', ['classes' => 'mb-4'])
                        @include('elements.checkout-box')

                        <div class="d-flex flex-row justify-content-between align-items-center mb-4 mt-3 ">

                            <a href="{{route('jobs.packages', ($jobID ? ['jobID' => $jobID] : []))}}" class="">{{__("Back")}}</a>
                            <div class="d-flex justify-content-center align-items-center">
                                @if(Auth::check())
                                    @if(!$jobID && $selectedPlan->price)
                                        <div class="mr-3 d-flex">
                                            <a href="" class="pay-later">{{__("Pay later")}}</a>
                                            <div class="ml-2">|</div>
                                        </div>
                                    @endif
                                @else
                                    <div class="mr-3 d-flex">
                                        <a href="javascript:void(0);"
                                           data-toggle="modal"
                                           data-target="#login-dialog">{{__("Pay later")}}</a>
                                        <div class="ml-2">|</div>
                                    </div>
                                @endif

                                @if(Auth::check())
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-primary checkout-continue-btn mb-0">{{$selectedPlan->price ? __('Pay') : __('Save')}}
                                            <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
                                                <span class="sr-only">{{__('Loading...')}}</span>
                                            </div>
                                        </button>
                                    </div>
                                @else
                                    <button class="btn btn-primary mb-0"
                                            data-toggle="modal"
                                            data-target="#login-dialog"
                                    >{{__('Continue')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if(Auth::check())
    @else
        @include('elements.modal-login')
    @endif

@stop
