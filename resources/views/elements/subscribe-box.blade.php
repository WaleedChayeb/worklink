<div class="subscribe-box card rounded-xl shadow-sm {{isset($classes) ? $classes : ''}}">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-7 d-flex justify-content-center align-items-center">
                <div class="">
                    <div>
                        <div class="h4 font-weight-bold">{{__("We've got more coming...")}}</div>
                        <p class="mt-2 mb-3">{{__("Want to hear from us when we add new items? Sign up for our newsletter and we'll email you every time we got a new batch of items.")}}</p>
                    </div>
                    <div class="subscribe-inline w-100 mt-3">
                            <div class="d-flex">
                                <input type="email" name="subscriber-email-field" id="subscriber-email-field" class="form-control" placeholder="{{__("Email address")}}">
                                <button type="submit" onclick="NewsLetter.addEmailSubscriber()" class="btn btn-primary mb-0 email-subscribe-button">{{__('Subscribe')}}</button>
                            </div>
                            <div class="subscribe-error"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5 mb-2 mb-md-0 d-none d-md-block">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{asset('/img/newsletter.svg')}}" alt="{{__('Subscribe')}}" class="subscribe-box-image"/>
                </div>
            </div>
        </div>

    </div>
</div>
