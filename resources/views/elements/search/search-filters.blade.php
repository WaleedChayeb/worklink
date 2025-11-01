<div class="card rounded-xl shadow-sm">
    <div class="card-body">

        <form class="search-filters-form mb-0">
            <div class="form-group">
                <label for="terms" class="font-weight-bold mb-1">{{__('Terms')}}</label>
                <input type="text" class="form-control" id="terms" name="terms" placeholder="{{__('Laravel developer')}}" value="{{$filters['terms'] ? $filters['terms'] : ''}}">
            </div>

            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="category_id">
                    {{__('Category')}}
                </label>

                <select id="category_id" name="category_id" class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                    <option value="all">{{__('All...')}}</option>
                    @foreach(JobsHelper::getAvailableCategories() as $category)
                        <option value="{{$category->id}}" id="{{$category->id}}" id="{{$category->id}}" {{$filters['category_id'] == $category->id ? 'selected' : ''}}>{{__($category->name)}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="skills">
                    {{__('Job skills')}}
                </label>
                <select id="skills" name="skills[]" class="form-control skills-selector {{ $errors->has('skills') ? 'is-invalid' : '' }}" multiple="multiple">
                    @foreach(JobsHelper::getAvailableSkills() as $skill)
                        @if($skill->name !== 'All')
{{--                            todo: Translate this one/adapt url to handle it--}}
                            <option>{{$skill->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label class="font-weight-bold d-flex mb-1" for="type_id">
                    {{__('Job type')}}
                </label>
                <select id="type_id" name="type_id" class="form-control {{ $errors->has('type_id') ? 'is-invalid' : '' }}">
                    <option value="all">{{__('All...')}}</option>
                    @foreach(JobsHelper::getAvaialableJobTypes() as $type)
                        <option value="{{$type->id}}" {{$filters['type_id'] == $type->id ? 'selected' : ''}}>{{__($type->name)}}</option>
                    @endforeach
                </select>

                @if($errors->has('type_id'))
                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{$errors->first('type_id')}}</strong>
                                                </span>
                @endif
            </div>

            <div class="form-group">
                <label for="location" class="font-weight-bold mb-1">{{__('Location')}}</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="{{__('Remote/Canada/New York')}}" value="{{$filters['location'] ? $filters['location'] : ''}}">
            </div>

            <div class="form-group">
                <label for="applicants_number" class="font-weight-bold mb-1">{{__('Applicants')}}</label>
                <select class="form-control" id="applicants_number" name="applicants_number">
                    <option value="all">{{__('All...')}}</option>
                @foreach(JobsHelper::getAvailableApplicantsRanges() as $type)
                        <option value="{{$type->id}}" {{$filters['applicants_number'] == $type->id ? 'selected' : ''}}>{{__($type->name)}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sort_range" class="font-weight-bold mb-1">{{__('Posted')}}</label>
                <select class="form-control" id="sort_range" name="sort_range">
                    <option value="all">{{__('All...')}}</option>
                @foreach(config('app.site.jobs.sort_ranges') as $range)
                        <option value="{{$range['id']}}" id="{{$range['id']}}" {{$filters['sort_range'] == $range['id'] ? 'selected' : ''}}>{{__($range['name'])}}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary btn-block mb-0">{{__('Search')}}</button>

        </form>
    </div>
</div>
