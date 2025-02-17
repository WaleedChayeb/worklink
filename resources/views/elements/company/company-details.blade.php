<div class="card rounded-xl shadow-sm">
    <div class="card-body">
        <div class="mb-4 mt-3">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <img src="{{$company->logo}}" class="rounded-circle w-64">
            </div>

            <div class="d-flex justify-content-center align-items-center mb-2">
                <div class="h5 font-weight-bolder d-flex text-truncate mb-0">{{$company->name}}</div>
                @if($company->user->email_verified_at && $company->user->birthdate && ($company->user->verification && $company->user->verification->status == 'verified'))
                    <div data-toggle="tooltip" data-placement="bottom" title="{{__('Verified company')}}">
                        @include('elements.icon',['icon'=>'checkmark-circle-outline','centered'=> 'true' ,'classes'=>'ml-1 text-primary','variant'=>'medium'])
                    </div>
                @endif
                <div>
                    @if(Auth::check() && Auth::user()->id == $company->user_id)
                        <a href="{{route('my.companies.edit',['companyID' => $company->id])}}">
                            @include('elements.icon',['icon'=>'create-outline','centered'=>'true','classes'=>'ml-2', 'variant'=>'medium'])
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex  mb-3 text-truncate">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'location-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Location')}}</div>
                <div class="text-muted mt-1 text-truncate">{{$company->hq}}</div>
            </div>
        </div>

        <div class="d-flex  mb-3 text-truncate">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'globe-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Website')}}</div>
                <div class="text-muted mt-1 text-truncate"><a rel="nofollow" target="_blank" href="{{$company->website_url}}">{{$company->website_url}}</a></div>
            </div>
        </div>

        <div class="d-flex text-truncate ">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold"> @include('elements.icon',['icon'=>'briefcase-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Job posted')}}</div>
                <div class="text-muted mt-1 text-truncate">{{count($company->jobs)}} {{__('Jobs')}}</div>
            </div>
        </div>

    </div>
</div>
