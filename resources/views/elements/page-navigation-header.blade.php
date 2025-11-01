<div class="row mt-4 pt-2 mt-md-5 pt-md-0 mb-3">
    <div class="col-md-12 col-lg-12 mb-0 mb-md-5 mb-lg-0 mt-1 mt-md-0 ">
        <div class="mb-3 w-100">
            <div class="d-flex flex-row align-items-center">
                @foreach($points as $data)
                    @if(isset($data['route']))
                        <a class="" href="{{$data['route']}}">{{$data['title']}}</a>
                        @include('elements.icon',['icon'=>'chevron-forward-outline','centered'=>'false','classes'=>'mx-2','variant'=>'small'])
                    @else
                        <div class="">{{$data['title']}}</div>
                    @endif
                @endforeach
            </div>
            <div class="w-100 d-flex align-items-center flex-row justify-content-between">
                <div class="text-bold mt-2 mt-md-3 mb-0 h3 {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{$title}}</div>
                @if(isset($button))
                    <a class="btn btn-primary m-0" href="{{$button['route']}}">{{$button['title']}}</a>
                @endif
            </div>
        </div>
    </div>
</div>
