<div class="tags-box card mb-4 rounded-xl shadow-sm">
    <div class="card-body">
        <div class="card-title mb-0">
            <div class="h4 font-weight-bold {{count($tags) ? 'mb-4 mt-1' : 'mb-2'}}">{{__('Popular tags')}}</div>
        </div>
        <div class="pl-0">
            <div class="d-flex flex-row">
                @if(count($tags))
                    <div class="row">
                        <div class="col-12">
                            @foreach($tags as $tag)
                                <a href="{{route('search.get',['skills' => [$tag->name]])}}">
                                    <span class="badge badge-primary badge-primary-outline badge badge-pill mr-2 mb-2">{{$tag->name}}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="mb-0">{{__('No tags available')}}</p>
                @endif
            </div>
        </div>
    </div>
</div>
