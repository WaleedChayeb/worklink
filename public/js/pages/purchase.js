/**
 * Purchase job listing page component
 */
"use strict";
/* global checkout, CreateHelper, app, JobCreate, trans, redirect, launchToast */

$(function () {

    Purchase.initPurchseSubmit();

});
/**
 * Jobs create class
 */
var Purchase = {

    /**
     * Inits purchase submit action
     */
    initPurchseSubmit: function () {
        $('.checkout-continue-btn').on('click', function (e) {
            // e.preventDefault();
            const processor = checkout.getSelectedPaymentMethod();
            if(checkout.validatePreCheckout(processor)){
                // show update plan dialog to warn user about his current plan cancellation
                if(checkout.paymentData.type === checkout.monthlySubscriptionUpdateType
                    || checkout.paymentData.type === checkout.yearlySubscriptionUpdateType){
                    e.preventDefault();
                    checkout.showUpdatePlanDialog();

                    return true;
                }

                checkout.updatePaymentForm();
                checkout.validateAllFields(()=>{
                    let jobData = CreateHelper.populateDraftData();
                    const jobIDToUpdate = $('.checkout-info').attr('data-job-id');
                    if(jobIDToUpdate === 'null'){
                        $.ajax({
                            type: 'POST',
                            data: jobData,
                            url: app.baseUrl + '/jobs/save/job',
                            success: function (result) {
                                CreateHelper.clearDraftData(Purchase.startPaymentProcessFlow(processor, result.id));
                            },
                            error: function (result) {
                                launchToast('danger', trans('Error'), result.responseJSON.message);
                            }
                        });
                    }
                    else{
                        Purchase.startPaymentProcessFlow(processor, jobIDToUpdate);
                    }
                    return true;
                });
            }
            return false;
        });

        $('.pay-later').on('click', function (e) {
            e.preventDefault();
            let jobData = CreateHelper.populateDraftData();
            $.ajax({
                type: 'POST',
                data: jobData,
                url: app.baseUrl + '/jobs/save/job',
                success: function (result) {
                    CreateHelper.clearDraftData(redirect(result.redirect));
                },
                error: function (result) {
                    launchToast('danger', trans('Error'), result.responseJSON.message);
                }
            });
        });
    },

    /**
     * Inits the payment module based on jobID
     * @param processor
     * @param jobID
     */
    startPaymentProcessFlow: function (processor, jobID = null) {
        // Updating some payment info on the fly
        $('.checkout-info').attr('data-job-id', jobID);
        $('#job').val(jobID);

        if(JobCreate.debugJobCreation){
            if (confirm("[DEBUG] Continue? \r\n* Ok = Continue with redirect \r\n* Cancel = Stay on page, but the call is made.") === true) {
                checkout.startCheckoutProcess(processor);
            }
        }
        else{
            checkout.startCheckoutProcess(processor);
        }
    }

};
