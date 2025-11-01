<!doctype html>
<html class="h-100" dir="{{Cookie::get('app_rtl') == 'rtl' ? 'rtl' : 'ltr'}}" lang="{{session('locale')}}">
<head>
    @include('template.head',['additionalCss' => [
                '/libs/animate.css/animate.css',
                '/libs/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
                '/css/side-menu.css',
             ]])
</head>
<body class="d-flex flex-column">
@include('elements.impersonation-header')
@include('elements.global-announcement')
<!-- Dark overlay -->
<div class="overlay"></div>
<div class="flex-fill app-content bg-base">
    @include('template.header', ['hideOnDesktop' => true])

    @include('template.side-menu',  ['hideOnDesktop' => false])

    <div class="container-xl">
        <div class="row">
            <div class="col-12 col-md-12 px-0 content-wrapper">
                @yield('content')
            </div>
        </div>

    </div>

</div>

</div>
@if(getSetting('compliance.enable_age_verification_dialog'))
    @include('elements.site-entry-approval-box')
@endif
@include('template.footer-compact',['offset'=>true])
@include('template.jsVars')
@include('template.jsAssets',['additionalJs' => [
               '/libs/wow.js/dist/wow.min.js',
               '/libs/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
               '/js/SideMenu.js'
]])
@include('elements.language-selector-box')
</body>
</html>
