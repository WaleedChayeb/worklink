<div class="listing-wrapper border mb-4 py-3 pr-3 pr-md-0 pl-3 pl-md-0 shadow-sm {{isset($job->plan) && $job->plan->highlight_ad && !(isset($isOwner) && $isOwner == true) ? 'featured-listing-gradient' : ''}}">
    <div class="d-flex">
        <div class="w-100">
            <div class="row no-gutters">
                <div class="{{(isset($isOwner) && $isOwner == true) ? 'col-10' : 'col-sm-12'}} col-md-6 col-lg-7">
                    <div class="d-flex flex-row-no-rtl align-items-center">
                        <div class="d-flex align-items-center">
                            @if( ((isset($isOwner) && $isOwner == true)) || (isset($job->plan) && $job->plan->display_logo))
                                <div class="listing-company-logo d-flex align-items-center">
                                    <img src="{{$job->company->logo}}" class="rounded-circle w-64 shadow-sm" alt="{{$job->company->name}}">
                                </div>
                            @endif
                        </div>
                        <div class="text-truncate pl-3 pl-md-2 pl-lg-4 listing-details">
                            <p class="mb-1 text-truncate">{{$job->company->name}}</p>
                            <span class="h5 mb-1 font-weight-bold text-truncate"><a href="{{route('jobs.get',[ 'slug'=>$job->slug])}}" class="text-dark-r">{{$job->title}}</a></span>
                            @if(isset($isOwner) && $isOwner == true)
                                <div class="mt-3">

                                    @if($job->activeSubscription)
                                        <p class="mb-1">
                                        @if($job->activeSubscription->status === \App\Model\Subscription::CANCELED_STATUS)
                                            <div class="d-flex flex-column flex-md-row w-auto">
                                                <div>
                                                    <div class="badge badge-warning-outline text-xs badge-sm font-weight-bolder mb-1 mb-md-0">{{__('Canceled')}}</div>
                                                </div>
                                                <div class="d-flex align-items-center ml-md-2">
                                                    @include('elements.icon',['icon'=>'time-outline','variant'=>'small', 'classes' => 'mr-1'])

                                                    {{__('Expires on')}} {{$job->activeSubscription->expires_at->format('F d')}}
                                                </div>
                                                <a class="d-none d-md-flex ml-md-2 font-weight-bold text-dark-r align-items-center" href="{{route('jobs.packages',['jobID' => $job->id])}}">
                                                    @include('elements.icon',['icon'=>'refresh-outline','variant'=>'small', 'classes' => 'mr-1'])
                                                    {{__('Renew listing')}}
                                                </a>
                                            </div>
                                        @else
                                            <div class="d-flex flex-column flex-md-row w-auto">
                                                <div>
                                                    <span class="badge badge-success-outline text-xs badge-sm font-weight-bolder mb-1 mb-md-0">{{__('Active')}}</span>
                                                </div>
                                                <div class="d-flex align-items-center ml-md-2">
                                                    @include('elements.icon',['icon'=>'time-outline','variant'=>'small', 'classes' => 'mr-1'])
                                                    {{__('Renews on')}} {{$job->activeSubscription->expires_at->format('F d')}}
                                                </div>

                                            </div>
                                            @endif
                                            </p>
                                            @else
                                                <div class="d-flex flex-column flex-md-row w-auto">
                                                    <div>
                                                        <div class="badge badge-warning-outline text-xs badge-sm font-weight-bolder mb-1 mb-md-0">{{__('Inactive')}}</div>
                                                    </div>
                                                    <a class="d-none d-md-flex ml-md-2 font-weight-bold text-dark-r align-items-center" href="{{route('jobs.packages',['jobID' => $job->id])}}">
                                                        @include('elements.icon',['icon'=>'refresh-outline','variant'=>'small', 'classes' => 'mr-1'])
                                                        {{__('Renew listing')}}
                                                    </a>
                                                </div>

                                            @endif
                                </div>
                            @else
                                <p class="mb-1 text-truncate">{{isset($job->type->name) ? $job->type->name : __('No type')}} - {{$job->salary}}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="{{(isset($isOwner) && $isOwner == true) ? 'col-2' : 'col-sm-12'}}  col-md-6 col-lg-5 flex-row flex-md-row-reverse mt-1 mb-0 pr-0 pr-lg-4">
                    @if(isset($isOwner) && $isOwner == true)
                        <div class="dropdown {{Cookie::get('app_rtl') == 'rtl' ? 'dropright' : 'dropleft'}} d-flex flex-row flex-row-reverse">
                            <a class="btn btn-sm text-dark-r text-hover btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle px-2 py-1 m-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                @include('elements.icon',['icon'=>'ellipsis-horizontal-outline'])
                            </a>
                            <div class="dropdown-menu">
                                <!-- Dropdown menu links -->
                                <a class="dropdown-item" href="javascript:void(0)" onclick="shareOrCopyLink('')">{{__('Copy link')}}</a>
                                @if(Auth::check())
                                    @if(Auth::check()/* && Auth::user()->id == $post->user->id*/)
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{route('jobs.packages',['jobID' => $job->id])}}">
                                            {{
                                                $job->activeSubscription && $job->activeSubscription->status === \App\Model\Subscription::ACTIVE_STATUS
                                                ?
                                                __('Update plan')
                                                :
                                                __('Renew')
                                            }}
                                        </a>
                                        @if($job->activeSubscription && \App\Providers\SettingsServiceProvider::subscriptionCancelAvailable($job->activeSubscription))
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="SubscriptionsSettings.confirmSubCancelation({{$job->activeSubscription->id}})">{{__('Cancel subscription')}}</a>
                                        @endif
                                        <a class="dropdown-item" href="{{route('my.jobs.edit',['jobID' => $job->id])}}">{{__('Edit')}}</a>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="Job.confirmDelete({{$job->id}})">{{__('Delete')}}</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @else
                        <div class=" ">

                            <div class="listing-sub-details">
                                <div class="mb-1 d-flex flex-row flex-md-row-reverse mt-2 pl-3 pl-md-0">
                                    @if($job->decodedSkills)
                                        @foreach(collect($job->decodedSkills)->slice(0,3) as $i => $skill)
                                            <span class="badge badge-primary badge-primary-outline badge badge-pill mr-2 mr-md-0 ml-md-2 {{$i === 2 ? 'd-none d-lg-block' : ''}}">{{$skill->name}}</span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="mb-1 d-flex flex-row flex-md-row-reverse pl-3 pl-md-0">
                                    <div class="d-flex text-truncate mb-1 mt-1">
                                        <div class="d-flex align-items-center text-muted">
                                            @include('elements.icon',['icon'=>'globe-outline', 'centered' => false, 'classes' => 'mr-1'])
                                            <div class=" d-flex align-items-center text-truncate">
                                                <div class="text-truncate max-width-150">
                                                    {{$job->location}}
                                                </div>
                                            </div>
                                        </div>
{{--                                        <div class="ml-3 d-flex align-items-center text-muted">--}}
{{--                                            @include('elements.icon',['icon'=>'calendar-clear-outline', 'centered' => false, 'classes' => 'mr-1'])--}}
{{--                                            {{$job->created_at->diffForHumans()}}--}}
{{--                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                    @include('elements.settings.transaction-cancel-dialog')
                </div>
            </div>
        </div>
    </div>
</div>
