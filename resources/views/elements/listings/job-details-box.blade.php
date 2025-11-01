<div class="card rounded-xl mt-4 shadow-sm">
    <div class="card-body my-1">

        <div class="d-flex  mb-3">
            <div class="d-flex flex-column">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'calendar-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Posted on')}}</div>
                <div class="text-muted mt-1">{{$job->created_at->toFormattedDateString()}}</div>
            </div>
        </div>

        <div class="d-flex  mb-3">
            <div class="d-flex flex-column">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'people-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Applicants')}}</div>
                <div class="text-muted mt-1">{{$job->applicants_count}}</div>
            </div>
        </div>

        <div class="d-flex mb-3">
            <div class="d-flex flex-column">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'pricetags-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Skills')}}</div>
                <div class="mb-1 mt-1">
                    @if($job->decodedSkills)
                        @foreach($job->decodedSkills as $skill)
                            <span class="badge badge-primary badge-primary-outline badge badge-pill mr-2 mt-2">{{$skill->name}}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex  mb-3">
            <div class="d-flex flex-column">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'ticket-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Category')}}</div>
                <div class="text-muted mt-1">{{$job->categoryName->name}}</div>
            </div>
        </div>

        <div class="d-flex  mb-3">
            <div class="d-flex flex-column">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'trail-sign-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Type')}}</div>
                <div class="text-muted mt-1">{{$job->type->name}}</div>
            </div>
        </div>

        <div class="d-flex  mb-3 text-truncate">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'logo-usd','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Salary')}}</div>
                <div class="text-muted mt-1 text-truncate">{{$job->salary}}</div>
            </div>
        </div>

        <div class="d-flex text-truncate">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'location-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Location')}}</div>
                <div class="text-muted mt-1 text-truncate">{{$job->location}}</div>
            </div>
        </div>

    </div>
</div>
