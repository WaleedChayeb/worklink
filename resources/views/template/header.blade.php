<nav class="{{$hideOnDesktop  ? 'd-flex d-md-none' : '' }} navbar {{Route::currentRouteName() == 'home' ? 'sticky-top' : ''}} navbar-expand-sm {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white') : (Cookie::get('app_theme') == 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white'))}} shadow ">
    <div class="container">
        <a class="navbar-brand d-flex justify-content-center align-items-center" href="{{ route('home') }}">
            <img src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}" class="d-inline-block align-top image-256" alt="{{__("Site logo")}}" title="{{getSetting('site.name')}}">
        </a>
        <button class="navbar-toggler" type="button" daria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <ul class="navbar-nav ml-auto">
                </ul>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto d-flex align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('search.get') }}">{{ __('Search') }}</a>
                </li>

                @if(getSetting('site.display_blog_page'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blog.get') }}">{{ __('Blog') }}</a>
                    </li>
                @endif

                <!-- Authentication Links -->
                @guest 
                    <a class="btn btn-primary btn-grow m-0 ml-3"  href="{{ route('login') }}">{{ __('Login') }}</a>  
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-right text-truncate d-flex align-items-center pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <div class="text-truncate max-width-150">{{ Auth::user()->name }}</div> <img src="{{Auth::user()->avatar}}" class="rounded-circle home-user-avatar ml-3">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown"> 
                            <a class="dropdown-item" href="{{route('search.get')}}">
                                {{__('Search')}}
                            </a>
                            <a class="dropdown-item" href="{{route('my.settings')}}">
                                {{__('Settings')}}
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
