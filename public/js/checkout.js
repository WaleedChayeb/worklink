/**
 * Component used for handling checkout dialog actions
 */
"use strict";
/* global app, trans, launchToast, selectedPlan, currentSubscription, getWebsiteFormattedAmount, updateButtonState, getTaxDescription */

$(function () {
    // Document ready

    $('.custom-control').on('change', function () {
        $('.error-message').hide();
    });

    $('#headingOne').on('click', function () {
        if ($('#headingOne').hasClass('collapsed')) {
            $('.card-header .label-icon').html('<ion-icon name="chevron-up-outline"></ion-icon>');
        } else {
            $('.card-header .label-icon').html('<ion-icon name="chevron-down-outline"></ion-icon>');
        }
    });

    checkout.initPayments();

    $('#checkout-center').on('hidden.bs.modal', function () {
        $(this).find('#billing-agreement-form').trigger('reset');
        $('.payment-error').addClass('d-none');
    });

    // Radio button
    $('.radio-group .radio').on('click', function () {
        $(this).parent().parent().find('.radio').removeClass('selected');
        $(this).addClass('selected');
        $('.payment-error').addClass('d-none');
        checkout.updatePaymentDescriptionBasedOnPaymentMethod();
    });

    $('.country-select').on('change', function () {
        checkout.updatePaymentSummaryData();
    });
});

/**
 * Checkout class
 */
var checkout = {
    allowedPaymentProcessors: ['stripe', 'paypal', 'coinbase', 'nowpayments', 'ccbill', 'paystack'],
    recurringPaymentProcessors: ['stripe', 'paypal', 'ccbill'],
    paymentData: {},
    selectedPlan: null,
    currentSubscription: null,
    monthlySubscriptionUpdateType: 'monthly-subscription-update',
    yearlySubscriptionUpdateType: 'yearly-subscription-update',
    oneMonthSubscriptionType: 'one-month-subscription',
    yearlySubscriptionType: 'yearly-subscription',

    /**
     * Initiates the payment data payload
     */
    initiatePaymentData: function (type, amount, firstName, lastName, billingAddress, country, city, state, postcode, jobId, planId) {
        checkout.paymentData = {
            type: type,
            amount: amount,
            firstName: firstName,
            lastName: lastName,
            billingAddress: billingAddress,
            country: country,
            city: city,
            state: state,
            postcode: postcode,
            jobId: jobId,
            planId: planId
        };
    },

    initPayments: function(){
        //get data-id attribute of the clicked element
        const checkoutDataElement = $('.checkout-info');

        var amount = checkoutDataElement.attr('data-amount');
        var type = checkoutDataElement.attr('data-type');
        var firstName = checkoutDataElement.attr('data-first-name');
        var lastName = checkoutDataElement.attr('data-last-name');
        var billingAddress = checkoutDataElement.attr('data-billing-address');
        var name = checkoutDataElement.attr('data-name');
        var country = checkoutDataElement.attr('data-country');
        var city = checkoutDataElement.attr('data-city');
        var state = checkoutDataElement.attr('data-state');
        var postcode = checkoutDataElement.attr('data-postcode');
        var jobId = checkoutDataElement.attr('data-job-id');
        var planId = null;

        if(typeof selectedPlan !== 'undefined' && selectedPlan) {
            planId = selectedPlan.id;
            amount = selectedPlan.price;
            name = selectedPlan.name;
            checkout.selectedPlan = selectedPlan;
        }

        if(typeof currentSubscription !== 'undefined') {
            checkout.currentSubscription = currentSubscription;
            if(currentSubscription.active) {
                type = checkout.monthlySubscriptionUpdateType;
            }
        }

        checkout.initiatePaymentData(
            type,
            amount,
            firstName,
            lastName,
            billingAddress,
            country,
            city,
            state,
            postcode,
            jobId,
            planId
        );

        checkout.fillCountrySelectOptions();
        checkout.updatePaymentSummaryData();
        checkout.prefillBillingDetails();
        checkout.updatePaymentProviders(type);

        if (name && name !== '') {
            $('.payment-description').removeClass('d-none');
            $('.payment-description').text(trans(name));
        }

        $('#checkout-amount').val(amount);
    },


    /**
     * Updates the payment form
     */
    updatePaymentForm: function () {
        $('#payment-type').val(checkout.paymentData.type);
        $('#job').val(checkout.paymentData.jobId);
        $('#plan').val(checkout.paymentData.planId);
        $('#provider').val(checkout.paymentData.provider);
        $('#paymentFirstName').val(checkout.paymentData.firstName);
        $('#paymentLastName').val(checkout.paymentData.lastName);
        $('#paymentBillingAddress').val(checkout.paymentData.billingAddress);
        $('#paymentCountry').val(checkout.paymentData.country);
        $('#paymentState').val(checkout.paymentData.state);
        $('#paymentPostcode').val(checkout.paymentData.postcode);
        $('#paymentCity').val(checkout.paymentData.city);
        $('#payment-deposit-amount').val(checkout.paymentData.totalAmount);
        $('#paymentTaxes').val(JSON.stringify(checkout.paymentData.taxes));
    },

    stripe: null,

    /**
     * Instantiates the payment session
     * And starts the payment flow, if validation passes
     */
    initPayment: function () {
        const processor = checkout.getSelectedPaymentMethod();
        if(checkout.validatePreCheckout(processor)){
            checkout.updatePaymentForm();
            checkout.validateAllFields(()=>{
                checkout.startCheckoutProcess(processor);
                $('#update-plan').modal('hide');
                return true;
            });

        } else {
            this.hideUpdatePlanDialog();
        }
        return false;
    },

    /**
     * UI Validation for the checkout required fields
     * @param processor
     * @returns {boolean}
     */
    validatePreCheckout: function(processor){
        if (!processor && !this.allowFreeTrialPaymentForPlan() && checkout.selectedPlan.price > 0) {
            $('.payment-error').removeClass('d-none');
            return false;
        }
        return true;
    },

    /**
     * Hides all validation errors
     * Updates payment payload
     * Starts the payment flow
     * @param processor
     */
    // eslint-disable-next-line no-unused-vars
    startCheckoutProcess: function(processor){
        $('.paymentProcessorError').hide();
        $('.error-message').hide();
        updateButtonState('loading', $('.checkout-continue-btn'), trans('Pay'), 'white');

        $('.payment-button').trigger('click');
    },

    /**
     * Runs backend validation check for billing data
     * @param callback
     */
    validateAllFields: function(callback){
        checkout.clearFormErrors();
        updateButtonState('loading', $('.checkout-continue-btn'), trans('Pay'), 'white');

        $.ajax({
            type: 'POST',
            data: $('#pp-buyItem').serialize(),
            url: app.baseUrl + '/payment/initiate/validate',
            success: function () {
                updateButtonState('loaded', $('.checkout-continue-btn'), trans('Pay'), 'white');

                callback();
            },
            error: function (result) {
                updateButtonState('loaded', $('.checkout-continue-btn'), trans('Pay'), 'white');

                if(result.status === 500){
                    launchToast('danger',trans('Error'),result.responseJSON.message);
                }
                $.each(result.responseJSON.errors,function (field,error) {
                    let fieldElement = $('.uifield-'+field);
                    fieldElement.addClass('is-invalid');
                    fieldElement.parent().append(
                        `
                            <span class="invalid-feedback" role="alert">
                                <strong>${error}</strong>
                            </span>
                        `
                    );
                });
            }
        });
    },


    /**
     * Clears up dialog (all) form errors
     */
    clearFormErrors: function () {
        // Clearing up prev form errors
        $('.invalid-feedback').remove();
        $('input').removeClass('is-invalid');
    },

    /**
     * Returns currently selected payment method
     */
    getSelectedPaymentMethod: function () {
        const paypalProvider = $('.paypal-payment-provider').hasClass('selected');
        const stripeProvider = $('.stripe-payment-provider').hasClass('selected');
        const creditProvider = $('.credit-payment-provider').hasClass('selected');
        const coinbaseProvider = $('.coinbase-payment-provider').hasClass('selected');
        const nowPaymentsProvider = $('.nowpayments-payment-provider').hasClass('selected');
        const ccbillProvider = $('.ccbill-payment-provider').hasClass('selected');
        const paystackProvider = $('.paystack-payment-provider').hasClass('selected');
        let val = null;
        if (paypalProvider) {
            val = 'paypal';
        } else if (stripeProvider) {
            val = 'stripe';
        } else if (creditProvider) {
            val = 'credit';
        } else if(coinbaseProvider){
            val = 'coinbase';
        } else if(nowPaymentsProvider){
            val = 'nowpayments';
        } else if(ccbillProvider){
            val = 'ccbill';
        } else if(paystackProvider){
            val = 'paystack';
        }
        if (val) {
            checkout.paymentData.provider = val;
            return val;
        }
        return false;
    },

    /**
     * Validates the amount field
     * @returns {boolean}
     */
    checkoutAmountValidation: function () {
        // eslint-disable-next-line no-unused-vars
        const checkoutAmount = $('#checkout-amount').val();
        // Apply a value validation here if needed
        // Example of invalid trigger
        // $('#checkout-amount').addClass('is-invalid');
        //                 return false;

        return true;
    },

    /**
     * Validates FN field
     */
    validateFirstNameField: function () {
        let firstNameField = $('input[name="firstName"]');
        checkout.paymentData.firstName = firstNameField.val();
    },

    /**
     * Validates LN field
     */
    validateLastNameField: function () {
        let lastNameField = $('input[name="lastName"]');
        checkout.paymentData.lastName = lastNameField.val();
    },

    /**
     * Validates Adress field
     */
    validateBillingAddressField: function () {
        let billingAddressField = $('textarea[name="billingAddress"]');
        checkout.paymentData.billingAddress = billingAddressField.val();
    },

    /**
     * Validates city field
     */
    validateCityField: function () {
        let cityField = $('input[name="billingCity"]');
        checkout.paymentData.city = cityField.val();
    },

    /**
     * Validates state field
     */
    validateStateField: function () {
        let stateField = $('input[name="billingState"]');
        checkout.paymentData.state = stateField.val();
    },

    /**
     * Validates the ZIP code
     */
    validatePostcodeField: function () {
        let postcodeField = $('input[name="billingPostcode"]');
        checkout.paymentData.postcode = postcodeField.val();
    },

    /**
     * Validates the country field
     */
    validateCountryField: function () {
        let countryField = $('.country-select');
        let countryValidation = $('.country-select').find(':selected').val().length;
        let selectedCountry = $('.country-select').find(':selected');
        if (countryValidation) {
            countryField.removeClass('is-invalid');
            checkout.paymentData.country = selectedCountry.text();
        }
        else{
            checkout.paymentData.country = '';
        }
    },

    /**
     * Prefills user billing data, if available
     */
    prefillBillingDetails: function () {
        $('input[name="firstName"]').val(checkout.paymentData.firstName);
        $('input[name="lastName"]').val(checkout.paymentData.lastName);
        $('input[name="billingCity"]').val(checkout.paymentData.city);
        $('input[name="billingState"]').val(checkout.paymentData.state);
        $('input[name="billingPostcode"]').val(checkout.paymentData.postcode);
        $('textarea[name="billingAddress"]').val(checkout.paymentData.billingAddress);
    },

    /**
     * Fetches list of countries, in order to calculcate taxes
     */
    fillCountrySelectOptions: function () {
        $.ajax({
            type: 'GET',
            url: app.baseUrl + '/countries',
            success: function (result) {
                if (result !== null && typeof result.countries !== 'undefined' && result.countries.length > 0) {
                    $('.country-select').find('option').remove().end().append('<option value="">'+trans("Select a country")+'</option>');
                    $.each(result.countries, function (i, item) {
                        let selected = checkout.paymentData.country !== null && checkout.paymentData.country === item.name;
                        $('.country-select').append($('<option>', {
                            value: item.id,
                            text: item.name,
                            selected: selected
                        }).data({taxes: item.taxes}));
                        if (selected) {
                            checkout.updatePaymentSummaryData();
                        }
                    });
                }
            }
        });
    },

    /**
     * Updates payment summary data, taxes included
     */
    updatePaymentSummaryData: function () {
        let subtotalAmount = typeof checkout.paymentData.amount !== 'undefined' ? parseFloat(checkout.paymentData.amount) : 0.00;
        let countryInclusiveTaxesPercentage = 0.00;
        let countryExclusiveTaxesPercentage = 0.00;
        let taxesAmount = 0.00;
        let totalAmount = subtotalAmount;
        let inclusiveTaxesAmount = 0.00;
        let exclusiveTaxesAmount = 0.00;
        let fixedTaxesAmount = 0.00;
        checkout.paymentData.totalAmount = subtotalAmount;
        let taxes = [];

        // calculate taxes by country
        $('.taxes-details').html("");
        let selectedCountry = $('.country-select').find(':selected');
        if (selectedCountry !== null && selectedCountry.val() > 0) {
            let countryTaxes = selectedCountry.data('taxes');
            if (countryTaxes !== null) {
                if (countryTaxes.length > 0) {
                    for (let i = 0; i < countryTaxes.length; i++) {
                        let countryTaxPercentage = countryTaxes[i].percentage;
                        if (countryTaxPercentage !== null && countryTaxPercentage > 0) {
                            let countryTaxAmount = 0.00;
                            if (countryTaxes[i].type === 'exclusive') {
                                countryExclusiveTaxesPercentage += parseFloat(countryTaxes[i].percentage);
                                taxes.push({
                                    countryTaxName: countryTaxes[i].name,
                                    type: 'exclusive',
                                    countryTaxPercentage: parseFloat(countryTaxes[i].percentage),
                                    hidden: countryTaxes[i].hidden
                                });
                            }

                            if (countryTaxes[i].type === 'inclusive') {
                                countryInclusiveTaxesPercentage += parseFloat(countryTaxes[i].percentage);
                                taxes.push({
                                    countryTaxAmount: countryTaxAmount,
                                    countryTaxName: countryTaxes[i].name,
                                    type: 'inclusive',
                                    countryTaxPercentage: parseFloat(countryTaxes[i].percentage),
                                    hidden: countryTaxes[i].hidden
                                });
                            }

                            if (countryTaxes[i].type === 'fixed') {
                                fixedTaxesAmount += parseFloat(countryTaxes[i].percentage);
                                taxes.push({
                                    countryTaxAmount: countryTaxes[i].percentage,
                                    countryTaxName: countryTaxes[i].name,
                                    type: 'fixed',
                                    hidden: countryTaxes[i].hidden
                                });
                            }
                        }
                    }
                }
            }
        }

        let formattedTaxes = {data: [], taxesTotalAmount: 0.00, subtotal: subtotalAmount.toFixed(2)};

        if (subtotalAmount > 0 && !this.allowFreeTrialPaymentForPlan()) {
            for (let j = 0; j < taxes.length; j++) {
                let taxAmount = 0.00;
                let inclusiveTaxesAmount = subtotalAmount - subtotalAmount / (1 + countryInclusiveTaxesPercentage.toFixed(2) / 100);
                if (taxes[j].type === 'inclusive') {
                    let countryTaxAmount = subtotalAmount - subtotalAmount / (1 + taxes[j].countryTaxPercentage.toFixed(2) / 100);
                    let remainingFees = inclusiveTaxesAmount - countryTaxAmount;
                    let amountWithoutRemainingFees = subtotalAmount - remainingFees;
                    taxAmount = amountWithoutRemainingFees - amountWithoutRemainingFees / (1 + taxes[j].countryTaxPercentage.toFixed(2) / 100);

                    try {
                        taxAmount = taxAmount.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
                    } catch (e) {
                        taxAmount = taxAmount.toFixed(2);
                    }
                }

                if (taxes[j].type === 'exclusive') {
                    let amountWithInclusiveFeesDeducted = subtotalAmount - inclusiveTaxesAmount;
                    taxAmount = (taxes[j].countryTaxPercentage.toFixed(2) / 100) * amountWithInclusiveFeesDeducted;
                    taxAmount = taxAmount.toFixed(2);
                }

                if (taxes[j].type === 'fixed') {
                    taxAmount = taxes[j].countryTaxAmount;
                }

                formattedTaxes.data.push({
                    taxName: taxes[j].countryTaxName,
                    taxAmount: taxAmount,
                    taxPercentage: taxes[j].countryTaxPercentage,
                    taxType: taxes[j].type
                });

                if(!taxes[j].hidden) {
                    let item = "<div class=\"row ml-2\">\n" +
                        "<span class=\"col-sm left\">" + getTaxDescription(taxes[j].countryTaxName, taxes[j].countryTaxPercentage, taxes[j].type) + "</span>\n" +
                        "<span class=\"country-tax col-sm right text-right\">\n" +
                        "    <b>" + getWebsiteFormattedAmount(taxAmount) + "</b>\n" +
                        "</span>\n" +
                        "</div>";
                    $('.taxes-details').append(item);
                }
            }

            let subtotal = subtotalAmount;
            if (countryInclusiveTaxesPercentage > 0) {
                inclusiveTaxesAmount = subtotalAmount - subtotalAmount / (1 + countryInclusiveTaxesPercentage.toFixed(2) / 100);
                if (inclusiveTaxesAmount > 0) {
                    subtotal = subtotalAmount - inclusiveTaxesAmount;
                }
            }

            if (countryExclusiveTaxesPercentage > 0) {
                exclusiveTaxesAmount = (countryExclusiveTaxesPercentage.toFixed(2) / 100) * subtotal;
                totalAmount = totalAmount + exclusiveTaxesAmount;
            }

            if (fixedTaxesAmount > 0) {
                totalAmount += fixedTaxesAmount;
            }

            if (formattedTaxes.data && formattedTaxes.data.length > 0) {
                for (let i = 0; i < formattedTaxes.data.length; i++) {
                    if (formattedTaxes.data[i]['taxAmount'] !== undefined) {
                        taxesAmount = taxesAmount + parseFloat(formattedTaxes.data[i]['taxAmount']);
                    }
                }
            }
            formattedTaxes.taxesTotalAmount = taxesAmount.toFixed(2);
            checkout.paymentData.totalAmount = totalAmount.toFixed(2);
        }

        checkout.paymentData.taxes = formattedTaxes;

        $('.subtotal-amount b').html(
            this.allowFreeTrialPaymentForPlan() ? trans('FREE') : getWebsiteFormattedAmount(subtotalAmount.toFixed(2))
        );
        $('.total-amount b').html(
            this.allowFreeTrialPaymentForPlan() ? trans('FREE') : getWebsiteFormattedAmount(totalAmount.toFixed(2))
        );
    },

    togglePaymentProvider: function(toggle, paymentMethodClass){
        let paymentMethod = $(paymentMethodClass);
        if(toggle){
            if(paymentMethod.hasClass('d-none')){
                paymentMethod.removeClass('d-none');
            }
        } else {
            if(!paymentMethod.hasClass('d-none')){
                paymentMethod.addClass('d-none');
            }
        }

    },

    switchSubscriptionPeriod: function($noOfMonths = 1) {
        const isYearlySub = $noOfMonths === 12;
        checkout.paymentData.amount = isYearlySub ? selectedPlan.yearly_price : selectedPlan.price;
        if(checkout.currentSubscription && checkout.currentSubscription.active) {
            checkout.paymentData.type = isYearlySub
                ? checkout.yearlySubscriptionUpdateType
                : checkout.monthlySubscriptionUpdateType;
        }
        this.updatePaymentSummaryData();
        this.updatePaymentForm();
        this.updatePaymentProviders(checkout.paymentData.type);
    },

    showUpdatePlanDialog: function(){
        $('#update-plan').modal('show');
    },

    hideUpdatePlanDialog: function(){
        $('#update-plan').modal('hide');
    },

    /**
     * Update payment providers based on settings and payment type
     * @param type
     */
    updatePaymentProviders: function(type) {
        // payment providers are hidden by default to cover free plans
        if(checkout.paymentData.amount > 0 && !this.allowFreeTrialPaymentForPlan()) {
            $('.payment-method').removeClass('d-none');
            $('.payment-method-description').removeClass('d-none');
        }

        let showStripeProvider = !app.stripeRecurringDisabled;
        let showPaypalProvider = !app.paypalRecurringDisabled;
        let showCCBillProvider = !app.ccBillRecurringDisabled;

        // handles ccbill provider as they only allow 30 or 90 days subscriptions
        if (showCCBillProvider
            && (type === this.yearlySubscriptionType || type === this.yearlySubscriptionUpdateType)) {
            showCCBillProvider = false;
        }

        checkout.togglePaymentProvider(showCCBillProvider, '.ccbill-payment-method');
        checkout.togglePaymentProvider(showStripeProvider, '.stripe-payment-method');
        checkout.togglePaymentProvider(showPaypalProvider, '.paypal-payment-method');
    },

    /**
     * Updates payment description based on payment provider
     */
    updatePaymentDescriptionBasedOnPaymentMethod: function() {
        const selectedPaymentMethod = checkout.getSelectedPaymentMethod();
        if(checkout.recurringPaymentProcessors.includes(selectedPaymentMethod)) {
            $('.payment-method-description').html(
                trans('Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be charged on a recurring basis. You can cancel your subscription at any time.')
            );
        } else {
            $('.payment-method-description').html(
                trans('Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be redirected back to the website.')
            );
        }
    },

    /**
     * Checks if we allow free trial payment for this user and plan
     * @returns {boolean}
     */
    allowFreeTrialPaymentForPlan: function() {
        return checkout.selectedPlan.trial_days > 0 && !checkout.selectedPlan.has_payment_for_plan;
    }
};
