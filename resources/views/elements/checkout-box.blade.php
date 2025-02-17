<div class="row checkout-dialog">
    @include('elements.standard-dialog',[
        'dialogName' => 'update-plan',
        'title' => __('Update your plan'),
        'content' => __('Are you sure you want to cancel your current subscription and create a new one for the new plan?'),
        'actionLabel' => __('Yes'),
        'actionFunction' => 'checkout.initPayment();',
    ])
    <div class="col-lg-6 mx-auto">
        {{-- Paypal and stripe actual buttons --}}
        <div class="paymentOption paymentPP d-none">
            <form id="pp-buyItem" method="post" action="{{route('payment.initiatePayment')}}">
                @csrf
                <input type="hidden" name="amount" id="payment-deposit-amount" value="12">
                <input type="hidden" name="transaction_type" id="payment-type" value="">
                <input type="hidden" name="job_id" id="job" value="">
                <input type="hidden" name="plan_id" id="plan" value="">
                <input type="hidden" name="provider" id="provider" value="">
                <input type="hidden" name="first_name" id="paymentFirstName" value="">
                <input type="hidden" name="last_name" id="paymentLastName" value="">
                <input type="hidden" name="billing_address" id="paymentBillingAddress" value="">
                <input type="hidden" name="city" id="paymentCity" value="">
                <input type="hidden" name="state" id="paymentState" value="">
                <input type="hidden" name="postcode" id="paymentPostcode" value="">
                <input type="hidden" name="country" id="paymentCountry" value="">
                <input type="hidden" name="taxes" id="paymentTaxes" value="">
                <button class="payment-button" type="submit"></button>
            </form>
        </div>

        <div class="paymentOption ml-2 paymentStripe d-none">
            <button id="stripe-checkout-button">{{__('Checkout')}}</button>
        </div>


        <div class="payment-body">

            <div class="input-group mb-3 checkout-amount-input d-none">
                <div class="input-group-prepend">
                            <span class="input-group-text" id="amount-label">
                                @include('elements.icon',['icon'=>'cash-outline','variant'=>'medium','centered'=>false])
                            </span>
                </div>
                <input class="form-control uifield-amount" placeholder="{{__('Amount ($5 min, $500 max)',['min'=>getSetting('payments.min_tip_value'),'max'=>getSetting('payments.max_tip_value'),'currency'=>config('app.site.currency_symbol')])}}" aria-label="Username" aria-describedby="amount-label" id="checkout-amount" type="number" min="0" step="1" max="500" >
                <div class="invalid-feedback">{{__('Please enter a valid amount.')}}</div>
            </div>
        </div>

        <div id="accordion" class="mb-3">
            <div class="card rounded-xl shadow-sm">

                <div id="billingInformation" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <form id="billing-agreement-form">
                            <div class="tab-content">
                                <!-- credit card info-->
                                <div id="individual" class="tab-pane fade show active pt-1">
                                    <div class="row form-group">
                                        <div class="col-sm-6 col-6">
                                            <div class="form-group">
                                                <label for="firstName">
                                                    <span>{{__('First name')}}</span>
                                                </label>
                                                <input type="text" name="firstName" placeholder="{{__('First name')}}" onchange="checkout.validateFirstNameField();" required class="form-control uifield-first_name">
                                            </div>

                                        </div>
                                        <div class="col-sm-6 col-6">
                                            <div class="form-group">
                                                <label for="lastName">
                                                    <span>{{__('Last name')}}</span>
                                                </label>
                                                <input type="text" name="lastName" placeholder="{{__('Last name')}}" onblur="checkout.validateLastNameField()" required class="form-control uifield-last_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="countrySelect">
                                            <span>{{__('Country')}}</span>
                                        </label>
                                        <select class="country-select form-control input-sm uifield-country" id="countrySelect" required onchange="checkout.validateCountryField()"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="billingCity">
                                            <span>{{__('City')}}</span>
                                        </label>
                                        <input type="text" name="billingCity" placeholder="{{__('City')}}" onblur="checkout.validateCityField()" required class="form-control uifield-city">
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-6">
                                            <div class="form-group">
                                                <label for="billingState">
                                                    <span>{{__('State')}}</span>
                                                </label>
                                                <input type="text" name="billingState" placeholder="{{__('State')}}" onblur="checkout.validateStateField()" required class="form-control uifield-state">
                                            </div>

                                        </div>
                                        <div class="col-sm-6 col-6">
                                            <div class="form-group">
                                                <label for="billingPostcode">
                                                    <span>{{__('Postcode')}}</span>
                                                </label>
                                                <input type="text" name="billingPostcode" placeholder="{{__('Postcode')}}" onblur="checkout.validatePostcodeField()" required class="form-control uifield-postcode">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardNumber">
                                            <span>{{__('Address')}}</span>
                                        </label>
                                        <textarea rows="2" type="text" name="billingAddress" onblur="checkout.validateBillingAddressField()" placeholder="{{__('Street address, apartment, suite, unit')}}" class="form-control w-100 uifield-billing_address" required></textarea>
                                    </div>
                                </div>
                                <div class="billing-agreement-error error text-danger d-none">{{__('Please complete all billing details')}}</div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="col-lg-6 mx-auto">

        <div class="card rounded-xl shadow-sm ">
            <div class="card-body">

                <div class="d-flex justify-content-center">
                    @if($selectedPlan->price)
                        <div class="col-12 pb-4 px-0">
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center" id="plan-month" onclick="checkout.switchSubscriptionPeriod()">
                                    <input type="radio" name="interval" value="month" checked="checked" >{{__('Monthly')}}
                                </label>
                                <label class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center" id="plan-year" onclick="checkout.switchSubscriptionPeriod(12)">
                                    <input type="radio" name="interval" value="year">{{__('Yearly')}}
                                    <span class="badge bg-success text-white ml-2">{{$discount ? "$discount%" : ''}}</span>
                                </label>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <div class="d-flex justify-content-between">
                        <h6>{{__('Payment summary')}}</h6>
                        <b class="payment-description mb-3 d-none"></b>
                    </div>
                    <div class="subtotal row">
                        <span class="col-sm left"><b>{{__('Subtotal')}}:</b></span>
                        <span class="subtotal-amount col-sm right text-right">
                                        <b>$0.00</b>
                                    </span>
                    </div>
                    <div class="taxes row">
                        <span class="col-sm left"><b>{{__('Taxes')}}:</b></span>
                    </div>
                    <div class="taxes-details"></div>
                    <div class="total row">
                        <span class="col-sm left"><b>{{__('Total')}}:</b></span>
                        <span class="total-amount col-sm right text-right">
                                        <b>$0.00</b>
                                    </span>
                    </div>
                </div>

                <div class="payment-method d-none mt-3">
                    <h6>{{__('Payment method')}}</h6>
                    <div class="d-flex text-left radio-group row px-2">
                        @if(getSetting('payments.stripe_secret_key') && getSetting('payments.stripe_public_key') && !getSetting('payments.stripe_checkout_disabled'))
                            <div class="p-1 col-6 col-md-3 col-lg-3 col-md-3 stripe-payment-method" >
                                <div class="radio mx-auto stripe-payment-provider d-flex align-items-center justify-content-center" data-value="stripe">
                                    <img src="{{asset('/img/logos/stripe.svg')}}">
                                </div>
                            </div>
                        @endif
                        @if(getSetting('payments.paypal_client_id') && getSetting('payments.paypal_secret') && !getSetting('payments.paypal_checkout_disabled'))
                            <div class="p-1 col-6 col-md-3 col-lg-3 col-md-3 paypal-payment-method">
                                <div class="radio mx-auto paypal-payment-provider d-flex align-items-center justify-content-center" data-value="paypal">
                                    <img src="{{asset('/img/logos/paypal.svg')}}">
                                </div>
                            </div>
                        @endif
                        @if(getSetting('payments.coinbase_api_key') && !getSetting('payments.coinbase_checkout_disabled'))
                            <div class="p-1 col-6 col-md-3 col-lg-3 col-md-3 coinbase-payment-method">
                                <div class="radio mx-auto coinbase-payment-provider d-flex align-items-center justify-content-center" data-value="coinbase">
                                    <img src="{{asset('/img/logos/coinbase.svg')}}">
                                </div>
                            </div>
                        @endif
                        @if(getSetting('payments.nowpayments_api_key') && !getSetting('payments.nowpayments_checkout_disabled'))
                            <div class="p-1 col-6 col-md-3 col-lg-3 col-md-3 nowpayments-payment-method">
                                <div class="radio mx-auto nowpayments-payment-provider d-flex align-items-center justify-content-center" data-value="nowpayments">
                                    <img src="{{asset('/img/logos/nowpayments.svg')}}">
                                </div>
                            </div>
                        @endif
                        @if(\App\Providers\PaymentsServiceProvider::ccbillCredentialsProvided())
                            <div class="p-1 col-6 col-md-3 col-lg-3 col-md-3 d-none ccbill-payment-method">
                                <div class="radio mx-auto ccbill-payment-provider d-flex align-items-center justify-content-center" data-value="ccbill">
                                    <img src="{{asset('/img/logos/ccbill.svg')}}">
                                </div>
                            </div>
                        @endif
                        @if(getSetting('payments.paystack_secret_key') && !getSetting('payments.paystack_checkout_disabled'))
                            <div class="p-1 col-6 col-md-3 col-lg-3 col-md-3 paystack-payment-method">
                                <div class="radio mx-auto paystack-payment-provider d-flex align-items-center justify-content-center" data-value="paystack">
                                    <img src="{{asset('/img/logos/paystack.svg')}}">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="payment-error error text-danger text-bold d-none mb-1">{{__('Please select your payment method')}}</div>
                <p class="text-muted mt-3 payment-method-description d-none"> {{__('Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be redirected back to the website.')}} </p>

            </div>
        </div>
    </div>


</div>

