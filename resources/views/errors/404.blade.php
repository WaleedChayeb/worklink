@extends('layouts.generic')
@section('page_title', __('Page not found'))

@section('content')
    <div class="container py-5">
        <div class="error-404 d-flex justify-content-center align-items-center min-vh-65" >
            <div class="error-container d-flex flex-column">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{asset('/img/404 error.svg')}}">
                </div>
                <div class="text-center mt-4">
                    <h3 class="text-bold">{{__('Page Not found')}}</h3>
                    <p>{{__('The page your looking for is not available.')}}</p>
                    <div class="d-flex mt-2 justify-content-center">
                        <a href="{{route('home')}}" class="right">{{__('Go home')}} »</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
