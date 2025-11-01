<div class="col-12 col-md-4 pricing-table" data-package-id="{{$plan->id}}">

    <div class="d-flex justify-content-center selected-pack-label">
        <span class="badge badge-primary">{{__('SELECTED')}}</span>
    </div>

    <div class="card rounded-xl no-gutters p-4 pricing-table-wrapper pointer-cursor">

        <div class="d-flex justify-content-center">
            <div class="text-uppercase font-weight-bolder h5">{{$plan->name}}</div>
        </div>

        <div class="d-flex justify-content-center">
            <div class="text-uppercase font-weight-bolder h3">{{\App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($plan->price)}}</div>
        </div>

        <div class="px-4">
            <hr>
        </div>

        <div class="features-list">
            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->display_logo ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Display your company logo')}}
            </div>
            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->share_on_social_media ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Share on social media network')}}
            </div>
            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->share_on_newsletter ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Display in newsletter campaigns')}}
            </div>
            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->share_on_partner_network ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Distribute to Partner Network')}}
            </div>
            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->share_on_slack ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Share in Slack channel')}}
            </div>

            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->highlight_ad ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Highlight ad')}}
            </div>

            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> $plan->main_page_pin ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Pin on homepage')}}
            </div>

            <div class="d-flex flex-row align-items-center mb-3">
                @include('elements.icon',['icon'=>'checkmark-circle','variant'=>'small', 'classes'=> !$plan->hasPaymentForPlan && $plan->trial_days > 0 ? 'text-primary mr-2' : 'text-gradient bg-gradient-faded-secondary mr-2', 'centered' => false])
                {{__('Free trial')}}
            </div>

        </div>
    </div>

    <div class="d-flex justify-content-end">
        <span class="text-muted mt-1">
            @if($increment !== 0)
                <small>{{($increment+1) * $i}}X {{__('more views')}}</small>
            @endif
        </span>
    </div>

</div>
