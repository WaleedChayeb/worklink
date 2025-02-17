@extends('layouts.user-no-nav')

{{-- SEO, Schema & Share --}}
@section('page_title', __('My jobs'))

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
            '/js/Job.js',
            '/js/pages/settings/subscriptions.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')

    @include('elements.standard-dialog',[
    'dialogName' => 'job-delete-dialog',
    'title' => __('Delete job listing'),
    'content' => __('Are you sure you want to delete this job listing?'),
    'actionLabel' => __('Delete'),
    'actionFunction' => 'Job.delete();',
])

    <div class="container">

        @include('elements.page-navigation-header',[
            'title' => __('Jobs'),
            'points' => [
                ['title' => __('Home'),    'route' => route('home')],
                ['title' => __('My jobs')],
            ],
            'button' => [
                'title' => __('New'),
                'route' => route('jobs.create')
            ]
        ])

        @include('elements.message-alert',['classes'=>'alert-box pt-0 pb-4'])

        <div class="row mb-4 mb-md-4">
            <div class="col-md-12 col-lg-12">
                @if(count($jobs))
                    <div class="">
                        @foreach($jobs as $job)
                            @include('elements.listings.job-listing-box',['job' => $job, 'isOwner' => true])
                        @endforeach
                    </div>
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <div class="row w-75 min-vh-65">
                                    <div class="col-auto d-flex align-items-center">
                                        <img src="{{asset('/img/no-jobs-found.svg')}}" class="image-250">
                                    </div>
                                    <div class="col d-flex align-items-center pl-5">
                                        <div class="">
                                            <div>
                                                <h4>{{__('You currently have no jobs.')}}</h4>
                                                <p>{{__('You can always create a job in just a few seconds, by clicking the button on the top right side of this page.')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="d-flex flex-row-reverse mt-3">
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
