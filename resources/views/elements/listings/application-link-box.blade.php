@if(is_int(strpos($job->application_link, 'http')))
    <a href="{{$job->application_link}}" class="btn btn-primary btn-block mb-0" target="_blank">{{__('Apply for this position')}}</a>
@elseif(preg_match('/^\+?[0-9]+$/', str_replace([' ', '-', '(', ')'], '', $job->application_link)))
    <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $job->application_link) }}" class="btn btn-primary btn-block mb-0" target="_blank">{{__('Apply for this position')}}</a>
@else
    <a href="mailto:{{$job->application_link}}" class="btn btn-primary btn-block mb-0" target="_blank">{{__('Apply for this position')}}</a>
@endif
