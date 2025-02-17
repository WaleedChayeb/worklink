@extends('layouts.no-nav')

{{-- SEO, Schema & Share --}}
@section('page_title', __('Email unsubscribe'))
@section('share_url', route('newsletter.unsubscribe'))
@section('share_title',  __('Email unsubscribe') . ' - ' .getSetting('site.name'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    <meta name="robots" content="noindex">
@stop

@section('scripts')
    {!!
        Minify::javascript([
               '/js/Newsletter.js'

         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6">
                <div class="login d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-7 col-xl-6 mx-auto">
                                <a href="{{action('HomeController@index')}}">
                                    <img class="brand-logo pb-4" src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}">
                                </a>
                                @if (session('status'))
                                    <div class="alert alert-success text-white" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                    @csrf
                                    <div class="form-group ">
                                        <label for="email" class=" col-form-label ">{{ __('E-Mail Address') }}</label>
                                        <div class="">
                                            <input id="subscriber-email-field" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col">
                                            <button type="button" class="btn btn-grow btn-lg btn-primary bg-gradient-primary btn-block unsubscribe-button" onclick="NewsLetter.removeEmailSubscriber()">
                                                {{ __('Unsubscribe') }}
                                            </button>
                                        </div>
                                    </div>
                                <hr>
                                <div class=" text-center">
                                    <p class="mb-0">
                                        {{__("If you unsubscribe, you might miss news and deals from us.")}}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-flex bg-image p-0 m-0">
                <div class="d-flex m-0 p-0 bg-gradient-primary w-100 h-100">
                    <img src="{{asset('/img/pattern-lines.svg')}}" alt="pattern-lines" class="img-fluid opacity-6">
                </div>
            </div>
        </div>
    </div>
@endsection
