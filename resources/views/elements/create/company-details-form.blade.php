<div class="d-flex justify-content-{{$showGuide ? 'between' : 'end'}} {{$showGuide ? 'mt-4' : ''}} mb-4">
    @if($showGuide !== false)
        <div>
            <h5 class="font-weight-bolder  text-gradient bg-gradient-primary">{{__('Tell Us More About Your Company')}}</h5>
            @if( !(Auth::check() && count(Auth::user()->companies) == 0) )
                <span class="text-muted existing-company-label"><span class="font-weight-bold ">{{__("Posted before?")}}</span> {{__("Pick an")}}  <a href="javascript:void(0);" onclick="{{Auth::check() ? "CreateHelper.toggleCompanyCreateSelector('existing')" : "CreateHelper.openLoginDialog()"}}">{{__('existing company')}}</a>.</span>
            @else
                <span class="text-muted existing-company-label">{{__('Your company will be saved so you can re-use it for other listings.')}}</span>
            @endif
            <span class="text-muted new-company-label d-none"><span class="font-weight-bold ">{{__('Want to start fresh?')}}</span> {{__('Create a')}} <a href="javascript:void(0);" onclick="CreateHelper.toggleCompanyCreateSelector('new')">{{__('new company')}}</a>.</span>
        </div>
    @else

        <div class="d-flex align-items-center">
            <div class="text-muted mr-2 required-field-label">{{__('REQUIRED FIELDS')}}</div> <div class="bg-warning rounded-pill required-field"></div>
        </div>
    @endif

</div>

@if(Auth::check() && count(Auth::user()->companies) &&  !in_array(Route::currentRouteName(), ['my.companies.create','my.companies.edit']))
    {{-- Use a previously created company--}}
    <div class="existing-company">
        <div class="input-holder">
            <select id="company_id" name="company_id" class="repositories form-control input-sm" placeholder="{{__('Company')}}"></select>
        </div>
    </div>
@endif

<div class="new-company">

    <div class="d-flex flex-row">
        <div class="w-50 {{GenericHelper::getSiteDirection() == 'rtl' ? 'pl-2' : 'pr-2'}}">
            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="company_name">
                    {{__('Company name')}}
                    <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
                </label>
                <small class="form-text text-muted mb-2">{{__('Enter your company or organization’s name.')}}</small>
                <input class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}" id="company_name" name="company_name">
            </div>
        </div>

        <div class="w-50">
            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="company_hq">
                    {{__('Company HQ')}}
                    <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
                </label>
                <small class="form-text text-muted mb-2">{{__('Where your company is officially headquartered.')}}</small>
                <input class="form-control {{ $errors->has('company_hq') ? 'is-invalid' : '' }}" id="company_hq" name="company_hq">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="font-weight-bold d-flex mb-1" for="company_logo">
            {{__('Company logo')}}
            <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
        </label>
        <div class="company-logo-wrapper d-flex justify-content-center align-items-center pointer-cursor">

            <div class="">
                <div class="font-weight-bolder text-muted mb-1">{{__('Click or drag your image here.')}}</div>
                <div class="d-flex justify-content-center">
                    <img class="card-img-top"  src="{{asset('/img/default-avatar.jpg')}}">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-row">
        <div class="w-50 {{GenericHelper::getSiteDirection() == 'rtl' ? 'pl-2' : 'pr-2'}}">
            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="company_website_url">
                    {{__('Company\'s Website URL')}}
                    <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
                </label>
                <small class="form-text text-muted mb-2">{{__('EG: https://website.com')}}</small>
                <input class="form-control {{ $errors->has('company_website_url') ? 'is-invalid' : '' }}" id="company_website_url" name="company_website_url">
            </div>
        </div>

        <div class="w-50">
            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="company_email">
                    {{__('Email')}}
                    <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
                </label>
                <small class="form-text text-muted mb-2">{{__("The company email won't shown anywhere")}}</small>
                <input class="form-control {{ $errors->has('company_email') ? 'is-invalid' : '' }}" id="company_email" name="company_email">
            </div>
        </div>
    </div>


    <div class="form-group">
        <label class="font-weight-bold d-flex mb-1" for="description">
            {{__('Company description')}}
            <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
        </label>

        <div class="d-flex justify-content-between align-items-center">
            @include('elements.trix-toolbar',['id' => 'company-form-toolbar'])
        </div>
        <trix-editor input="company_description" class="{{ $errors->has('company_description') ? 'is-invalid' : '' }}" toolbar="company-form-toolbar"></trix-editor>
        <input id="company_description" type="hidden" name="company_description">
        <div class="d-flex justify-content-between mt-1">
            <div class="error-holder"></div>
            @if(getSetting('ai.open_ai_enabled'))
                <a href="javascript:void(0)" onclick="{{"AiSuggestions.suggestDescriptionDialog('#company_description', 'companyDescription');"}}" data-toggle="tooltip" data-placement="left" title="{{__('Use AI to generate your description.')}}">{{trans_choice("Suggestion",2)}}</a>
            @endif
        </div>
    </div>
</div>
