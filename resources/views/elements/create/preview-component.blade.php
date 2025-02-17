<h4 class="my-4 font-weight-bolder  text-gradient bg-gradient-primary">{{__("Job listing preview")}}</h4>
@include('elements.listings.job-listing-box', ['job' => JobsHelper::parseJobDraftSessionData(session('jobRequest'), 'job')])

<h4 class="my-4 pt-1 font-weight-bolder  text-gradient bg-gradient-primary">{{__('Company page preview')}}</h4>
<div class="row mb-2 px-0 flex-column-reverse flex-md-row">
    <div class="col-12 col-md-3 mt-3 mt-md-0">
        @include('elements.listings.company-preview-box', [
                'company' => JobsHelper::parseJobDraftSessionData(session('jobRequest'), 'company'),
                 'job' => JobsHelper::parseJobDraftSessionData(session('jobRequest'),
                 'job'
                 )])
    </div>
    <div class="col-12 col-md-9">
        <div class="">
            <div class="pl-0 pl-md-2">
                <h4 class="font-weight-bold">{{__('About')}}</h4>
                <div class="text-break">
                    {!! Purifier::clean(JobsHelper::parseJobDraftSessionData(session('jobRequest'), 'company')->description) !!}
                </div>
            </div>
        </div>
    </div>
</div>
