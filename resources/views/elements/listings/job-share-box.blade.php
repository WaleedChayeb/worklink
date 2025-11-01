<div class="card rounded-xl mt-4 shadow-sm">
    <div class="card-body">

        <p class=" font-weight-bold">{{__('Share this job')}}</p>

        <div class="input-group ">
            <input type="text" class="form-control" value="{{Request::url()}}">
            <div class="input-group-append ">
                <button class="btn btn-outline-primary mb-0 py-0 px-3" type="button" onclick="copyJobUrl('{{Request::url()}}')">
                    @include('elements.icon',['icon'=>'copy-outline','variant'=>'medium'])
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">

            <a class="m-2" data-sharer="facebook" data-hashtag="hashtag" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}"  href="javascript:void();">
                @include('elements.icon',['icon'=>'logo-facebook','variant'=>'medium','classes' => 'opacity-8'])
            </a>

            <a class="m-2" data-sharer="twitter" data-title="Checkout Sharer.js!" data-hashtags="awesome, sharer.js" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" href="javascript:void(0);" >
                @include('elements.icon',['icon'=>'logo-twitter','variant'=>'medium','classes' => 'opacity-8'])
            </a>
            <a class="m-2" data-sharer="skype" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" data-title="Checkout Sharer.js!" href="javascript:void(0);" >
                @include('elements.icon',['icon'=>'logo-skype','variant'=>'medium','classes' => 'opacity-8'])
            </a>
            <a class="m-2" data-sharer="whatsapp" data-title="Checkout Sharer.js!" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" href="javascript:void(0);" >
                @include('elements.icon',['icon'=>'logo-whatsapp','variant'=>'medium','classes' => 'opacity-8'])
            </a>
            <a class="m-2" data-sharer="email" data-title="Awesome Url" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" data-subject="{{__('Hey! Check out that URL')}}" data-to="some@email.com" href="javascript:void(0);" >
                @include('elements.icon',['icon'=>'mail-outline','variant'=>'medium','classes' => 'opacity-8'])
            </a>
            <a class="m-2" class="button" data-sharer="linkedin" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" href="javascript:void(0);" >
                @include('elements.icon',['icon'=>'logo-linkedin','variant'=>'medium','classes' => 'opacity-8'])
            </a>
            <a class="m-2" data-sharer="telegram" data-title="Checkout Sharer.js!" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" href="javascript:void(0);" >
                @include('elements.icon',['icon'=>'paper-plane','variant'=>'medium','classes' => 'text-lg opacity-8'])
            </a>
            <a class="m-2" data-sharer="reddit" data-url="{{route('jobs.get',[ 'slug'=> $job->slug])}}" href="javascript:void(0);">
                @include('elements.icon',['icon'=>'logo-reddit','variant'=>'medium','classes' => 'text-lg opacity-8'])
            </a>
        </div>

    </div>
</div>
