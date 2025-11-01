@extends('layouts.user-no-nav')

{{-- SEO, Schema & Share --}}
@section('page_title', __('My companies'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/pages/home.css',
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/Company.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')

    @include('elements.standard-dialog',[
        'dialogName' => 'company-delete-dialog',
        'title' => __('Delete company'),
        'content' => __('Are you sure you want to delete this company? All associated jobs will be deleted as well.'),
        'actionLabel' => __('Delete'),
        'actionFunction' => 'Company.delete();',
    ])


    <div class="container">

    @include('elements.page-navigation-header',[
        'title' => __('Companies'),
        'points' => [
            ['title' => __('Home'),    'route' => route('home')],
            ['title' => __('My companies')],
        ],
        'button' => [
            'title' => __('New'),
            'route' => route('my.companies.create')
        ]
    ])

    @include('elements.message-alert',['classes'=>'alert-box pt-0 pb-4'])

    <div class="row mb-4 mb-md-4">
        <div class="col-md-12 col-lg-12 mt-1 mt-md-0">
            <div class="">
                <div class="row">
                    @if(count($companies))
                        @foreach($companies as $company)
                            <div class="col-12 col-md-6 col-lg-3 mb-4">
                                @include('elements.listings.my-company-box',['company' => $company, 'isOwner' => true])
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <div class="row w-75 min-vh-65">
                                    <div class="col-auto d-flex align-items-center">
                                        <img src="{{asset('/img/no-jobs-found.svg')}}" class="image-250">
                                    </div>
                                    <div class="col d-flex align-items-center pl-5">
                                        <div class="">
                                            <div>
                                                <h4>{{__('You currently have no companies')}}.</h4>
                                                <p>{{__('You can always create a company in just a few seconds, by clicking the button on the top right side of this page')}}.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-row-reverse mt-1 ">
                {{ $companies->links() }}
            </div>
        </div>
    </div>

    </div>

@endsection
