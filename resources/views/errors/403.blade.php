@extends('layouts.generic')
@section('page_title', __('Forbidden'))

@section('content')
    <div class="container py-5">
        <div class=" d-flex justify-content-center align-items-center min-vh-65" >
            <div class="error-container d-flex flex-column">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{asset('/img/500 error.svg')}}">
                </div>
                <div class="text-center mt-4">
                    <h3 class="text-bold"> 403 | {{__($exception->getMessage() ?: 'Forbidden')}}</h3>
                    <div class="d-flex justify-content-center mt-2">
                        <a href="{{route('home')}}" class="right">{{__('Go home')}} »</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
