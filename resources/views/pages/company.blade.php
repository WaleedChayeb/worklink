@extends('layouts.generic')

{{-- SEO, Schema & Share --}}
@section('page_title', $company->name)
@section('share_url', route('company.get', ['slug' => $slug]))
@section('share_title',  $company->name . ' - ' .getSetting('site.name'))
@section('share_description', SEOSchemaHelper::getDescriptionExcerpt($company->description))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('meta')
    {!! SEOSchemaHelper::organization($company) !!}
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

    <div class="container my-5">
        <div class="row">
            <div class="w-100 d-flex justify-content-center">

                <div class="col-12 col-md-11">
                    @include('elements.message-alert',['classes'=>'pt-2 px-0 pb-4'])
                    @include('elements.report-user-or-post',['reportStatuses' => GenericHelper::getReportTypes()])

                    <div class="row mb-2 px-0 flex-column-reverse flex-md-row">

                        <div class="col-12 col-md-3 mt-3 mt-md-0">
                            @include('elements.company.company-details', ['company' => $company])
                            <div class="d-flex mt-3">
                                <p class="text-sm mb-0"><a href="javascript:void(0)" onclick="showReportBox(null, {{$company->id}})">{{__('Report')}}</a></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="">
                                <div class="pl-0 pl-md-2">
                                    <h4 class="font-weight-bold">{{__('About')}}</h4>
                                    <div class="text-break company-description">
                                        {!! Purifier::clean($company->description) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($companyJobs->count())
                        <div class="row mt-4 mt-md-5  px-0">
                            <div class="col-12">
                                <h4 class="mb-4 font-weight-bold">{{__('Active listings')}}</h4>
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
