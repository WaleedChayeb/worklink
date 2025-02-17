<div class="featured-category-wrapper my-5">
    <div class="listings-wrapper">
        <div class="mb-3 d-flex justify-content-between">
            <span class="h4 font-weight-bold">{{$categoryName}}</span>
            @if(count($listings))
                {{--                <div class="text-muted h6 mt-2 flex-column-reverse ml-2 d-none d-md-flex">{{__('Last updated')}} {{$listings[0]->created_at->diffForHumans(null,false,false)}}</div>--}}
            @endif
        </div>
        @if(count($listings))
            @foreach($listings as $job)
                @include('elements.listings.job-listing-box',['job' => $job])
            @endforeach
        @else
            <h6 class="text-muted">{{__('No jobs within this category yet')}}</h6>
        @endif
    </div>
    @if($category !== 'all')
        @if(count($listings) && $category->id)
            <div class="w-100 d-flex align-items-center justify-content-center">
                <a href="{{route('browse.get', ['slug' => Str::slug(\App\Model\JobCategory::where('id', $category->id)->first()->name)])}}" class="btn btn-outline-primary mb-0">{{__('View all')}} {{$listings->total()}} {{$category->name}} {{__('jobs')}}</a>
            </div>
        @endif
    @else
        <div class="w-100 d-flex align-items-center justify-content-center">
            <a href="{{route('browse.get', ['slug' => Str::slug("all")])}}" class="btn btn-outline-primary mb-0">{{__('View all')}} {{$listings->total()}} {{__('jobs')}}</a>
        </div>
    @endif
</div>
