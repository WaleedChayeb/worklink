<footer class="{{$offset ? 'footer-compact' : ''}} {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'bg-white') : (Cookie::get('app_theme') == 'dark' ? '' : 'bg-white'))}}">
    <!-- A grey container -->
    <div class="greycontainer">
        <!-- A black container -->
        <div class="blackcontainer">
            <!-- Container to indent the content -->
            <div class="container">
                <div class="copyRightInfo d-flex flex-column-reverse flex-md-row d-md-flex justify-content-md-between py-3">
                    <div class="d-flex flex-row justify-content-center justify-content-md-start align-items-center my-2 my-md-0">
                        <p class="mb-0">&copy; {{date('Y')}} {{getSetting('site.name')}}. {{__('All rights reserved.')}}</p>
                    </div>
                    <div class="d-flex flex-row justify-content-center justify-content-md-start">
                        @include('elements.footer.dark-mode-switcher')
                        @include('elements.footer.direction-switcher')
                        @include('elements.footer.language-switcher')
                    </div>

                </div>
            </div>
        </div>
    </div>
</footer>
