<div class="d-flex justify-content-{{$showGuide ? 'between' : 'end'}} mb-4">
    @if($showGuide !== false)
        <div><h5 class="font-weight-bolder mb-0 text-gradient bg-gradient-primary">{{__('First, tell us about the position')}}</h5></div>
    @endif
    <div class="d-flex align-items-center">
        <div class="text-muted mr-2 required-field-label">{{__('REQUIRED FIELDS')}}</div> <div class="bg-warning rounded-pill required-field"></div>
    </div>
</div>

<div class="form-group">
    <label class="font-weight-bold d-flex mb-1" for="title">
        {{__('Job title')}}
        <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
    </label>
    <small  class="form-text text-muted mb-2">{{__('Example: “Senior Designer”. Titles must describe one position.')}}</small>
    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" id="title" name="title">
</div>

<div class="d-flex flex-row">
    <div class="w-50 {{GenericHelper::getSiteDirection() == 'rtl' ? 'pl-2' : 'pr-2'}}">
        <div class="form-group">
            <label class="font-weight-bold d-flex mb-1" for="location">
                {{__('Job location')}}
                <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
            </label>
            <small  class="form-text text-muted mb-2 d-none d-md-block">{{__("EG: 'Remote', 'Remote / USA', 'New York City', 'Remote GMT-5', etc.")}}</small>
            <small  class="form-text text-muted mb-2 d-block d-md-none">{{__("EG:  'Remote/US'.")}}</small>
            <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location" name="location">
        </div>
    </div>

    <div class="w-50">
        <div class="form-group">
            <label class="font-weight-bold d-flex mb-1" for="type_id">
                {{__('Job type')}}
                <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
            </label>
            <small  class="form-text text-muted mb-2">{{__('Full time / Contract.')}}</small>

            <select id="type_id" name="type_id" class="form-control {{ $errors->has('type_id') ? 'is-invalid' : '' }}">
                @foreach(JobsHelper::getAvaialableJobTypes() as $type)
{{--                    <option value="{{$type->id}}">{{__($type->name)}}</option>--}}
                    <option id="{{$type->id}}" {{session('jobRequest.type_id') == $type->name ? 'selected' : ''}}>{{__($type->name)}}</option>

                @endforeach
            </select>

            @if($errors->has('type_id'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{$errors->first('type_id')}}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="d-flex flex-row">
    <div class="w-50 {{GenericHelper::getSiteDirection() == 'rtl' ? 'pl-2' : 'pr-2'}}">
        <div class="form-group">
            <label class="font-weight-bold d-flex mb-1" for="category_id">
                {{__('Job category')}}
                <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
            </label>
            <select id="category_id" name="category_id" class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                <option selected>{{__('Choose...')}}</option>
                @foreach(JobsHelper::getAvailableCategories() as $category)
                    <option id="{{$category->id}}" {{session('jobRequest.category_id') == $category->name ? 'selected' : ''}}>{{__($category->name)}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="w-50">
        <div class="form-group">
            <label class="font-weight-bold d-flex mb-1" for="skills">
                {{__('Job skills')}}
                <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
            </label>
            <select id="skills" name="skills" class="form-control skills-selector {{ $errors->has('skills') ? 'is-invalid' : '' }}" multiple="multiple">
                @foreach(JobsHelper::getAvailableSkills() as $skill)
                    @if($skill->name !== 'All')
                        <option>{{$skill->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="font-weight-bold mb-1" for="salary">{{__('Salary')}}</label>
    <small  class="form-text text-muted mb-2">{{__("Examples: '$120,000 – $145,000 USD', '€80,000 — €102,000'")}}</small>
    <input class="form-control {{ $errors->has('salary') ? 'is-invalid' : '' }}" id="salary" name="salary">
</div>

<div class="form-group">
    <label class="font-weight-bold d-flex mb-1" for="application_link">
        {{__('Application Link or Email')}}
        <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
    </label>
    <small  class="form-text text-muted mb-2">{{__('Link to Application page or Email address.')}}</small>
    <input class="form-control {{ $errors->has('application_link') ? 'is-invalid' : '' }}" id="application_link" name="application_link">
</div>


<div class="form-group">
    <label class="font-weight-bold d-flex mb-1" for="description">
        {{__('Job description')}}
        <div class="d-flex align-items-center ml-2"><div class="bg-warning rounded-pill required-field"></div></div>
    </label>
    {{--    Trix toolbard override --}}
    <div class="d-flex justify-content-between align-items-center">
        @include('elements.trix-toolbar',['id' => 'job-form-toolbar'])
    </div>
    <trix-editor input="description" class="{{ $errors->has('description') ? 'is-invalid' : '' }}" toolbar="job-form-toolbar"></trix-editor>
    <input id="description" type="hidden" name="description">
    <div class="d-flex justify-content-between mt-1">
        <div class="error-holder"></div>
        @if(getSetting('ai.open_ai_enabled'))
            <a href="javascript:void(0)" onclick="{{"AiSuggestions.suggestDescriptionDialog('#description', 'jobDescription');"}}" data-toggle="tooltip" data-placement="left" title="{{__('Use AI to generate your description.')}}">{{trans_choice("Suggestion",2)}}</a>
        @endif
    </div>
</div>

