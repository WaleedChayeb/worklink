<div class="py-2 {{isset($classes) ? $classes : ''}}">
    <div class="container">
        <div class="text-center mb-3">
            <p class="mb-0 text-muted font-weight-bold mb-4">{{__("Trusted by the world's leading companies")}}</p>
        </div>
        <div class="d-flex align-items-center justify-content-center">
            <div class="d-flex justify-content-center align-items-center row col">
                @foreach(GenericHelper::getAvailableFeaturedClients() as $client)
                    @if($client->company)
                        <a href="{!! $client->hyperlink ? $client->hyperlink : route('company.get', ['slug' => $client->company->slug]) !!}" target="_blank" rel="nofollow">
                            <img src="{{$client->company->logo}}" class="mx-4 {{$loop->last ? '' : 'mb-4 mb-md-1'}} grayscale featured-client-logo" title="{{$client->company->name}}"/>
                        </a>
                    @else
                        @if($client->hyperlink)<a href="{!! $client->hyperlink !!}" target="_blank" rel="nofollow"> @endif
                            <img src="{{asset($client->logo)}}" class="mx-4 {{$loop->last ? '' : 'mb-4 mb-md-1'}} grayscale featured-client-logo" title="{{__($client->client_name)}}"/>
                            @if($client->hyperlink)</a> @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
