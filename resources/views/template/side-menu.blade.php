<nav class="d-flex d-md-none d-lg-none sidebar {{$hideOnDesktop  ? 'sidebar-desktop-hidden' : '' }} shadow {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'light') : (Cookie::get('app_theme') == 'dark' ? '' : 'light'))}}">

    <div class="d-flex flex-grow flex-column">

        <div class="">
            <!-- close sidebar menu -->
            <div class="col-12 py-2 pr-2 d-flex flex-row-reverse d-md-none d-lg-none">
                <div class="dismiss d-flex justify-content-center align-items-center flex-row">
                    @include('elements.icon',['icon'=>'arrow-back','variant'=>'medium'])
                </div>
            </div>
            <div class="col-12 py-2 d-none d-md-flex dismiss-holder"></div>
            <div class="col-12 sidebar-wrapper">
                <div class="mb-4 d-flex flex-row-no-rtl px-3">
                    <div>
                        <a href="{{route('home')}}">
                            <img src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}" class="image-180" alt="{{__("Site logo")}}" title="{{getSetting('site.name')}}">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class=" flex-grow overflow-auto">


            <div class="side-menu  px-3">

                <ul class="nav flex-column user-side-menu">
                    <li class="nav-item ">
                        <a href="{{route('home')}}" class="h-pill h-pill-primary nav-link {{Route::currentRouteName() == 'home' ? 'active' : ''}} d-flex justify-content-between">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                    @include('elements.icon',['icon'=>'home-outline','variant'=>'small'])
                                </div>
                                <span class="d-block  text-truncate side-menu-label">{{__('Home')}}</span>
                            </div>
                        </a>
                    </li>


                    <li class="nav-item ">
                        <a href="{{route('search.get')}}" class="h-pill h-pill-primary nav-link d-flex justify-content-between {{Route::currentRouteName() == 'search.get' ? 'active' : ''}}">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                    @include('elements.icon',['icon'=>'search-outline','variant'=>'small'])
                                </div>
                                <span class="d-block  text-truncate side-menu-label">{{__('Search')}}</span>
                            </div>
                        </a>
                    </li>

                    @if(Auth::check())
                    <li class="nav-item ">
                        <a href="{{route('my.jobs.get')}}" class="h-pill h-pill-primary nav-link {{ in_array(Route::currentRouteName(), ['my.jobs.get', 'my.jobs.edit']) ? 'active' : ''}} d-flex justify-content-between">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                    @include('elements.icon',['icon'=>'briefcase-outline','variant'=>'small'])
                                </div>
                                <span class="d-block  text-truncate side-menu-label">{{__('My jobs')}}</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item ">
                        <a href="{{route('my.companies.get')}}" class="h-pill h-pill-primary nav-link {{in_array(Route::currentRouteName(), ['my.companies.get', 'my.companies.edit', 'my.companies.create'])  ? 'active' : ''}} d-flex justify-content-between">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                    @include('elements.icon',['icon'=>'business-outline','variant'=>'small'])
                                </div>
                                <span class="d-block  text-truncate side-menu-label">{{__('Companies')}}</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{route('my.settings')}}" class="nav-link {{Route::currentRouteName() == 'my.settings' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                    @include('elements.icon',['icon'=>'settings-outline','variant'=>'small'])
                                </div>
                                <span class="d-block  text-truncate side-menu-label">{{__('Settings')}}</span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{route('pages.get',['slug'=>'help'])}}"
                           class="nav-link h-pill h-pill-primary d-flex justify-content-between scroll-link d-flex align-items-center pointer-cursor"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                        >
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                    @include('elements.icon',['icon'=>'log-out-outline','variant'=>'small'])
                                </div>
                                <span class="d-block  text-truncate side-menu-label">{{__('Log out')}}</span>
                            </div>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>

                        @else
                        <li class="nav-item">
                            <a href="{{route('login')}}" class="nav-link h-pill h-pill-primary d-flex justify-content-between">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                                        @include('elements.icon',['icon'=>'log-in-outline','variant'=>'small'])
                                    </div>
                                    <span class="d-block  text-truncate side-menu-label">{{__('Login')}}</span>
                                </div>
                            </a>
                        </li>

                    @endif

                </ul>
            </div>
        </div>

@if(Auth::check())
        <div class="{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'bg-base-1' : 'bg-base') : (Cookie::get('app_theme') == 'dark' ? 'bg-base-1' : 'bg-base'))}} p-3 text-truncate image-250">
            <div class="d-flex align-items-center text-truncate">
                <div>
                    @if(Auth::check())
                        <img src="{{Auth::user()->avatar}}" class="rounded-circle user-avatar">
                    @else
                        <div class="avatar-placeholder">
                            @include('elements.icon',['icon'=>'person-circle','variant'=>'xlarge'])
                        </div>
                    @endif
                </div>
                <div class="ml-2 text-truncate">
                    {{Auth::user()->name}}
                </div>
            </div>
        </div>
    @endif
    </div>

</nav>
