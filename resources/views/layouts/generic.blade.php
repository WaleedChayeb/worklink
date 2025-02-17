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
@include('template.header', ['hideOnDesktop' => false])
<!-- Dark overlay -->
<div class="overlay"></div>
<div class="flex-fill {{ (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'bg-base' : '') : (Cookie::get('app_theme') == 'dark' ? 'bg-base' : '')) }}">
    @include('template.side-menu',  ['hideOnDesktop' => true])
    @yield('content')
</div>
@if(getSetting('compliance.enable_age_verification_dialog'))
    @include('elements.site-entry-approval-box')
@endif
@include('template.footer')
@include('template.jsVars')
@include('template.jsAssets',['additionalJs' => [
               '/libs/wow.js/dist/wow.min.js',
               '/libs/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
               '/js/SideMenu.js',
               '/js/Newsletter.js'
]])
@include('elements.language-selector-box')

</body>
</html>
