<div class="card rounded-xl shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-row-reverse">
            <div class="dropdown {{Cookie::get('app_rtl') == 'rtl' ? 'dropright' : 'dropleft'}} mr-2">
                <a class="btn btn-sm text-dark-r text-hover btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle px-2 py-1 m-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    @include('elements.icon',['icon'=>'ellipsis-horizontal-outline'])
                </a>
                <div class="dropdown-menu">
                    <!-- Dropdown menu links -->
                    <a class="dropdown-item" href="javascript:void(0)" onclick="shareOrCopyLink('')">{{__('Copy link')}}</a>
                    @if(Auth::check())
                        @if(Auth::check()/* && Auth::user()->id == $post->user->id*/)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('my.companies.edit',['companyID' => $company->id])}}">{{__('Edit')}}</a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="Company.confirmDelete({{$company->id}});">{{__('Delete')}}</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-4 mt-3">

            <div class="d-flex justify-content-center align-items-center mb-3">
                <img src="{{$company->logo}}" class="rounded-circle w-64">
            </div>

            <div class="d-flex justify-content-center align-items-center mb-2 text-truncate">
                <span class="h5 font-weight-bolder text-truncate">{{$company->name}}</span>
            </div>
        </div>

        <div class="d-flex mb-3 text-truncate">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'location-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Location')}}</div>
                <div class="text-muted mt-1 text-truncate">{{$company->hq}}</div>
            </div>
        </div>

        <div class="d-flex mb-3 text-truncate">
            <div class="d-flex flex-column text-truncaten">
                <div class="d-flex flex-row-no-rtl font-weight-bold">@include('elements.icon',['icon'=>'globe-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Website')}}</div>
                <div class="text-muted mt-1 text-truncate"><a rel="nofollow" target="_blank" href="{{$company->website_url}}">{{$company->website_url}}</a></div>
            </div>
        </div>

        <div class="d-flex  text-truncate">
            <div class="d-flex flex-column text-truncate">
                <div class="d-flex flex-row-no-rtl font-weight-bold"> @include('elements.icon',['icon'=>'briefcase-outline','centered'=>'false','classes'=>'mr-2','variant'=>'medium']) {{__('Job posted')}}</div>
                <div class="text-muted mt-1 text-truncate">{{count($company->jobs)}} {{__('Jobs')}}</div>
            </div>
        </div>

        <hr>
        <div class="d-flex justify-content-center align-items-center">
            <a href="{{route('company.get',['slug' => $company->slug])}}">{{__('View company profile and all jobs')}}</a>
        </div>
    </div>
</div>
