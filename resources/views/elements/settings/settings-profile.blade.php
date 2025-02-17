<div class="mt-3 mt-md-0">

    @if(!Auth::user()->email_verified_at) @include('elements.resend-verification-email-box') @endif

    <form method="POST" action="{{route('my.settings.profile.save',['type'=>'profile'])}}">
        @csrf
        @include('elements.dropzone-dummy-element')
        <div class="mb-4">
            <div class="form-group">
                <label for="birthdate">{{__('Avatar')}}</label>

                <div class="d-flex align-items-center">


                    <div class="shadow-sm avatar-holder">
                        <img class="card-img-top" src="{{Auth::user()->avatar}}">
                    </div>

                    <div class="ml-3">
                        <div>
                            <a href="javascript:void(0);" class="avatar-change-label">{{__('Change')}}</a>
                            <span class="text-muted">|</span>
                            <a href="javascript:void(0);" onclick="ProfileSettings.removeUserAsset('avatar')">{{__('Remove')}}</a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
        @if(session('success'))
            <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
                {{session('success')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{__('Close')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="form-group">
            <label for="name">{{__('Full name')}}</label>
            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" aria-describedby="emailHelp" value="{{Auth::user()->name}}">
            @if($errors->has('name'))
                <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('name')}}</strong>
            </span>
            @endif
        </div>
        <div class="form-group">
            <label for="bio">{{__('Bio')}}</label>
            <textarea class="form-control {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio" rows="3" spellcheck="false">{{Auth::user()->bio}}</textarea>
            @if($errors->has('bio'))
                <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('bio')}}</strong>
            </span>
            @endif
        </div>
        <div class="form-group">
            <label for="birthdate">{{__('Birthdate')}}</label>
            <input type="date" class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="birthdate" name="birthdate" aria-describedby="emailHelp"  value="{{Auth::user()->birthdate}}" max="{{$minBirthDate}}">
            @if($errors->has('birthdate'))
                <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('birthdate')}}</strong>
            </span>
            @endif
        </div>

        <div class="form-group">
            <label for="location">{{__('Location')}}</label>
            <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location" name="location" aria-describedby="emailHelp"  value="{{Auth::user()->location}}">
            @if($errors->has('location'))
                <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('location')}}</strong>
            </span>
            @endif
        </div>

        <div class="form-group">
            <label for="website" value="{{Auth::user()->website}}">{{__('Website URL')}}</label>
            <input type="url" class="form-control {{ $errors->has('website') ? 'is-invalid' : '' }}" id="website" name="website" aria-describedby="emailHelp" value="{{Auth::user()->website}}">
            @if($errors->has('website'))
                <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('website')}}</strong>
            </span>
            @endif
        </div>
        <button class="btn btn-primary btn-block rounded mr-0" type="submit">{{__('Save')}}</button>
    </form>
</div>
