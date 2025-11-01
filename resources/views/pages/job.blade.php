@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', $job->title)
@section('share_url', route('jobs.get', ['slug' => $slug]))
@section('share_title',  $job->title . ' - ' .getSetting('site.name'))
@section('share_description', SEOSchemaHelper::getDescriptionExcerpt($job->description))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    {!! SEOSchemaHelper::jobPosting($job) !!}
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
            '/js/pages/job.js',
            '/libs/sharer.js/sharer.min.js',
         ])->withFullUrl()
    !!}
@stop

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-12 col-md-11">
                    @include('elements.message-alert',['classes'=>'pt-0 pb-4'])
                    <div class="row px-0">
                        <div class="col-12 col-md-8 mb-4 mb-md-0">
                            <div class="d-flex align-items-center">
                                <h1 class="h3 font-weight-bolder mb-0 text-break">{{$job->title}}</h1>
                                <div>
                                    @if(Auth::check() && Auth::user()->id == $job->user_id)
                                        <a href="{{route('my.jobs.edit',['jobID' => $job->id])}}">
                                            @include('elements.icon',['icon'=>'create-outline','centered'=>'true','classes'=>'ml-3', 'variant'=>'medium'])
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="listing-container mt-4" id="job-listing-show-container">
                                <div class="overflow-hidden">
                                    {!! Purifier::clean($job->description) !!}
                                </div>
                                <div>

                                    <div class="card rounded-xl shadow-sm d-none d-md-block">
                                        <div class="card-body">

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="font-weight-bold">
                                                    {{__("Like the listing? Give it a try.")}}
                                                </div>
                                                <div class="d-flex justify-content-center align-items-center {{--my-3--}} application-link">
                                                    @include('elements.listings.application-link-box')
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            @include('elements.listings.company-preview-box', ['company' => $job->company])
                            @if(getSetting('custom-code-ads.sidebar_ad_spot'))
                                @include('elements.ads.job-page-sidebar-ad')
                            @endif
                            @include('elements.listings.job-details-box')
                            @include('elements.listings.job-share-box')
                            @include('elements.report-user-or-post',['reportStatuses' => GenericHelper::getReportTypes()])
                            <div class="d-flex flex-row-reverse mt-3">
                                <p class="text-sm mb-0"><a href="javascript:void(0)" onclick="showReportBox({{$job->id}}, {{$job->company->id}})">{{__('Report')}}</a></p>
                            </div>
                        </div>
                    </div>
                    @if($companyJobs->count())
                        <div class="row mt-4 mt-md-5  px-0">
                            <div class="col-12">
                                <h4 class="mb-4 font-weight-bold">{{__('Other listings')}}</h4>
                                @foreach($companyJobs as $job)
                                    @include('elements.listings.job-listing-box',['job' => $job])
                                @endforeach
                            </div>
                            <div class="w-100 d-flex flex-row-reverse mt-2 mr-4">
                                {{ $companyJobs->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
