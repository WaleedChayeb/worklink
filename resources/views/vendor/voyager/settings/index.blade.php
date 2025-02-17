@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.__('voyager::generic.settings'))

@section('css')
    <link href="{{ asset('css/admin-settings.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/libs/@simonwep/pickr/dist/themes/nano.min.css')}}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-settings"></i> {{ __('voyager::generic.settings') }}
    </h1>
@stop

@section('content')


    <div class="page-content settings container-fluid">

        @if(isset($storageErrorMessage) && $storageErrorMessage !== false)
            <div class="storage-incorrect-bucket-config tab-additional-info">
                <div class="alert alert-warning alert-dismissible mb-1">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="info-label"><div class="icon voyager-info-circled"></div><strong>{{__("Warning!")}}</strong> {{__("The last storage settings you provided are invalid. Storage driver will reverted to local storage.")}}</div>
                    <div class="mt-05">{{__("Last error received:")}}</div>
                    <pre>{{$storageErrorMessage}}</pre>
                </div>
            </div>
        @endif

        @if(isset($emailsErrorMessage) && $emailsErrorMessage !== false)
            <div class="storage-incorrect-bucket-config tab-additional-info">
                <div class="alert alert-warning alert-dismissible mb-1">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="info-label"><div class="icon voyager-info-circled"></div><strong>{{__("Warning!")}}</strong> {{__("The email driver settings you provided are invalid. Email driver will reverted to logs.")}}</div>
                    <div class="mt-05">{{__("Last error received:")}}</div>
                    <pre>{{$emailsErrorMessage}}</pre>
                </div>
            </div>
        @endif

        <form action="{{ route('voyager.settings.update') }}" method="POST" enctype="multipart/form-data" class="save-settings-form">
            {{ method_field("PUT") }}
            {{ csrf_field() }}
            <input type="hidden" name="setting_tab" class="setting_tab" value="{{ $active }}" />
            <div class="panel">

                <div class="page-content settings container-fluid">
                    <ul class="nav nav-tabs">
                        <?php
                        $categoriesOrder = [
                            'Site',
                            'Storage',
                            'Media',
                            'Feed',
                            'Payments',
                            'Websockets',
                            'Emails',
                            'Slack',
                            'Social',
                            'Custom Code / Ads',
                            'Admin',
                            'Streams',
                            'Compliance',
                            'Security',
                            'AI',
                            'Colors',
                        ];
                        $categories = [];
                        foreach($categoriesOrder as $category){
                            if(isset( $settings[$category])){
                                $categories[$category] = $settings[$category];
                            }
                        }
                        $settings = $categories;
                        ?>
                        @foreach($settings as $group => $setting)
                            @if($group != 'Colors' && $group != 'License')
                                <li @if($group == $active) class="active" @endif>
                                    <a data-toggle="tab" class="settings-menu-{{lcfirst($group)}}" href="#{{ \Illuminate\Support\Str::slug($group) }}">{{ __($group) }}</a>
                                </li>
                            @endif
                        @endforeach
                        <li @if($group === $active && $active === 'Colors') class="active" @endif>
                            <a data-toggle="tab" href="#colors">{{__('Colors')}}</a>
                        </li>
                        <li @if($group === $active && $active === 'License') class="active" @endif>
                            <a data-toggle="tab" href="#license">{{__('License')}}</a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <div id="license" class="tab-pane fade in @if($group == $active && $active === 'License') active @endif">

                            <div class="kind-of-a-form-control">

                                <div class="panel-heading setting-row setting-theme_license" data-settingkey="license_product_license_key">
                                    <h3 class="panel-title">
                                        {{__('Product license code')}}
                                    </h3>
                                </div>

                                <div class="panel-body no-padding-left-right setting-row" data-settingkey="license_product_license_key">
                                    <div class="col-md-12 no-padding-left-right">
                                        <input type="text" class="form-control license_product_license_key" name="license_product_license_key" placeholder="Your license key" value="{{getSetting('license.product_license_key') ? getSetting('license.product_license_key') : ''}}">
                                    </div>
                                </div>
                                <div class="admin-setting-description">
                                    <code>
                                        {{__('Your product license key. Can be taken out of your')}} <a href="https://codecanyon.net/downloads">Codecanyon {{__('downloads')}}</a> {{__('page')}}.
                                    </code>
                                </div>

                                <div class="d-none">
                                    <select class="form-control group_select d-none" name="license_product_license_key_group">
                                        @foreach($groups as $group)
                                            <option value="License" selected></option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>


                        </div>

                        <div id="colors" class="tab-pane fade in @if($group == $active && $active === 'Colors') active @endif">
                            <div class="">
                                <div class="alert alert-info alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>

                                    <div class="info-label d-flex">
                                        <div class="icon voyager-info-circled"></div>
                                        <span class="ml-2">
                                            {{__('Few general notes about generating themes.')}}
                                        </span>
                                    </div>
                                    <ul class="mt-05">
                                        <li>{{__('The themes are generated on a remote server. Timings may vary but it might take between 20-40s for a run.')}}</li>
                                        <li>{{__('Regular license holders can generate 5 themes per day.')}}</li>
                                        <li>{{__('If')}} <code>{{__('zip')}}</code> {{__('extension is available on the server, the theme will be updated automatically.')}}</li>
                                        <li>{{__("If the extension is not available, you will need to upload the archive you'll be getting onto the following directory :")}} <code>public/css/theme</code>.</li>
                                        <li>{{__('When updating your site, remember to backup your')}} <code>public/css/theme</code> {{__('folder and restore it after the update.')}}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="kind-of-a-form-control">

                                <div class="panel-heading setting-row setting-theme_color_code" data-settingkey="theme_color_code">
                                    <h3 class="panel-title">
                                        {{__('Primary color code')}}
                                    </h3>
                                </div>

                                <div class="panel-body no-padding-left-right setting-row setting-theme_color_code" data-settingkey="theme_color_code">
                                    <div class="col-md-12 no-padding-left-right">
                                        <input type="text" class="form-control" name="theme_color_code" id="theme_color_code" value="#{{getSetting('colors.theme_color_code') ? getSetting('colors.theme_color_code') : 'cb0c9f'}}">
                                    </div>
                                </div>
                                <div class="admin-setting-description">
                                    <code>
                                        {{__('Theme primary color hex code. EG: #cb0c9f')}}
                                    </code>
                                </div>

                            </div>

                            <div class="row">

                                <div class="kind-of-a-form-control col-lg-6">

                                    <div class="panel-heading setting-row setting-theme_gradient_from" data-settingkey="theme_gradient_from">
                                        <h3 class="panel-title">
                                            {{__('Gradient color start from')}}
                                        </h3>
                                    </div>

                                    <div class="panel-body no-padding-left-right setting-row setting-theme_gradient_from" data-settingkey="theme_gradient_from">
                                        <div class="col-md-12 no-padding-left-right">
                                            <input type="text" class="form-control" name="theme_gradient_from" id="theme_gradient_from" value="#{{getSetting('colors.theme_gradient_from') ? getSetting('colors.theme_gradient_from') : 'cb0c9f'}}">
                                        </div>
                                    </div>
                                    <div class="admin-setting-description">
                                        <code>
                                            {{__("Theme's primary gradient - start from, color hex code. EG: #7928CA")}}
                                        </code>
                                    </div>

                                </div>

                                <div class="kind-of-a-form-control col-lg-6">

                                    <div class="panel-heading setting-row setting-theme_gradient_to" data-settingkey="theme_gradient_to">
                                        <h3 class="panel-title">
                                            {{__('Gradient color ends on')}}
                                        </h3>
                                    </div>

                                    <div class="panel-body no-padding-left-right setting-row setting-theme_gradient_to" data-settingkey="theme_gradient_to">
                                        <div class="col-md-12 no-padding-left-right">
                                            <input type="text" class="form-control" name="theme_gradient_to" id="theme_gradient_to" value="#{{getSetting('colors.theme_gradient_to') ? getSetting('colors.theme_gradient_to') : 'cb0c9f'}}">
                                        </div>
                                    </div>
                                    <div class="admin-setting-description">
                                        <code>
                                            {{__("Theme's primary gradient - ends on, color hex code. EG: #FF0080")}}
                                        </code>
                                    </div>

                                </div>


                                <div class="kind-of-a-form-control col-lg-12">

                                    <div class="panel-heading setting-row setting-theme_skip_rtl" data-settingkey="theme_skip_rtl">
                                        <h3 class="panel-title">
                                            {{__('Include RTL versions')}}
                                        </h3>
                                    </div>

                                    <div class="panel-body no-padding-left-right setting-row setting-theme_skip_rtl" data-settingkey="theme_skip_rtl">
                                        <div class="col-md-12 no-padding-left-right">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="theme_skip_rtl" value="">
                                                    {{__('Generate RTL Versions as well')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-setting-description">
                                        <code>
                                            {{__('Choose if RTL version of the theme should be generated or not. If enabled, theme generation time will increase.')}}
                                        </code>
                                    </div>

                                </div>

                            </div>
                        </div>

                        @foreach($settings as $group => $group_settings)
                            <div id="{{ \Illuminate\Support\Str::slug($group) }}" class="tab-pane fade in @if($group == $active) active @endif">

                                <div class="tab-additional-info">

                                    @if($group == 'Emails')

                                        <div class="emails-info">
                                            <div class="alert alert-info alert-dismissible mb-1">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <div class="info-label mb-0"><div class="icon voyager-info-circled"></div> {{__('You can use any SMTP you have access to or mailgun API. Full info can be found over')}} <a target="_blank" class="text-white" href="https://docs.qdev.tech/justjobs/documentation.html/#emails">{{__('the documentation')}}</a> .</div>
                                            </div>
                                        </div>

                                    @endif

                                </div>

                                @if($group == 'Security')
                                    <div class="">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="mb-4">Security settings</h4>

                                                <div class="tabbable-panel">
                                                    <div class="tabbable-line">
                                                        <ul class="nav nav-tabs ">
                                                            <li class="active">
                                                                <a href="#security-general" data-toggle="tab" onclick="Admin.securitySettingsSubTabSwitch('general')">
                                                                    General </a>
                                                            </li>
                                                            <li>
                                                                <a href="#security-captcha" data-toggle="tab" onclick="Admin.securitySettingsSubTabSwitch('captcha')">
                                                                    Captcha </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($group == 'Payments')

                                    <div class="tab-additional-info">

                                        <div class="payments-info">


                                            @if(!file_exists(storage_path('logs/cronjobs.log')))
                                                <div class="alert alert-info alert-dismissible mb-1 payments-info-crons">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="info-label"><div class="icon voyager-dollar"></div>{{__("The payment system requires cronjobs so you can easily setup them by using the following line")}}:</div>
                                                    <ul>
                                                        <li><code>* * * * * cd {{base_path()}} && php artisan schedule:run >> /dev/null 2>&1</code></li>
                                                    </ul>
                                                    <div class="mt-05">
                                                        {{__('Before setting up the payment processors, please also give the')}} <a class="text-white" target="_blank" href="https://docs.qdev.tech/justjobs/documentation.html/#crons">{{__("docs section")}}</a> {{__('a read')}}.
                                                    </div>
                                                </div>
                                            @endif

                                                <div class="alert alert-info alert-dismissible mb-1 payments-info-paypal d-none">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="info-label"><div class="icon voyager-info-circled"></div> {{__('In order to be able to receive payment updates from :provider, please use these webhooks endpoints', ['provider' => 'Paypal'])}}:</div>
                                                    <ul>
                                                        <li><code>{{route('paypal.payment.update')}}</code></li>
                                                    </ul>
                                                </div>


                                                <div class="alert alert-info alert-dismissible mb-1 payments-info-stripe d-none">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="info-label"><div class="icon voyager-info-circled"></div> {{__("In order to be able to receive payment updates from :provider, please use these webhooks endpoints", ['provider' => 'Stripe'])}}:</div>
                                                    <ul>
                                                        <li><code>{{route('stripe.payment.update')}}</code></li>
                                                    </ul>
                                                </div>

                                                <div class="alert alert-info alert-dismissible mb-1 payments-info-coinbase d-none">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="info-label"><div class="icon voyager-info-circled"></div> {{__("In order to be able to receive payment updates from :provider, please use these webhooks endpoints", ['provider' => 'Coinbase'])}}:</div>
                                                    <ul>
                                                        <li><code>{{route('coinbase.payment.update')}}</code></li>
                                                    </ul>
                                                </div>



                                                <div class="alert alert-info alert-dismissible mb-1 payments-info-ccbill d-none">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="info-label"><div class="icon voyager-info-circled"></div> {{__("In order to use :provider as payment provider you'll need the following endpoints", ['provider' => 'CCBill'])}}:
                                                        <ul>
                                                            <li>{{__('Webhook URL')}}: <code>{{route('ccBill.payment.update')}}</code></li>
                                                            <li>{{__('Approval & Denial URL')}}: <code>{{route('payment.checkCCBillPaymentStatus')}}</code></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="alert alert-info alert-dismissible mb-1 payments-info-paystack d-none">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="info-label"><div class="icon voyager-info-circled"></div> {{__("In order to use :provider as payment provider you'll need the following endpoints", ['provider' => 'Paystack'])}}:</div>
                                                    <ul>
                                                        <li>{{__('Webhook URL')}}: <code>{{route('paystack.payment.update')}}</code></li>
                                                        <li>{{__('Callback URL')}}: <code>{{route('payment.checkPaystackPaymentStatus')}}</code></li>
                                                    </ul>
                                                </div>

                                        </div>


                                    </div>

                                    <div class="">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="mb-4">{{__('Payments settings')}}</h4>

                                                <div class="tabbable-panel">
                                                    <div class="tabbable-line">
                                                        <ul class="nav nav-tabs ">
                                                            <li class="active">
                                                                <a href="#payments-general" data-toggle="tab" onclick="Admin.paymentsSettingsSubTabSwitch('general')">
                                                                    {{__('General settings')}} </a>
                                                            </li>
                                                            <li>
                                                                <a href="#payments-processors" data-toggle="tab" onclick="Admin.paymentsSettingsSubTabSwitch('processors')">
                                                                    {{__('Processors')}} </a>
                                                            </li>
                                                            <li>
                                                                <a href="#payments-invoices" data-toggle="tab" onclick="Admin.paymentsSettingsSubTabSwitch('invoices')">
                                                                    {{__('Invoices')}} </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-heading setting-row setting-payments.driver" data-settingkey="payments.driver">
                                        <h3 class="panel-title"> {{__('Payment provider')}}  </h3>
                                    </div>
                                    <div class="panel-body no-padding-left-right setting-row setting-payments.driver" data-settingkey="payments.driver">
                                        <div class="col-md-12 no-padding-left-right">
                                            <select class="form-control" name="payments.driver" id="payments.driver">
                                                <option value="stripe">Stripe</option>
                                                <option value="paypal">Paypal</option>
                                                <option value="coinbase">Coinbase</option>
                                                <option value="nowpayments">NowPayments</option>
                                                <option value="ccbill">CCBill</option>
                                                <option value="paystack">Paystack</option>
                                            </select>

                                        </div>

                                    </div>
                                @endif


                                @if($group == 'Social')

                                    <div class="social-login-info">
                                        <div class="alert alert-info alert-dismissible mb-1">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <div class="info-label"><div class="icon voyager-info-circled"></div>Each of the social login provider will require you a <i><strong>"Callback Url"</strong></i>. Here are the endpoints that you will need to set up for each provider:</div>
                                            <ul>
                                                <li><code>Facebook: {{route('social.login.callback',['provider'=>'facebook'])}}</code></li>
                                                <li><code>Twitter: {{route('social.login.callback',['provider'=>'twitter'])}}</code></li>
                                                <li><code>Google: {{route('social.login.callback',['provider'=>'google'])}}</code></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="mb-4">Social media settings</h4>

                                                <div class="tabbable-panel">
                                                    <div class="tabbable-line">
                                                        <ul class="nav nav-tabs ">
                                                            <li class="active">
                                                                <a href="#social-login" data-toggle="tab" onclick="Admin.socialSettingsSubTabSwitch('login')">
                                                                    Social login </a>
                                                            </li>
                                                            <li>
                                                                <a href="#social-links" data-toggle="tab" onclick="Admin.socialSettingsSubTabSwitch('links')">
                                                                    Social links </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @foreach($group_settings as $setting)
                                    <div class="panel-heading setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                                        <h3 class="panel-title">
                                            {{ $setting->display_name }} @if(config('voyager.show_dev_tips'))<code>getSetting('{{ $setting->key }}')</code>@endif
                                        </h3>
                                    </div>

                                    <div class="panel-body no-padding-left-right setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                                        <div class="col-md-12 no-padding-left-right">
                                            @if ($setting->type == "text")
                                                <input type="text" class="form-control" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                            @elseif($setting->type == "text_area")
                                                <textarea class="form-control" name="{{ $setting->key }}">{{ $setting->value ?? '' }}</textarea>
                                            @elseif($setting->type == "rich_text_box")
                                                <textarea class="form-control richTextBox" name="{{ $setting->key }}">{{ $setting->value ?? '' }}</textarea>
                                            @elseif($setting->type == "code_editor")
                                                <?php $options = json_decode($setting->details); ?>
                                                <div id="{{ $setting->key }}" data-theme="{{ @$options->theme }}" data-language="{{ @$options->language }}" class="ace_editor min_height_400" name="{{ $setting->key }}">{{ $setting->value ?? '' }}</div>
                                                <textarea name="{{ $setting->key }}" id="{{ $setting->key }}_textarea" class="hidden">{{ $setting->value ?? '' }}</textarea>
                                            @elseif($setting->type == "image" || $setting->type == "file")
                                                @if(isset( $setting->value ) && !empty( $setting->value ) /*&& Storage::disk(config('voyager.storage.disk'))->exists($setting->value)*/)
                                                    <div class="img_settings_container">
                                                        <a href="{{ route('voyager.settings.delete_value', $setting->id) }}" class="voyager-x delete_value"></a>
                                                        @php
                                                            $imageUrl = null;
                                                            $decodedValue = json_decode($setting->value, true);
                                                            if (is_array($decodedValue) && isset($decodedValue[0]['download_link'])) {
                                                                $imageUrl = Storage::disk(config('voyager.storage.disk'))->url($decodedValue[0]['download_link']);
                                                            } elseif (filter_var($setting->value, FILTER_VALIDATE_URL)) {
                                                                $imageUrl = Storage::disk(config('voyager.storage.disk'))->url($setting->value);
                                                            } else {
                                                                $imageUrl = Storage::disk(config('voyager.storage.disk'))->url($setting->value);
                                                            }
                                                            $imageUrl = urldecode($imageUrl);
                                                        @endphp

                                                        @if($imageUrl)
                                                            <img src="{{ $imageUrl }}" class="setting-value-image">
                                                        @endif
                                                    </div>
                                                    <div class="clearfix"></div>
                                                @elseif($setting->type == "file" && isset( $setting->value ))
                                                    @if(json_decode($setting->value) !== null)
                                                        @foreach(json_decode($setting->value) as $file)
                                                            <div class="fileType">
                                                                <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) }}">
                                                                    {{ $file->original_name }}
                                                                </a>
                                                                <a href="{{ route('voyager.settings.delete_value', $setting->id) }}" class="voyager-x delete_value"></a>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                                <input type="file" name="{{ $setting->key }}">
                                            @elseif($setting->type == "select_dropdown")
                                                <?php $options = json_decode($setting->details); ?>
                                                <?php $selected_value = (isset($setting->value) && !empty($setting->value)) ? $setting->value : NULL; ?>
                                                <select class="form-control" name="{{ $setting->key }}">
                                                    <?php $default = (isset($options->default)) ? $options->default : NULL; ?>
                                                    @if(isset($options->options))
                                                        @foreach($options->options as $index => $option)
                                                            <option value="{{ $index }}" @if($default == $index && $selected_value === NULL) selected="selected" @endif @if($selected_value == $index) selected="selected" @endif>{{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>

                                            @elseif($setting->type == "radio_btn")
                                                <?php $options = json_decode($setting->details); ?>
                                                <?php $selected_value = (isset($setting->value) && !empty($setting->value)) ? $setting->value : NULL; ?>
                                                <?php $default = (isset($options->default)) ? $options->default : NULL; ?>
                                                <ul class="radio">
                                                    @if(isset($options->options))
                                                        @foreach($options->options as $index => $option)
                                                            <li>
                                                                <input type="radio" id="option-{{ $index }}" name="{{ $setting->key }}"
                                                                       value="{{ $index }}" @if($default == $index && $selected_value === NULL) checked @endif @if($selected_value == $index) checked @endif>
                                                                <label for="option-{{ $index }}">{{ $option }}</label>
                                                                <div class="check"></div>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            @elseif($setting->type == "checkbox")
                                                <?php $options = json_decode($setting->details); ?>
                                                <?php $checked = (isset($setting->value) && $setting->value == 1) ? true : false; ?>
                                                @if (isset($options->on) && isset($options->off))
                                                    <input type="checkbox" name="{{ $setting->key }}" class="toggleswitch" @if($checked) checked @endif data-on="{{ $options->on }}" data-off="{{ $options->off }}">
                                                @else
                                                    <input type="checkbox" name="{{ $setting->key }}" @if($checked) checked @endif class="toggleswitch">
                                                @endif
                                            @endif
                                        </div>
                                        <div class="d-none">
                                            <select class="form-control group_select" name="{{ $setting->key }}_group">
                                                @foreach($groups as $group)
                                                    <option value="{{ $group }}" {!! $setting->group == $group ? 'selected' : '' !!}>{{ $group }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <?php
                                    $settingDetails = json_decode($setting->details);
                                    $hasDescription = false;
                                    if(isset($settingDetails->description)){
                                        $hasDescription = true;
                                    }
                                    ?>
                                    @if($hasDescription)
                                        <div class="admin-setting-description setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                                            <code>
                                                {{$settingDetails->description}}
                                            </code>
                                        </div>
                                    @endif
                                    @if(!$loop->last)
                                        <hr class="setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
            <button type="submit" class="btn btn-primary pull-right">{{ __('voyager::settings.save') }}</button>
        </form>

        <div class="clearfix"></div>


    </div>



@stop

@section('javascript')
    <script type="text/javascript" src="{{asset('/libs/@simonwep/pickr/dist/pickr.es5.min.js')}}"></script>
    <script>
        $('document').ready(function () {
            $('#toggle_options').on('click', function () {
                $('.new-settings-options').toggle();
                if ($('#toggle_options .voyager-double-down').length) {
                    $('#toggle_options .voyager-double-down').removeClass('voyager-double-down').addClass('voyager-double-up');
                } else {
                    $('#toggle_options .voyager-double-up').removeClass('voyager-double-up').addClass('voyager-double-down');
                }
            });

            $('.toggleswitch').bootstrapToggle();

            $('[data-toggle="tab"]').on('click', function() {
                $(".setting_tab").val($(this).html());
            });

            $('.delete_value').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form').attr('action', $(this).attr('href'));
                $(this).closest('form').submit();
            });

            // Initiliaze rich text editor
            tinymce.init(window.voyagerTinyMCE.getConfig());
        });
    </script>
    <script type="text/javascript">
        $(".group_select").not('.group_select_new').select2({
            tags: true,
            width: 'resolve'
        });
        $(".group_select_new").select2({
            tags: true,
            width: 'resolve',
            placeholder: '{{ __("voyager::generic.select_group") }}'
        });
        $(".group_select_new").val('').trigger('change');
    </script>
    <iframe id="form_target" name="form_target" class="d-none"></iframe>
    <form class="settings-upload" id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
        <input type="hidden" name="type_slug" id="type_slug" value="settings">
    </form>

    <script>
        try{
            var options_editor = ace.edit('options_editor');
            options_editor.getSession().setMode("ace/mode/json");

            var options_textarea = document.getElementById('options_textarea');
            options_editor.getSession().on('change', function() {
                console.log(options_editor.getValue());
                options_textarea.value = options_editor.getValue();
            });
        } catch (e) {
            // eslint-disable-next-line no-console
            console.warn(e);
        }

        var site_settings = {
            'emails.driver': "{{getSetting('emails.driver')}}",
            'storage.driver': "{{getSetting('storage.driver')}}",
            'websockets.driver': "{{getSetting('websockets.driver')}}",
            'colors.theme_color_code': "{{getSetting('colors.theme_color_code')}}",
            'colors.theme_gradient_from': "{{getSetting('colors.theme_gradient_from')}}",
            'colors.theme_gradient_to': "{{getSetting('colors.theme_gradient_to')}}",
            'license.product_license_key': "{{getSetting('license.product_license_key')}}",
            'security.captcha_driver': "{{getSetting('security.captcha_driver')}}",
        }

    </script>
@stop
