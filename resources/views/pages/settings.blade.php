@extends('layouts.user-no-nav')

@section('page_title',  __(":settingTab settings",['settingTab' => ucfirst(__($activeSettingsTab)) ]))

@section('scripts')
    {!!
        Minify::javascript(
            array_merge($additionalAssets['js'],[
                '/js/pages/settings/settings.js',
         ])
        )->withFullUrl()
    !!}
@stop

@section('styles')
    {!!
        Minify::stylesheet(
            array_merge($additionalAssets['css'],[
                '/css/pages/settings.css',
                ])
         )->withFullUrl()
    !!}
    <style>
        .selectize-control.multi .selectize-input>div.active {
            background:#{{getSetting('colors.theme_color_code')}};
        }
    </style>
@stop

@section('content')

    <div class="container">
        <div class="row mt-2 mt-md-5 mb-5">
            <div class="col-12 col-md-4 col-lg-3 mb-3 settings-menu  mr-0" >
                <div class="settings-menu-wrapper">
                    <div class="d-none d-md-block">
                        @include('elements.settings.settings-menu',['availableSettings' => $availableSettings])
                    </div>
                    <div class="setting-menu-mobile d-block d-md-none mt-3">
                        @include('elements.settings.settings-menu-mobile',['availableSettings' => $availableSettings])
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-lg-9 mb-5 mb-lg-0  settings-content mt-1 mt-md-0">
                <div class="card rounded-xl shadow-sm">
                    <div class="ml-3 mt-3 mt-md-0 ml-4 ml-md-3">
                        <h5 class="text-bold mt-0 mt-md-3 mb-0 {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{ ucfirst(__($activeSettingsTab))}}</h5>
                        <h6 class="mt-2 text-muted">{{__($currentSettingTab['heading'])}}</h6>
                    </div>
                    <hr class="{{in_array($activeSettingsTab, ['subscriptions','payments']) ? 'mb-0' : ''}} d-none d-md-block">
                    <div class="{{in_array($activeSettingsTab, ['subscriptions','payments']) ? '' : 'px-4 px-md-3'}}">
                        @include('elements.settings.settings-'.$activeSettingsTab)
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
